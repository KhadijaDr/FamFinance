<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Factures') }}
            </h2>
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-bill-modal')">
                {{ __('Ajouter une facture') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Vue d'ensemble des factures -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Vue d\'ensemble des factures') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                            <h4 class="text-sm font-medium text-indigo-800 mb-2">{{ __('Factures totales') }}</h4>
                            <p class="text-2xl font-bold text-indigo-700">{{ $bills->total() }}</p>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">{{ __('Factures à payer') }}</h4>
                            <p class="text-2xl font-bold text-yellow-700">{{ $pendingBillsCount }}</p>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <h4 class="text-sm font-medium text-red-800 mb-2">{{ __('Factures échues') }}</h4>
                            <p class="text-2xl font-bold text-red-700">{{ $overdueBillsCount }}</p>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">{{ __('Montant mensuel récurrent') }}</h4>
                            <p class="text-2xl font-bold text-blue-700">{{ number_format($monthlyRecurringAmount, 2, ',', ' ') }} MAD</p>
                        </div>
                    </div>
                    
                    <!-- Factures à venir -->
                    @if($upcomingBills->count() > 0)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Factures à venir (7 prochains jours)') }}</h4>
                            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($upcomingBills as $bill)
                                        <li class="p-4 flex justify-between items-center">
                                            <div>
                                                <p class="font-medium">{{ $bill->name }}</p>
                                                <p class="text-sm text-gray-500">{{ __('Échéance') }}: {{ $bill->due_date->format('d/m/Y') }}</p>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-lg font-bold mr-4">{{ number_format($bill->amount, 2, ',', ' ') }} MAD</span>
                                                <form method="POST" action="{{ route('bills.markAsPaid', $bill) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        {{ __('Marquer comme payée') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Liste des factures -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtres -->
                    <div class="mb-6 bg-gray-100 p-4 rounded-lg">
                        <form method="GET" action="{{ route('bills.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="status" :value="__('Statut')" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Tous') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>{{ __('Payées') }}</option>
                                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>{{ __('Échues') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="frequency" :value="__('Fréquence')" />
                                <select id="frequency" name="frequency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Toutes') }}</option>
                                    <option value="once" {{ request('frequency') === 'once' ? 'selected' : '' }}>{{ __('Unique') }}</option>
                                    <option value="monthly" {{ request('frequency') === 'monthly' ? 'selected' : '' }}>{{ __('Mensuelle') }}</option>
                                    <option value="yearly" {{ request('frequency') === 'yearly' ? 'selected' : '' }}>{{ __('Annuelle') }}</option>
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <x-primary-button>
                                    {{ __('Filtrer') }}
                                </x-primary-button>
                                
                                <a href="{{ route('bills.index') }}" class="inline-flex items-center ml-4 px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Réinitialiser') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Tableau des factures -->
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
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Montant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Fréquence') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Échéance') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Statut') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($bills as $bill)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $bill->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($bill->category)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                      style="background-color: {{ $bill->category->color ?? '#E5E7EB' }}; color: {{ $bill->category->color ? '#FFFFFF' : '#374151' }}">
                                                    {{ $bill->category->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">{{ __('Non catégorisé') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                            {{ number_format($bill->amount, 2, ',', ' ') }} MAD
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($bill->frequency === 'monthly')
                                                {{ __('Mensuelle') }}
                                            @elseif($bill->frequency === 'yearly')
                                                {{ __('Annuelle') }}
                                            @elseif($bill->frequency === 'once')
                                                {{ __('Unique') }}
                                            @else
                                                {{ $bill->frequency }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $bill->due_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            @if($bill->status === 'paid')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ __('Payée') }}
                                                </span>
                                            @elseif($bill->status === 'overdue')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ __('Échue') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    {{ __('En attente') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-bill-{{ $bill->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                {{ __('Modifier') }}
                                            </button>
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-bill-{{ $bill->id }}')" class="text-red-600 hover:text-red-900">
                                                {{ __('Supprimer') }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('Aucune facture trouvée.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $bills->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter une facture -->
    <x-modal name="add-bill-modal" :show="false" focusable>
        <form method="POST" action="{{ route('bills.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ajouter une nouvelle facture') }}
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
                    <x-input-label for="frequency" :value="__('Fréquence')" />
                    <select id="frequency" name="frequency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="monthly">{{ __('Mensuelle') }}</option>
                        <option value="yearly">{{ __('Annuelle') }}</option>
                        <option value="once">{{ __('Unique') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="day_of_month" :value="__('Jour du mois (pour récurrence mensuelle)')" />
                    <x-text-input id="day_of_month" name="day_of_month" type="number" min="1" max="31" class="mt-1 block w-full" :value="old('day_of_month')" />
                    <x-input-error :messages="$errors->get('day_of_month')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="due_date" :value="__('Date d\'échéance')" />
                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="payment_method" :value="__('Mode de paiement')" />
                    <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Sélectionner') }}</option>
                        <option value="Carte bancaire">{{ __('Carte bancaire') }}</option>
                        <option value="Prélèvement">{{ __('Prélèvement') }}</option>
                        <option value="Virement">{{ __('Virement') }}</option>
                        <option value="Chèque">{{ __('Chèque') }}</option>
                        <option value="Espèces">{{ __('Espèces') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                </div>
                
                <div>
                    <div class="flex items-center mt-4">
                        <input id="auto_pay" name="auto_pay" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="auto_pay" class="ml-2 text-sm text-gray-600">{{ __('Paiement automatique') }}</label>
                    </div>
                    <x-input-error :messages="$errors->get('auto_pay')" class="mt-2" />
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
</x-app-layout> 