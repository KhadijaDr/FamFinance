<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transactions') }}
            </h2>
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-transaction-modal')">
                {{ __('Ajouter une transaction') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtres de recherche -->
                    <div class="mb-6 bg-gray-100 p-4 rounded-lg">
                        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Tous') }}</option>
                                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>{{ __('Revenus') }}</option>
                                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>{{ __('Dépenses') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="category" :value="__('Catégorie')" />
                                <select id="category" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Toutes') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="date_from" :value="__('Date de début')" />
                                <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" :value="request('date_from')" />
                            </div>
                            
                            <div>
                                <x-input-label for="date_to" :value="__('Date de fin')" />
                                <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" :value="request('date_to')" />
                            </div>
                            
                            <div class="md:col-span-4 flex justify-end">
                                <x-primary-button>
                                    {{ __('Filtrer') }}
                                </x-primary-button>
                                
                                <a href="{{ route('transactions.index') }}" class="inline-flex items-center ml-4 px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Réinitialiser') }}
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Tableau des transactions -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Description') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Catégorie') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Montant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Mode de paiement') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : __('Date inconnue') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($transaction->category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                style="background-color: {{ $transaction->category->color ?? '#E5E7EB' }}; color: {{ $transaction->category->color ? '#FFFFFF' : '#374151' }}">
                                                {{ $transaction->category->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ __('Non catégorisé') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }} {{ number_format($transaction->amount, 2, ',', ' ') }} MAD
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->payment_method ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-transaction-{{ $transaction->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            {{ __('Modifier') }}
                                        </button>
                                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-transaction-{{ $transaction->id }}')" class="text-red-600 hover:text-red-900">
                                            {{ __('Supprimer') }}
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ __('Aucune transaction trouvée.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter une transaction -->
    <x-modal name="add-transaction-modal" :show="false" focusable>
        <form method="POST" action="{{ route('transactions.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ajouter une nouvelle transaction') }}
            </h2>
            
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="expense">{{ __('Dépense') }}</option>
                        <option value="income">{{ __('Revenu') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="date" :value="__('Date')" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
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
                
                <div class="md:col-span-2">
                    <x-input-label for="description" :value="__('Description')" />
                    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description')" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="payment_method" :value="__('Mode de paiement')" />
                    <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Sélectionner') }}</option>
                        <option value="Carte bancaire">{{ __('Carte bancaire') }}</option>
                        <option value="Espèce">{{ __('Espèce') }}</option>
                        <option value="Virement">{{ __('Virement') }}</option>
                        <option value="Prélèvement">{{ __('Prélèvement') }}</option>
                        <option value="Chèque">{{ __('Chèque') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="status" :value="__('Statut')" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="completed">{{ __('Complétée') }}</option>
                        <option value="pending">{{ __('En attente') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
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
    
    <!-- Modals pour éditer/supprimer les transactions (générés dynamiquement) -->
    @foreach($transactions as $transaction)
        <!-- Modal pour éditer la transaction -->
        <x-modal name="edit-transaction-{{ $transaction->id }}" :show="false" focusable>
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="p-6">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Modifier la transaction') }}
                </h2>
                
                <!-- Contenu du formulaire d'édition (similaire au formulaire d'ajout mais avec les valeurs préremplies) -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit_type_{{ $transaction->id }}" :value="__('Type')" />
                        <select id="edit_type_{{ $transaction->id }}" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="expense" {{ $transaction->type === 'expense' ? 'selected' : '' }}>{{ __('Dépense') }}</option>
                            <option value="income" {{ $transaction->type === 'income' ? 'selected' : '' }}>{{ __('Revenu') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <x-input-label for="edit_date_{{ $transaction->id }}" :value="__('Date')" />
                        <x-text-input id="edit_date_{{ $transaction->id }}" name="date" type="date" class="mt-1 block w-full" :value="$transaction->transaction_date->format('Y-m-d')" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_amount_{{ $transaction->id }}" :value="__('Montant')" />
                        <x-text-input id="edit_amount_{{ $transaction->id }}" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="$transaction->amount" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_category_id_{{ $transaction->id }}" :value="__('Catégorie')" />
                        <select id="edit_category_id_{{ $transaction->id }}" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Sélectionner une catégorie') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $transaction->category_id === $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <x-input-label for="edit_description_{{ $transaction->id }}" :value="__('Description')" />
                        <x-text-input id="edit_description_{{ $transaction->id }}" name="description" type="text" class="mt-1 block w-full" :value="$transaction->description" />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_payment_method_{{ $transaction->id }}" :value="__('Mode de paiement')" />
                        <select id="edit_payment_method_{{ $transaction->id }}" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Sélectionner') }}</option>
                            <option value="Carte bancaire" {{ $transaction->payment_method === 'Carte bancaire' ? 'selected' : '' }}>{{ __('Carte bancaire') }}</option>
                            <option value="Espèce" {{ $transaction->payment_method === 'Espèce' ? 'selected' : '' }}>{{ __('Espèce') }}</option>
                            <option value="Virement" {{ $transaction->payment_method === 'Virement' ? 'selected' : '' }}>{{ __('Virement') }}</option>
                            <option value="Prélèvement" {{ $transaction->payment_method === 'Prélèvement' ? 'selected' : '' }}>{{ __('Prélèvement') }}</option>
                            <option value="Chèque" {{ $transaction->payment_method === 'Chèque' ? 'selected' : '' }}>{{ __('Chèque') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <x-input-label for="edit_status_{{ $transaction->id }}" :value="__('Statut')" />
                        <select id="edit_status_{{ $transaction->id }}" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>{{ __('Complétée') }}</option>
                            <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                        </select>
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
        
        <!-- Modal pour supprimer la transaction -->
        <x-modal name="delete-transaction-{{ $transaction->id }}" :show="false" focusable>
            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="p-6">
                @csrf
                @method('DELETE')
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Supprimer la transaction') }}
                </h2>
                
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Êtes-vous sûr de vouloir supprimer cette transaction ? Cette action est irréversible.') }}
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