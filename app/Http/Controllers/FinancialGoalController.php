<?php

namespace App\Http\Controllers;

use App\Models\FinancialGoal;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FinancialGoalController extends Controller
{
    use AuthorizesRequests;

    /**
     * Affiche la liste des objectifs financiers.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Récupérer les objectifs avec filtrage par statut
        $query = FinancialGoal::where('user_id', $user->id);
        
        if (request('status') === 'active') {
            $query->where('is_completed', false);
        } elseif (request('status') === 'completed') {
            $query->where('is_completed', true);
        }
        
        // Récupérer les objectifs actifs et terminés séparément pour les statistiques
        $activeGoals = $query->clone()->where('is_completed', false)->get();
        $completedGoals = $query->clone()->where('is_completed', true)->get();
        
        // Statistiques
        $activeGoalsCount = $activeGoals->count();
        $completedGoalsCount = $completedGoals->count();
        $totalSaved = $activeGoals->sum('current_amount') + $completedGoals->sum('current_amount');
        
        // Pagination des résultats
        $goals = $query->orderBy('created_at', 'desc')->paginate(9);
        
        // Calculer les informations dynamiques pour chaque objectif
        foreach ($goals as $goal) {
            $goal->remaining_time = $this->calculateRemainingTime($goal->target_date);
        }
        
        return view('financial-goals.index', compact(
            'goals',
            'activeGoalsCount',
            'completedGoalsCount',
            'totalSaved'
        ));
    }
    
    /**
     * Affiche le formulaire de création d'un objectif.
     */
    public function create(): View
    {
        return view('financial-goals.create');
    }
    
    /**
     * Enregistre un nouvel objectif.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'target_date' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        FinancialGoal::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount ?? 0,
            'start_date' => $request->start_date,
            'target_date' => $request->target_date,
            'priority' => $request->priority,
            'color' => $request->color,
            'icon' => $request->icon,
            'notes' => $request->notes,
            'is_completed' => false,
        ]);
        
        return redirect()->route('financial-goals.index')
            ->with('success', 'Objectif financier créé avec succès.');
    }
    
    /**
     * Affiche les détails d'un objectif.
     */
    public function show(FinancialGoal $financialGoal): View
    {
        $this->authorize('view', $financialGoal);
        
        $this->calculateProgress($financialGoal);
        
        // Récupérer les transactions associées à cet objectif
        $transactions = Transaction::where('user_id', $financialGoal->user_id)
            ->where('description', 'like', '%' . $financialGoal->name . '%')
            ->orderByDesc('transaction_date')
            ->get();
        
        return view('financial-goals.show', compact('financialGoal', 'transactions'));
    }
    
    /**
     * Affiche le formulaire de modification d'un objectif.
     */
    public function edit(FinancialGoal $financialGoal): View
    {
        $this->authorize('update', $financialGoal);
        
        return view('financial-goals.edit', compact('financialGoal'));
    }
    
    /**
     * Met à jour un objectif.
     */
    public function update(Request $request, FinancialGoal $financialGoal): RedirectResponse
    {
        $this->authorize('update', $financialGoal);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        // Vérifier si l'objectif est atteint
        $isCompleted = $request->current_amount >= $request->target_amount;
        
        $financialGoal->update([
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount,
            'target_date' => $request->target_date,
            'description' => $request->description,
            'is_completed' => $isCompleted,
        ]);
        
        return redirect()->route('financial-goals.index')
            ->with('success', 'Objectif financier mis à jour avec succès.');
    }
    
    /**
     * Supprime un objectif.
     */
    public function destroy(FinancialGoal $financialGoal): RedirectResponse
    {
        $this->authorize('delete', $financialGoal);
        
        $financialGoal->delete();
        
        return redirect()->route('financial-goals.index')
            ->with('success', 'Objectif financier supprimé avec succès.');
    }
    
    /**
     * Ajoute une contribution à un objectif financier.
     */
    public function addContribution(Request $request, FinancialGoal $financialGoal): RedirectResponse
    {
        $this->authorize('update', $financialGoal);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:255',
        ]);
        
        // Mettre à jour le montant actuel
        $newAmount = $financialGoal->current_amount + $request->amount;
        $isCompleted = $newAmount >= $financialGoal->target_amount;
        
        $financialGoal->update([
            'current_amount' => $newAmount,
            'is_completed' => $isCompleted,
        ]);
        
        // Créer une transaction associée
        if ($request->has('create_transaction')) {
            Transaction::create([
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'type' => 'savings',
                'category' => 'Financial Goal',
                'description' => "Contribution à l'objectif : " . $financialGoal->name,
                'transaction_date' => $request->date,
                'notes' => $request->notes,
            ]);
        }
        
        return redirect()->route('financial-goals.show', $financialGoal)
            ->with('success', 'Contribution ajoutée avec succès.');
    }
    
    /**
     * Calcule la progression d'un objectif.
     */
    private function calculateProgress(FinancialGoal $goal): void
    {
        // Calculer le pourcentage de progression financière
        $goal->financial_progress = $goal->target_amount > 0 
            ? ($goal->current_amount / $goal->target_amount) * 100 
            : 0;
        
        // Calculer la progression temporelle
        $startDate = Carbon::parse($goal->created_at);
        $endDate = Carbon::parse($goal->target_date);
        $now = Carbon::now();
        
        $totalDays = $startDate->diffInDays($endDate);
        $daysElapsed = $startDate->diffInDays($now);
        
        $goal->time_progress = $totalDays > 0 
            ? min(($daysElapsed / $totalDays) * 100, 100) 
            : 0;
        
        // Définir le statut
        if ($goal->is_completed) {
            $goal->status = 'completed';
        } elseif ($goal->target_date < Carbon::now()) {
            $goal->status = 'overdue';
        } elseif ($goal->financial_progress >= $goal->time_progress) {
            $goal->status = 'on-track';
        } else {
            $goal->status = 'behind';
        }
        
        // Jours restants
        $goal->days_remaining = $now->diffInDays($endDate, false);
        
        // Montant restant à atteindre
        $goal->remaining_amount = max($goal->target_amount - $goal->current_amount, 0);
    }

    /**
     * Calcule le temps restant en jours, heures et minutes.
     */
    private function calculateRemainingTime(string $targetDate): string
    {
        $targetCarbon = Carbon::parse($targetDate);
        $now = Carbon::now();
        $diff = $now->diff($targetCarbon);

        $parts = [];

        if ($diff->days > 0) {
            $parts[] = $diff->days . ' ' . ($diff->days > 1 ? __('jours') : __('jour'));
        }

        // Seulement ajouter heures/minutes si moins d'un jour restant ou si heures/minutes sont > 0
        if ($diff->days == 0 || $diff->h > 0) {
             if ($diff->h > 0) {
                $parts[] = $diff->h . ' ' . ($diff->h > 1 ? __('heures') : __('heure'));
            }
             if ($diff->i > 0) {
                $parts[] = $diff->i . ' ' . ($diff->i > 1 ? __('minutes') : __('minute'));
             }
        }

        $remaining = implode(' et ', array_filter([implode(', ', array_slice($parts, 0, -1)), last($parts)]));

        if ($diff->invert) { // Date in the past
            if ($diff->days < 1) {
                return __('Date limite dépassée de moins d\'un jour');
            } else {
                return __('Date limite dépassée de') . ' ' . $remaining;
            }
        } else { // Date in the future or today
            if ($diff->days < 1 && count($parts) == 0) {
                 return __('Date limite aujourd\'hui');
            } elseif (empty($remaining)){
                return __('Moins d\'une minute restante');
            }
            else {
                return __('Date limite dans') . ' ' . $remaining;
            }
        }
    }
} 