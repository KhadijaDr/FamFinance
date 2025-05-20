<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Budgets') }}
            </h2>
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-budget-modal')">
                {{ __('Ajouter un budget') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Vue d'ensemble des budgets -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Vue d\'ensemble des budgets') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">{{ __('Budget total') }}</h4>
                            <p class="text-2xl font-bold text-blue-700">{{ number_format($totalBudget, 2, ',', ' ') }} MAD</p>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <h4 class="text-sm font-medium text-green-800 mb-2">{{ __('Dépenses totales') }}</h4>
                            <p class="text-2xl font-bold text-green-700">{{ number_format($totalSpent, 2, ',', ' ') }} MAD</p>
                        </div>
                        
                        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                            <h4 class="text-sm font-medium text-indigo-800 mb-2">{{ __('Reste disponible') }}</h4>
                            <p class="text-2xl font-bold text-indigo-700">{{ number_format($totalBudget - $totalSpent, 2, ',', ' ') }} MAD</p>
                        </div>
                    </div>
                    
                    <!-- Graphique de progression des budgets -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Progression des budgets par catégorie') }}</h4>
                        <div class="space-y-4">
                            @foreach($budgets as $budget)
                                @php
                                    $spent = $budget->spent ?? 0;
                                    $percentage = $budget->amount > 0 ? min(100, ($spent / $budget->amount) * 100) : 0;
                                    $colorClass = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-green-500');
                                @endphp
                                <div>
                                    <div class="flex justify-between mb-1 text-sm">
                                        <span class="font-medium">{{ $budget->name }}</span>
                                        <span>{{ number_format($spent, 2, ',', ' ') }} MAD / {{ number_format($budget->amount, 2, ',', ' ') }} MAD</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="{{ $colorClass }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des budgets -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtres -->
                    <div class="mb-6 bg-gray-100 p-4 rounded-lg">
                        <form method="GET" action="{{ route('budgets.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="period" :value="__('Période')" />
                                <select id="period" name="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Toutes') }}</option>
                                    <option value="monthly" {{ request('period') === 'monthly' ? 'selected' : '' }}>{{ __('Mensuel') }}</option>
                                    <option value="yearly" {{ request('period') === 'yearly' ? 'selected' : '' }}>{{ __('Annuel') }}</option>
                                    <option value="weekly" {{ request('period') === 'weekly' ? 'selected' : '' }}>{{ __('Hebdomadaire') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="category_id" :value="__('Catégorie')" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Toutes') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <x-primary-button>
                                    {{ __('Filtrer') }}
                                </x-primary-button>
                                
                                <a href="{{ route('budgets.index') }}" class="inline-flex items-center ml-4 px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Réinitialiser') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Tableau des budgets -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Nom') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Catégorie') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Période') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Montant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Dates') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Progression') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($budgets as $budget)
                                    @php
                                        $spent = $budget->spent ?? 0;
                                        $percentage = $budget->amount > 0 ? min(100, ($spent / $budget->amount) * 100) : 0;
                                        $barColorClass = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-green-500');
                                        $textColorClass = $percentage > 90 ? 'text-red-600' : ($percentage > 70 ? 'text-yellow-600' : 'text-green-600');
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $budget->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($budget->category)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                      style="background-color: {{ $budget->category->color ?? '#E5E7EB' }}; color: {{ $budget->category->color ? '#FFFFFF' : '#374151' }}">
                                                    {{ $budget->category->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">{{ __('Non catégorisé') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($budget->period === 'monthly')
                                                {{ __('Mensuel') }}
                                            @elseif($budget->period === 'yearly')
                                                {{ __('Annuel') }}
                                            @elseif($budget->period === 'weekly')
                                                {{ __('Hebdomadaire') }}
                                            @else
                                                {{ $budget->period }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                            {{ number_format($budget->amount, 2, ',', ' ') }} MAD
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $budget->start_date->format('d/m/Y') }}
                                            @if($budget->end_date)
                                                - {{ $budget->end_date->format('d/m/Y') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div>
                                                <div class="flex justify-between mb-1">
                                                    <span class="{{ $textColorClass }} font-medium">{{ round($percentage) }}%</span>
                                                    <span class="text-sm">{{ number_format($spent, 2, ',', ' ') }} MAD</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="{{ $barColorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-budget-{{ $budget->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                {{ __('Modifier') }}
                                            </button>
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-budget-{{ $budget->id }}')" class="text-red-600 hover:text-red-900">
                                                {{ __('Supprimer') }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('Aucun budget trouvé.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $budgets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter un budget -->
    <x-modal name="add-budget-modal" :show="false" focusable>
        <form method="POST" action="{{ route('budgets.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ajouter un nouveau budget') }}
            </h2>
            
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="name" :value="__('Nom')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="amount" :value="__('Montant')" />
                    <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('amount')" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="category_id" :value="__('Catégorie')" />
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Sélectionner une catégorie') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="period" :value="__('Période')" />
                    <select id="period" name="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="monthly">{{ __('Mensuel') }}</option>
                        <option value="yearly">{{ __('Annuel') }}</option>
                        <option value="weekly">{{ __('Hebdomadaire') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('period')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="start_date" :value="__('Date de début')" />
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="end_date" :value="__('Date de fin (optionnel)')" />
                    <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date')" />
                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                </div>
                
                <div class="md:col-span-2">
                    <x-input-label for="notes" :value="__('Notes (optionnel)')" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Annuler') }}
                </x-secondary-button>
                
                <x-primary-button class="ml-3">
                    {{ __('Enregistrer') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    
    <!-- Modals pour éditer/supprimer les budgets (générés dynamiquement) -->
    @foreach($budgets as $budget)
        <!-- Modal pour éditer le budget -->
        <x-modal name="edit-budget-{{ $budget->id }}" :show="false" focusable>
            <form method="POST" action="{{ route('budgets.update', $budget) }}" class="p-6">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Modifier le budget') }}
                </h2>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="edit_name_{{ $budget->id }}" :value="__('Nom')" />
                        <x-text-input id="edit_name_{{ $budget->id }}" name="name" type="text" class="mt-1 block w-full" :value="$budget->name" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_amount_{{ $budget->id }}" :value="__('Montant')" />
                        <x-text-input id="edit_amount_{{ $budget->id }}" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="$budget->amount" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_category_id_{{ $budget->id }}" :value="__('Catégorie')" />
                        <select id="edit_category_id_{{ $budget->id }}" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Sélectionner une catégorie') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $budget->category_id === $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <x-input-label for="edit_period_{{ $budget->id }}" :value="__('Période')" />
                        <select id="edit_period_{{ $budget->id }}" name="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="monthly" {{ $budget->period === 'monthly' ? 'selected' : '' }}>{{ __('Mensuel') }}</option>
                            <option value="yearly" {{ $budget->period === 'yearly' ? 'selected' : '' }}>{{ __('Annuel') }}</option>
                            <option value="weekly" {{ $budget->period === 'weekly' ? 'selected' : '' }}>{{ __('Hebdomadaire') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <x-input-label for="edit_start_date_{{ $budget->id }}" :value="__('Date de début')" />
                        <x-text-input id="edit_start_date_{{ $budget->id }}" name="start_date" type="date" class="mt-1 block w-full" :value="$budget->start_date->format('Y-m-d')" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_end_date_{{ $budget->id }}" :value="__('Date de fin (optionnel)')" />
                        <x-text-input id="edit_end_date_{{ $budget->id }}" name="end_date" type="date" class="mt-1 block w-full" :value="$budget->end_date ? $budget->end_date->format('Y-m-d') : ''" />
                    </div>
                    
                    <div class="md:col-span-2">
                        <x-input-label for="edit_notes_{{ $budget->id }}" :value="__('Notes (optionnel)')" />
                        <textarea id="edit_notes_{{ $budget->id }}" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $budget->notes }}</textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Annuler') }}
                    </x-secondary-button>
                    
                    <x-primary-button class="ml-3">
                        {{ __('Mettre à jour') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
        
        <!-- Modal pour supprimer le budget -->
        <x-modal name="delete-budget-{{ $budget->id }}" :show="false" focusable>
            <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="p-6">
                @csrf
                @method('DELETE')
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Supprimer le budget') }}
                </h2>
                
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Êtes-vous sûr de vouloir supprimer ce budget ? Cette action est irréversible.') }}
                </p>
                
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Annuler') }}
                    </x-secondary-button>
                    
                    <x-danger-button class="ml-3">
                        {{ __('Supprimer') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout> 