<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Résumé financier -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Solde actuel -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Solde actuel</p>
                            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($currentBalance, 2) }} MAD</h3>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Revenus mensuels -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Revenus ce mois</p>
                            <h3 class="text-2xl font-bold text-green-600">{{ number_format($monthlyIncome, 2) }} MAD</h3>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Dépenses mensuelles -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Dépenses ce mois</p>
                            <h3 class="text-2xl font-bold text-red-600">{{ number_format($monthlyExpense, 2) }} MAD</h3>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Graphique des dépenses par catégorie -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dépenses par catégorie</h3>
                    <div class="h-64">
                        <canvas id="expenses-chart"></canvas>
                    </div>
                </div>
                
                <!-- Suivi budgétaire -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Suivi budgétaire</h3>
                    @if($budgets->isEmpty())
                        <p class="text-gray-500 py-4">Vous n'avez pas encore de budgets définis.</p>
                        <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Créer un budget
                        </a>
                    @else
                        <div class="space-y-4">
                            @foreach($budgets as $budget)
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $budget->category->name }}
                                        </span>
                                        <span class="text-sm font-medium {{ $budget->status === 'danger' ? 'text-red-600' : ($budget->status === 'warning' ? 'text-yellow-600' : 'text-green-600') }}">
                                            {{ number_format($budget->spent, 2) }} MAD / {{ number_format($budget->amount, 2) }} MAD
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full {{ $budget->status === 'danger' ? 'bg-red-600' : ($budget->status === 'warning' ? 'bg-yellow-600' : 'bg-green-600') }}" style="width: {{ min($budget->percentage, 100) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Transactions récentes -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Transactions récentes</h3>
                        <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            Voir toutes
                        </a>
                    </div>
                    
                    @if($recentTransactions->isEmpty())
                        <p class="text-gray-500 py-4">Vous n'avez pas encore de transactions.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->transaction_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($transaction->category)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $transaction->category->color }}-100 text-{{ $transaction->category->color }}-800">
                                                        {{ $transaction->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">Non catégorisé</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                <span class="font-bold {{ $transaction->type === 'expense' ? 'text-red-600' : 'text-green-600' }}">{{ $transaction->type === 'expense' ? '-' : '+' }}{{ number_format($transaction->amount, 2) }} MAD</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Section latérale -->
                <div class="space-y-6">
                    <!-- Factures à venir -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Factures à venir</h3>
                            <a href="{{ route('bills.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                Voir toutes
                            </a>
                        </div>
                        
                        @if($upcomingBills->isEmpty())
                            <p class="text-gray-500 py-2">Aucune facture à payer prochainement.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($upcomingBills as $bill)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $bill->description }}</p>
                                            <p class="text-xs text-gray-500">Échéance: {{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</p>
                                        </div>
                                        <span class="text-sm font-bold text-red-600">{{ number_format($bill->amount, 2) }} MAD</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Objectifs financiers -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Objectifs financiers</h3>
                            <a href="{{ route('financial-goals.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                Voir tous
                            </a>
                        </div>
                        
                        @if($financialGoals->isEmpty())
                            <p class="text-gray-500 py-2">Vous n'avez pas encore défini d'objectifs financiers.</p>
                            <a href="{{ route('financial-goals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mt-2">
                                Créer un objectif
                            </a>
                        @else
                            <div class="space-y-4">
                                @foreach($financialGoals as $goal)
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $goal->name }}</span>
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ number_format($goal->current_amount, 2) }} MAD / {{ number_format($goal->target_amount, 2) }} MAD
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="h-2.5 rounded-full bg-blue-600" style="width: {{ min(($goal->current_amount / $goal->target_amount) * 100, 100) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration du graphique des dépenses par catégorie
            const expensesChart = document.getElementById('expenses-chart');
            
            if (expensesChart) {
                const categoryNames = {!! json_encode($categoryExpenses->keys()) !!};
                const categoryValues = {!! json_encode($categoryExpenses->values()) !!};
                
                const randomColors = categoryNames.map(() => {
                    const r = Math.floor(Math.random() * 255);
                    const g = Math.floor(Math.random() * 255);
                    const b = Math.floor(Math.random() * 255);
                    return `rgba(${r}, ${g}, ${b}, 0.7)`;
                });
                
                new Chart(expensesChart, {
                    type: 'doughnut',
                    data: {
                        labels: categoryNames,
                        datasets: [{
                            data: categoryValues,
                            backgroundColor: randomColors,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
