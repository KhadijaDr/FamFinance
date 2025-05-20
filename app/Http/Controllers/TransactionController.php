<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Affiche la liste des transactions.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->with('category');
        
        // Filtrage par type
        if ($request->has('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
    }

        // Filtrage par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filtrage par date
        if ($request->has('date_from') && $request->date_from) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        
        // Tri
        $sortField = $request->sort_by ?? 'transaction_date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);
        
        $transactions = $query->paginate(15);
        $categories = Category::where('user_id', $user->id)->get();
        
        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Affiche le formulaire de création d'une transaction.
     */
    public function create(): View
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        
        return view('transactions.create', compact('categories'));
    }
    
    /**
     * Enregistre une nouvelle transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        Transaction::create([
            'user_id' => $user->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'transaction_date' => $request->date,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction ajoutée avec succès.');
    }
    
    /**
     * Affiche les détails d'une transaction.
     */
    public function show(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Affiche le formulaire de modification d'une transaction.
     */
    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);
        
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->get();
        
        return view('transactions.edit', compact('transaction', 'categories'));
    }
    
    /**
     * Met à jour une transaction.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);
        
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $transaction->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'transaction_date' => $request->date,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction mise à jour avec succès.');
    }
    
    /**
     * Supprime une transaction.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);
        
        $transaction->delete();
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction supprimée avec succès.');
    }
    
    /**
     * Affiche les rapports de transactions.
     */
    public function reports(Request $request): View
    {
        $user = Auth::user();
        $period = $request->period ?? 'month';
        
        // Déterminer les dates de début et de fin selon la période
        $now = Carbon::now();
        
        switch ($period) {
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
        }
        
        // Récupérer les transactions de la période
        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->with('category')
            ->get();
        
        // Calculer les totaux
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;
        
        // Grouper les dépenses par catégorie
        $expensesByCategory = $transactions
            ->where('type', 'expense')
            ->groupBy('category.name')
            ->map(function ($items) use ($totalExpense) {
                return [
                    'total' => $items->sum('amount'),
                    'percentage' => $totalExpense > 0 ? ($items->sum('amount') / $totalExpense) * 100 : 0,
                ];
            });
        
        // Données pour les graphiques de tendance
        $dailyData = $transactions
            ->groupBy(function ($item) {
                return Carbon::parse($item->transaction_date)->format('Y-m-d');
            })
            ->map(function ($items) {
                return [
                    'income' => $items->where('type', 'income')->sum('amount'),
                    'expense' => $items->where('type', 'expense')->sum('amount'),
                ];
            });
        
        return view('transactions.reports', compact(
            'transactions',
            'period',
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'expensesByCategory',
            'dailyData'
        ));
    }
}
