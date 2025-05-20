<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $category->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> {{ __('Retour') }}
                </a>
                
                <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-category')">
                    <i class="fas fa-edit mr-2"></i> {{ __('Modifier') }}
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Carte d'information de la catégorie -->
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                @if($category->icon)
                                    <div class="w-16 h-16 flex items-center justify-center rounded-full mr-4" style="background-color: {{ $category->color ?? '#E5E7EB' }}">
                                        <i class="fa {{ $category->icon }} text-white text-2xl"></i>
                                    </div>
                                @else
                                    <div class="w-16 h-16 flex items-center justify-center rounded-full mr-4" style="background-color: {{ $category->color ?? '#E5E7EB' }}">
                                        <i class="fa fa-tag text-white text-2xl"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-xl font-semibold">{{ $category->name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $category->type === 'income' ? __('Revenu') : __('Dépense') }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($category->description)
                                <div class="mt-4 bg-gray-50 p-3 rounded-md">
                                    <p class="text-sm text-gray-600">{{ $category->description }}</p>
                                </div>
                            @endif
                            
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">{{ __('Détails') }}</h4>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Transactions') }}</span>
                                        <span class="font-medium">{{ $category->transactions_count ?? 0 }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total des revenus') }}</span>
                                        <span class="font-medium text-green-600">{{ number_format($totalIncome, 2, ',', ' ') }} MAD</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total des dépenses') }}</span>
                                        <span class="font-medium text-red-600">{{ number_format($totalExpense, 2, ',', ' ') }} MAD</span>
                                    </div>
                                    
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="font-medium">{{ __('Montant net') }}</span>
                                        <span class="font-medium {{ $netAmount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($netAmount, 2, ',', ' ') }} MAD
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-between">
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" x-data="{ confirmDelete: false }">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        @click="confirmDelete = true"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium focus:outline-none"
                                        x-show="!confirmDelete"
                                    >
                                        <i class="fas fa-trash-alt mr-1"></i> {{ __('Supprimer') }}
                                    </button>
                                    
                                    <div x-show="confirmDelete" class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">{{ __('Confirmer ?') }}</span>
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium focus:outline-none">
                                            {{ __('Oui') }}
                                        </button>
                                        <button type="button" @click="confirmDelete = false" class="text-gray-600 hover:text-gray-800 text-sm font-medium focus:outline-none">
                                            {{ __('Non') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Transactions récentes -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Transactions récentes') }}</h3>
                            
                            @if(count($recentTransactions) > 0)
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
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Montant') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Actions') }}
                                                </th>
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
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $transaction->type === 'income' ? '+' : '-' }} {{ number_format($transaction->amount, 2, ',', ' ') }} MAD
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    <a href="{{ route('transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('Voir') }}
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-4 text-center">
                                    <a href="{{ route('transactions.index', ['category_id' => $category->id]) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        {{ __('Voir toutes les transactions') }} <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            @else
                                <div class="bg-gray-50 p-4 rounded-md text-center">
                                    <p class="text-gray-600">{{ __('Aucune transaction trouvée pour cette catégorie.') }}</p>
                                    <a href="{{ route('transactions.create', ['category_id' => $category->id]) }}" class="mt-2 inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-plus mr-1"></i> {{ __('Ajouter une transaction') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pour éditer la catégorie -->
    <x-modal name="edit-category" :show="false" focusable>
        <div class="p-6" x-data="{
            name: '{{ $category->name }}',
            type: '{{ $category->type }}',
            description: '{{ $category->description }}',
            color: '{{ $category->color ?? '#3B82F6' }}',
            icon: '{{ $category->icon ?? 'fa-tag' }}',
            showIconPicker: false,
            icons: [
                'fa-home', 'fa-car', 'fa-utensils', 'fa-shopping-cart', 'fa-tshirt', 
                'fa-graduation-cap', 'fa-pills', 'fa-plane', 'fa-gamepad', 'fa-dumbbell',
                'fa-gift', 'fa-birthday-cake', 'fa-coffee', 'fa-glass-martini', 'fa-music',
                'fa-film', 'fa-book', 'fa-bus', 'fa-subway', 'fa-taxi', 'fa-bicycle',
                'fa-credit-card', 'fa-money-bill-alt', 'fa-coins', 'fa-piggy-bank', 'fa-wallet',
                'fa-dollar-sign', 'fa-euro-sign', 'fa-chart-line', 'fa-chart-pie',
                'fa-briefcase', 'fa-building', 'fa-store', 'fa-hospital', 'fa-university',
                'fa-hand-holding-usd', 'fa-funnel-dollar', 'fa-comment-dollar', 'fa-donate'
            ],
            selectIcon(icon) {
                this.icon = icon;
                this.showIconPicker = false;
            },
            submitForm() {
                document.getElementById('edit-category-form').submit();
            }
        }">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Modifier la catégorie') }}
            </h2>
            
            <form id="edit-category-form" method="POST" action="{{ route('categories.update', $category) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="name" :value="__('Nom')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" x-model="name" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <div class="mt-1 flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="type" value="expense" x-model="type" class="form-radio text-red-600 focus:ring-red-500">
                            <span class="ml-2 flex items-center text-sm">
                                <i class="fas fa-arrow-up text-red-500 mr-1"></i> {{ __('Dépense') }}
                            </span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="type" value="income" x-model="type" class="form-radio text-green-600 focus:ring-green-500">
                            <span class="ml-2 flex items-center text-sm">
                                <i class="fas fa-arrow-down text-green-500 mr-1"></i> {{ __('Revenu') }}
                            </span>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="icon" :value="__('Icône')" />
                    <div class="mt-1 flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center" :style="'background-color: ' + color">
                            <i class="fa" :class="icon" class="text-white"></i>
                        </div>
                        <x-text-input id="icon" name="icon" type="hidden" x-model="icon" />
                        <button 
                            type="button" 
                            @click="showIconPicker = !showIconPicker" 
                            class="px-3 py-2 border border-gray-300 rounded-md bg-white text-sm leading-4 text-gray-700 hover:bg-gray-50">
                            {{ __('Choisir une icône') }}
                        </button>
                    </div>
                    
                    <!-- Sélecteur d'icônes -->
                    <div x-show="showIconPicker" class="mt-2 p-3 bg-white border border-gray-200 rounded-md shadow-sm max-h-48 overflow-y-auto">
                        <div class="grid grid-cols-6 gap-2">
                            <template x-for="iconName in icons" :key="iconName">
                                <button 
                                    type="button"
                                    @click="selectIcon(iconName)" 
                                    class="flex items-center justify-center h-10 w-10 rounded-full hover:bg-gray-100"
                                    :class="{'bg-indigo-100 border-2 border-indigo-500': icon === iconName}">
                                    <i class="fa" :class="iconName"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="color" :value="__('Couleur')" />
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="color" id="color" name="color" x-model="color" class="h-10 w-20 border-gray-300 rounded-md shadow-sm">
                        <x-text-input x-model="color" name="color_hex" type="text" class="block w-32" placeholder="#3B82F6" />
                    </div>
                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="description" :value="__('Description (optionnel)')" />
                    <textarea id="description" name="description" rows="3" x-model="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </form>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Annuler') }}
                </x-secondary-button>
                
                <x-primary-button @click="submitForm()" class="ml-3">
                    {{ __('Mettre à jour') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</x-app-layout> 