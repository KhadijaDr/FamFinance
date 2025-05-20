<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Catégories') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('categories.initialize-default') }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 focus:bg-indigo-600 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-magic mr-2"></i> {{ __('Créer catégories par défaut') }}
                </a>
                <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-category-modal')">
                    <i class="fas fa-plus mr-2"></i> {{ __('Ajouter une catégorie') }}
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                            <div class="flex items-center">
                                <div class="py-1"><i class="fas fa-check-circle text-green-500 mr-2"></i></div>
                                <div>
                                    <p class="font-semibold">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if (session('info'))
                        <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
                            <div class="flex items-center">
                                <div class="py-1"><i class="fas fa-info-circle text-blue-500 mr-2"></i></div>
                                <div>
                                    <p class="font-semibold">{{ session('info') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div x-data="{
                        activeFilter: '{{ request('type') ?: 'all' }}',
                        searchQuery: '{{ request('search') }}',
                        showIconPicker: false,
                        selectedIcon: 'fa-tag',
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
                        setFilter(filter) {
                            this.activeFilter = filter;
                            document.getElementById('type').value = filter === 'all' ? '' : filter;
                        },
                        applyFilters() {
                            document.getElementById('filter-form').submit();
                        },
                        resetFilters() {
                            this.activeFilter = 'all';
                            this.searchQuery = '';
                            window.location.href = '{{ route('categories.index') }}';
                        },
                        selectIcon(icon) {
                            this.selectedIcon = icon;
                            document.getElementById('icon').value = icon;
                            this.showIconPicker = false;
                        }
                    }">
                        <!-- Filtres -->
                        <div class="mb-8 bg-gray-50 p-5 rounded-xl border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Filtrer les catégories') }}</h3>
                            
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-2">
                                    <button 
                                        @click="setFilter('all')" 
                                        :class="{'bg-indigo-600 text-white': activeFilter === 'all', 'bg-white text-gray-700 hover:bg-gray-100': activeFilter !== 'all'}"
                                        class="px-4 py-2 rounded-md text-sm font-medium border border-gray-300 shadow-sm transition-all duration-200">
                                        {{ __('Toutes') }}
                                    </button>
                                    <button 
                                        @click="setFilter('income')" 
                                        :class="{'bg-green-600 text-white': activeFilter === 'income', 'bg-white text-gray-700 hover:bg-gray-100': activeFilter !== 'income'}"
                                        class="px-4 py-2 rounded-md text-sm font-medium border border-gray-300 shadow-sm transition-all duration-200">
                                        <i class="fas fa-arrow-down text-green-500 mr-1" :class="{'text-white': activeFilter === 'income'}"></i> {{ __('Revenus') }}
                                    </button>
                                    <button 
                                        @click="setFilter('expense')" 
                                        :class="{'bg-red-600 text-white': activeFilter === 'expense', 'bg-white text-gray-700 hover:bg-gray-100': activeFilter !== 'expense'}"
                                        class="px-4 py-2 rounded-md text-sm font-medium border border-gray-300 shadow-sm transition-all duration-200">
                                        <i class="fas fa-arrow-up text-red-500 mr-1" :class="{'text-white': activeFilter === 'expense'}"></i> {{ __('Dépenses') }}
                                    </button>
                                </div>
                            </div>
                            
                            <form id="filter-form" method="GET" action="{{ route('categories.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                                <input type="hidden" id="type" name="type" :value="activeFilter === 'all' ? '' : activeFilter">
                                
                                <div class="flex-1">
                                    <x-input-label for="search" :value="__('Recherche')" />
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                        <x-text-input 
                                            id="search" 
                                            class="block w-full pl-10" 
                                            type="text" 
                                            name="search" 
                                            x-model="searchQuery" 
                                            placeholder="Nom de la catégorie" />
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <x-primary-button type="submit">
                                        {{ __('Appliquer') }}
                                    </x-primary-button>
                                    
                                    <button type="button" @click="resetFilters" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Réinitialiser') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Statistiques des catégories -->
                        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl p-4 shadow-md flex items-center">
                                <div class="rounded-full bg-white bg-opacity-30 p-3 mr-4">
                                    <i class="fas fa-tags text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-indigo-100">Total des catégories</p>
                                    <p class="text-2xl font-bold">{{ $categories->total() }}</p>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 shadow-md flex items-center">
                                <div class="rounded-full bg-white bg-opacity-30 p-3 mr-4">
                                    <i class="fas fa-arrow-down text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-green-100">Catégories de revenus</p>
                                    <p class="text-2xl font-bold">{{ $categories->where('type', 'income')->count() }}</p>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl p-4 shadow-md flex items-center">
                                <div class="rounded-full bg-white bg-opacity-30 p-3 mr-4">
                                    <i class="fas fa-arrow-up text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-red-100">Catégories de dépenses</p>
                                    <p class="text-2xl font-bold">{{ $categories->where('type', 'expense')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Grille des catégories -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" 
                             x-data="{ 
                                 categories: {{ json_encode($categories->items()) }},
                                 deleteCategory(id) {
                                     if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.')) {
                                         document.getElementById('delete-form-' + id).submit();
                                     }
                                 }
                             }">
                            @forelse($categories as $category)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-300 transform hover:-translate-y-1"
                                     style="border-left: 4px solid {{ $category->color ?? '#9CA3AF' }}">
                                    <div class="p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center">
                                                @if($category->icon)
                                                    <div class="w-12 h-12 flex items-center justify-center rounded-full mr-3 shadow-sm" style="background-color: {{ $category->color ?? '#E5E7EB' }}">
                                                        <i class="fa {{ $category->icon }} text-white text-xl"></i>
                                                    </div>
                                                @else
                                                    <div class="w-12 h-12 flex items-center justify-center rounded-full mr-3 shadow-sm" style="background-color: {{ $category->color ?? '#E5E7EB' }}">
                                                        <i class="fa fa-tag text-white text-xl"></i>
                                                    </div>
                                                @endif
                                                <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $category->type === 'income' ? __('Revenu') : __('Dépense') }}
                                            </span>
                                        </div>
                                        
                                        @if($category->description)
                                            <p class="mt-2 text-sm text-gray-600">{{ $category->description }}</p>
                                        @endif
                                        
                                        <div class="mt-4">
                                            <div class="flex items-center justify-between text-sm text-gray-600">
                                                <span>{{ __('Transactions') }}</span>
                                                <span class="font-medium">{{ $category->transactions_count ?? 0 }}</span>
                                            </div>
                                            
                                            @php
                                                $totalAmount = $category->type === 'income' ? ($category->total_income ?? 0) : ($category->total_expense ?? 0);
                                                $formattedAmount = number_format($totalAmount, 2, ',', ' ') . ' €';
                                            @endphp
                                            
                                            <div class="flex items-center justify-between text-sm mt-1">
                                                <span>{{ __('Montant total') }}</span>
                                                <span class="font-medium {{ $category->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $formattedAmount }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                                        <a href="{{ route('categories.show', $category) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            {{ __('Détails') }} <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                        
                                        <div class="flex space-x-3">
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-category-{{ $category->id }}')" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click.prevent="deleteCategory({{ $category->id }})" class="text-sm text-gray-600 hover:text-red-600 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <form id="delete-form-{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 bg-white rounded-lg shadow-sm p-8 text-center">
                                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-tags text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Aucune catégorie trouvée') }}</h3>
                                    <p class="text-gray-500 mb-6">{{ __('Commencez par créer une catégorie pour mieux organiser vos finances.') }}</p>
                                    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-category-modal')">
                                        <i class="fas fa-plus mr-2"></i> {{ __('Créer ma première catégorie') }}
                                    </x-primary-button>
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="mt-6">
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter une catégorie -->
    <x-modal name="add-category-modal" :show="false" focusable>
        <div class="p-6" x-data="{
            name: '',
            type: 'expense',
            description: '',
            color: '#3B82F6',
            icon: 'fa-tag',
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
                let form = document.getElementById('add-category-form');
                form.submit();
            }
        }">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ajouter une nouvelle catégorie') }}
            </h2>
            
            <form id="add-category-form" method="POST" action="{{ route('categories.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('Nom')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" x-model="name" required autofocus />
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
                    {{ __('Enregistrer') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>
    
    <!-- Modals pour éditer les catégories (générés dynamiquement) -->
    @foreach($categories as $category)
        <x-modal name="edit-category-{{ $category->id }}" :show="false" focusable>
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
                    document.getElementById('edit-category-form-{{ $category->id }}').submit();
                }
            }">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Modifier la catégorie') }}
                </h2>
                
                <form id="edit-category-form-{{ $category->id }}" method="POST" action="{{ route('categories.update', $category) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="edit_name_{{ $category->id }}" :value="__('Nom')" />
                        <x-text-input id="edit_name_{{ $category->id }}" name="name" type="text" class="mt-1 block w-full" x-model="name" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_type_{{ $category->id }}" :value="__('Type')" />
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
                    </div>
                    
                    <div>
                        <x-input-label for="edit_icon_{{ $category->id }}" :value="__('Icône')" />
                        <div class="mt-1 flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center" :style="'background-color: ' + color">
                                <i class="fa" :class="icon" class="text-white"></i>
                            </div>
                            <x-text-input id="edit_icon_{{ $category->id }}" name="icon" type="hidden" x-model="icon" />
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
                    </div>
                    
                    <div>
                        <x-input-label for="edit_color_{{ $category->id }}" :value="__('Couleur')" />
                        <div class="mt-1 flex items-center space-x-2">
                            <input type="color" id="edit_color_{{ $category->id }}" name="color" x-model="color" class="h-10 w-20 border-gray-300 rounded-md shadow-sm">
                            <x-text-input x-model="color" name="color_hex" type="text" class="block w-32" placeholder="#3B82F6" />
                        </div>
                    </div>
                    
                    <div>
                        <x-input-label for="edit_description_{{ $category->id }}" :value="__('Description (optionnel)')" />
                        <textarea id="edit_description_{{ $category->id }}" name="description" rows="3" x-model="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
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
    @endforeach
</x-app-layout> 