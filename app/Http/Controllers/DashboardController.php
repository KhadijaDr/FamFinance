<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Bill;
use App\Models\FinancialGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal avec les données financières.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Récupérer les transactions récentes
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();
        
        // Calculer le solde actuel
        $incomeTotal = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->sum('amount');
        
        $expenseTotal = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->sum('amount');
        
        $currentBalance = $incomeTotal - $expenseTotal;
        
        // Récupérer les transactions du mois courant
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $monthlyIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        $monthlyExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        // Récupérer les factures à venir
        $upcomingBills = Bill::where('user_id', $user->id)
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->where('status', '!=', 'paid')
            ->orderBy('due_date')
            ->get();
        
        // Récupérer les objectifs financiers en cours
        $financialGoals = FinancialGoal::where('user_id', $user->id)
            ->where('is_completed', false)
            ->orderBy('target_date')
            ->get();
        
        // Récupérer les données de budget pour le mois en cours
        $budgets = Budget::where('user_id', $user->id)
            ->where('is_active', true)
            ->with('category')
            ->get();
        
        // Préparer les données pour les graphiques
        $categoryExpenses = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->with('category')
            ->get()
            ->groupBy(function($transaction) {
                return $transaction->category ? $transaction->category->name : 'Non catégorisé';
            })
            ->map(function ($items) {
                return $items->sum('amount');
            });
        
        return view('dashboard', compact(
            'recentTransactions',
            'currentBalance',
            'monthlyIncome',
            'monthlyExpense',
            'upcomingBills',
            'financialGoals',
            'budgets',
            'categoryExpenses'
        ));
    }
}
