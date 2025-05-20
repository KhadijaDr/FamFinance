<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    /**
     * Affiche la liste des budgets.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Récupérer le mois et l'année en cours ou ceux spécifiés
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        
        // Calculer les dates de début et de fin du mois
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // Récupérer les budgets pour l'utilisateur et la période correspondante
        $budgets = Budget::where('user_id', $user->id)
            ->where('period', 'monthly')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                      ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                          $query->where('start_date', '<=', $startOfMonth)
                                ->where(function ($query) use ($endOfMonth) {
                                    $query->where('end_date', '>=', $endOfMonth)
                                          ->orWhereNull('end_date');
                                });
                      });
            })
            ->with('category')
            ->paginate(15)
            ->withQueryString();
        
        // Récupérer les dépenses réelles pour le mois
        $expenses = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('category_id')
            ->map(function ($items) {
                return $items->sum('amount');
            });
        
        // Calculer les pourcentages d'utilisation des budgets
        foreach ($budgets as $budget) {
            $spent = $expenses[$budget->category_id] ?? 0;
            $budget->spent = $spent;
            $budget->percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
            $budget->status = $budget->percentage > 100 ? 'danger' : ($budget->percentage > 80 ? 'warning' : 'success');
        }
        
        // Récupérer toutes les catégories pour l'utilisateur
        $categories = Category::where('user_id', $user->id)->get();
        
        // Calculer le total des budgets et des dépenses
        $totalBudget = $budgets->sum('amount');
        $totalSpent = array_sum($expenses->toArray());
        
        // Calculer les mois disponibles pour la navigation
        $availableMonths = Budget::where('user_id', $user->id)
            ->selectRaw('DISTINCT MONTH(start_date) as month, YEAR(start_date) as year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'year' => $item->year,
                    'label' => Carbon::createFromDate($item->year, $item->month, 1)->format('F Y')
                ];
            });
        
        return view('budgets.index', compact('budgets', 'categories', 'month', 'year', 'availableMonths', 'totalBudget', 'totalSpent'));
    }
    
    /**
     * Affiche le formulaire de création d'un budget.
     */
    public function create(): View
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        
        // Préparer les mois et années pour le selecteur
        $months = [];
        $currentMonth = Carbon::now()->month;
        
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::createFromDate(null, $i, 1)->format('F');
        }
        
        $currentYear = Carbon::now()->year;
        $years = range($currentYear, $currentYear + 5);
        
        return view('budgets.create', compact('categories', 'months', 'years', 'currentMonth', 'currentYear'));
    }
    
    /**
     * Enregistre un nouveau budget.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:monthly,yearly,weekly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        // Vérifier si un budget existe déjà pour cette catégorie et cette période
        $existingBudget = Budget::where('user_id', $user->id)
            ->where('category_id', $request->category_id)
            ->where('period', $request->period)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date ?? $request->start_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date ?? $request->start_date])
                      ->orWhere(function ($query) use ($request) {
                          $query->where('start_date', '<=', $request->start_date)
                                ->where(function ($query) use ($request) {
                                    $query->where('end_date', '>=', $request->start_date)
                                          ->orWhereNull('end_date');
                                });
                      });
            })
            ->first();
        
        if ($existingBudget) {
            return redirect()->back()->withErrors([
                'category_id' => 'Un budget existe déjà pour cette catégorie dans la période sélectionnée.'
            ])->withInput();
        }
        
        $budget = Budget::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'is_active' => true,
        ]);
        
        $startDate = Carbon::parse($budget->start_date);
        
        return redirect()->route('budgets.index', [
            'month' => $startDate->month,
            'year' => $startDate->year
        ])->with('success', 'Budget créé avec succès.');
    }
    
    /**
     * Affiche le formulaire de modification d'un budget.
     */
    public function edit(Budget $budget): View
    {
        $this->authorize('update', $budget);
        
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        
        // Préparer les mois et années pour le selecteur
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::createFromDate(null, $i, 1)->format('F');
        }
        
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 5);
        
        return view('budgets.edit', compact('budget', 'categories', 'months', 'years'));
    }
    
    /**
     * Met à jour un budget.
     */
    public function update(Request $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:monthly,yearly,weekly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier si un autre budget existe déjà pour cette catégorie et cette période
        $existingBudget = Budget::where('user_id', $budget->user_id)
            ->where('category_id', $request->category_id)
            ->where('period', $request->period)
            ->where('id', '!=', $budget->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date ?? $request->start_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date ?? $request->start_date])
                      ->orWhere(function ($query) use ($request) {
                          $query->where('start_date', '<=', $request->start_date)
                                ->where(function ($query) use ($request) {
                                    $query->where('end_date', '>=', $request->start_date)
                                          ->orWhereNull('end_date');
                                });
                      });
            })
            ->first();
        
        if ($existingBudget) {
            return redirect()->back()->withErrors([
                'category_id' => 'Un budget existe déjà pour cette catégorie dans la période sélectionnée.'
            ])->withInput();
        }
        
        $budget->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
        ]);
        
        $startDate = Carbon::parse($budget->start_date);
        
        return redirect()->route('budgets.index', [
            'month' => $startDate->month,
            'year' => $startDate->year
        ])->with('success', 'Budget mis à jour avec succès.');
    }
    
    /**
     * Supprime un budget.
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);
        
        $startDate = Carbon::parse($budget->start_date);
        $month = $startDate->month;
        $year = $startDate->year;
        
        $budget->delete();
        
        return redirect()->route('budgets.index', [
            'month' => $month,
            'year' => $year
        ])->with('success', 'Budget supprimé avec succès.');
    }
    
    /**
     * Affiche les performances budgétaires.
     */
    public function performance(): View
    {
        $user = Auth::user();
        
        // Récupérer les 12 derniers mois
        $months = [];
        $startMonth = Carbon::now()->subMonths(11);
        
        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $startMonth->copy()->addMonths($i);
            $months[] = [
                'month' => $currentMonth->month,
                'year' => $currentMonth->year,
                'label' => $currentMonth->format('M Y')
            ];
        }
        
        // Récupérer les données budgétaires et les dépenses réelles pour chaque mois
        $performanceData = [];
        
        foreach ($months as $monthData) {
            $month = $monthData['month'];
            $year = $monthData['year'];
            
            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            
            // Budgets pour ce mois
            $budgets = Budget::where('user_id', $user->id)
                ->where('period', 'monthly')
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                          ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                          ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                              $query->where('start_date', '<=', $startOfMonth)
                                    ->where(function ($query) use ($endOfMonth) {
                                        $query->where('end_date', '>=', $endOfMonth)
                                              ->orWhereNull('end_date');
                                    });
                          });
                })
                ->with('category')
                ->get();
            
            // Dépenses réelles pour ce mois
            $expenses = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->get()
                ->groupBy('category_id')
                ->map(function ($items) {
                    return $items->sum('amount');
                });
            
            // Calculer le total du budget et des dépenses
            $totalBudget = $budgets->sum('amount');
            $totalExpense = $expenses->sum();
            
            $performanceData[] = [
                'month' => $monthData['label'],
                'budget_total' => $totalBudget,
                'expense_total' => $totalExpense,
                'variance' => $totalBudget - $totalExpense,
                'variance_percentage' => $totalBudget > 0 ? (($totalBudget - $totalExpense) / $totalBudget) * 100 : 0,
                'details' => $budgets->map(function ($budget) use ($expenses) {
                    $spent = $expenses[$budget->category_id] ?? 0;
                    return [
                        'category' => $budget->category->name,
                        'budget' => $budget->amount,
                        'spent' => $spent,
                        'variance' => $budget->amount - $spent,
                        'percentage' => $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0,
                    ];
                })
            ];
        }
        
        return view('budgets.performance', compact('performanceData', 'months'));
    }
} 