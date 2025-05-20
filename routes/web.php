<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialGoalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transactions
    Route::resource('transactions', TransactionController::class);
    Route::get('/transactions/reports', [TransactionController::class, 'reports'])->name('transactions.reports');
    
    // Catégories
    Route::get('/default-categories', [CategoryController::class, 'initializeDefault'])->name('categories.initialize-default');
    Route::resource('categories', CategoryController::class);
    
    // Budgets
    Route::resource('budgets', BudgetController::class);
    Route::get('/budgets/performance', [BudgetController::class, 'performance'])->name('budgets.performance');
    
    // Factures
    Route::resource('bills', BillController::class);
    Route::get('/bills/upcoming', [BillController::class, 'upcoming'])->name('bills.upcoming');
    
    // Objectifs financiers
    Route::resource('financial-goals', FinancialGoalController::class);
    Route::get('/financial-goals/progress', [FinancialGoalController::class, 'progress'])->name('financial-goals.progress');
    Route::post('/financial-goals/{financialGoal}/contribution', [FinancialGoalController::class, 'addContribution'])->name('financial-goals.addContribution');
    
    // Notifications
    Route::resource('notifications', NotificationController::class);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
