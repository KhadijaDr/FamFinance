<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Objectifs Financiers') }}
            </h2>
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-goal-modal')">
                {{ __('Ajouter un objectif') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Vue d'ensemble des objectifs -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Vue d\'ensemble des objectifs') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                            <h4 class="text-sm font-medium text-indigo-800 mb-2">{{ __('Objectifs actifs') }}</h4>
                            <p class="text-2xl font-bold text-indigo-700">{{ $activeGoalsCount }}</p>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <h4 class="text-sm font-medium text-green-800 mb-2">{{ __('Objectifs atteints') }}</h4>
                            <p class="text-2xl font-bold text-green-700">{{ $completedGoalsCount }}</p>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">{{ __('Montant total épargné') }}</h4>
                            <p class="text-2xl font-bold text-yellow-700">{{ number_format($totalSaved, 2, ',', ' ') }} MAD</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des objectifs financiers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtres -->
                    <div class="mb-6 bg-gray-100 p-4 rounded-lg">
                        <form method="GET" action="{{ route('financial-goals.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="status" :value="__('Statut')" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Tous') }}</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Actifs') }}</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Atteints') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="priority" :value="__('Priorité')" />
                                <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Toutes') }}</option>
                                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('Haute') }}</option>
                                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('Moyenne') }}</option>
                                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('Basse') }}</option>
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <x-primary-button>
                                    {{ __('Filtrer') }}
                                </x-primary-button>
                                
                                <a href="{{ route('financial-goals.index') }}" class="inline-flex items-center ml-4 px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Réinitialiser') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Grille des objectifs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($goals as $goal)
                            @php
                                $percentage = $goal->target_amount > 0 ? min(100, ($goal->current_amount / $goal->target_amount) * 100) : 0;
                                $priorityColor = $goal->priority === 'high' ? 'bg-red-100 text-red-800' : ($goal->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800');
                                $daysLeft = $goal->target_date ? now()->diffInDays($goal->target_date, false) : null;
                                $isCompleted = $goal->is_completed;
                                $progressBarColor = $isCompleted ? 'bg-green-500' : 'bg-blue-500';
                            @endphp
                            
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-300">
                                <div class="p-5">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $goal->name }}</h3>
                                            @if($goal->priority)
                                                <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColor }}">
                                                    @if($goal->priority === 'high')
                                                        {{ __('Priorité haute') }}
                                                    @elseif($goal->priority === 'medium')
                                                        {{ __('Priorité moyenne') }}
                                                    @else
                                                        {{ __('Priorité basse') }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($isCompleted)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Atteint') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-4">
                                        <div class="flex justify-between mb-1 text-sm">
                                            <span class="font-medium">{{ __('Progression') }}: {{ round($percentage) }}%</span>
                                            <span>{{ number_format($goal->current_amount, 2, ',', ' ') }} MAD / {{ number_format($goal->target_amount, 2, ',', ' ') }} MAD</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="{{ $progressBarColor }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">{{ __('Date de début') }}</p>
                                            <p class="font-medium">{{ $goal->start_date->format('d/m/Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">{{ __('Date cible') }}</p>
                                            <p class="font-medium">{{ $goal->target_date->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($daysLeft !== null && !$isCompleted)
                                        <div class="mt-4 text-sm {{ $daysLeft < 0 ? 'text-red-600' : 'text-gray-600' }}">
                                            @if($daysLeft < 0)
                                                {{ __('Dépassé de') }} {{ abs($daysLeft) }} {{ abs($daysLeft) > 1 ? __('jours') : __('jour') }}
                                            @elseif($daysLeft === 0)
                                                {{ __('Dernier jour') }}
                                            @else
                                                {{ __('Reste') }} {{ $daysLeft }} {{ $daysLeft > 1 ? __('jours') : __('jour') }}
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($goal->notes)
                                        <div class="mt-4 text-sm text-gray-600">
                                            <p>{{ Str::limit($goal->notes, 100) }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-5 flex justify-between">
                                        @if(!$isCompleted)
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-contribution-{{ $goal->id }}')" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                                {{ __('Ajouter une contribution') }}
                                            </button>
                                        @else
                                            <span></span>
                                        @endif
                                        
                                        <div class="flex space-x-2">
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-goal-{{ $goal->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Modifier') }}
                                            </button>
                                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-goal-{{ $goal->id }}')" class="text-red-600 hover:text-red-900">
                                                {{ __('Supprimer') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 py-8 text-center text-gray-500">
                                <p>{{ __('Aucun objectif financier trouvé.') }}</p>
                                <p class="mt-2">{{ __('Commencez par créer un objectif pour suivre votre progression d\'épargne.') }}</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-6">
                        {{ $goals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter un objectif -->
    <x-modal name="add-goal-modal" :show="false" focusable>
        <form method="POST" action="{{ route('financial-goals.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ajouter un nouvel objectif financier') }}
            </h2>
            
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="name" :value="__('Nom')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="target_amount" :value="__('Montant cible')" />
                    <x-text-input id="target_amount" name="target_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('target_amount')" required />
                    <x-input-error :messages="$errors->get('target_amount')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="current_amount" :value="__('Montant actuel (optionnel)')" />
                    <x-text-input id="current_amount" name="current_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('current_amount', 0)" />
                    <x-input-error :messages="$errors->get('current_amount')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="start_date" :value="__('Date de début')" />
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="target_date" :value="__('Date cible')" />
                    <x-text-input id="target_date" name="target_date" type="date" class="mt-1 block w-full" :value="old('target_date')" required />
                    <x-input-error :messages="$errors->get('target_date')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="priority" :value="__('Priorité')" />
                    <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>{{ __('Basse') }}</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>{{ __('Moyenne') }}</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('Haute') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="color" :value="__('Couleur (optionnel)')" />
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="color" id="color" name="color" value="{{ old('color', '#3B82F6') }}" class="h-10 w-20 border-gray-300 rounded-md shadow-sm">
                        <x-text-input name="color_hex" type="text" value="{{ old('color_hex', '#3B82F6') }}" class="block w-32" placeholder="#3B82F6" />
                    </div>
                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="icon" :value="__('Icône (optionnel)')" />
                    <x-text-input id="icon" name="icon" type="text" class="mt-1 block w-full" :value="old('icon')" placeholder="fa-piggy-bank" />
                    <x-input-error :messages="$errors->get('icon')" class="mt-2" />
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
    
    <!-- Modals pour les objectifs (générés dynamiquement) -->
    @foreach($goals as $goal)
        <!-- Modal pour ajouter une contribution -->
        <x-modal name="add-contribution-{{ $goal->id }}" :show="false" focusable>
            <form method="POST" action="{{ route('financial-goals.addContribution', $goal) }}" class="p-6">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Ajouter une contribution à') }} "{{ $goal->name }}"
                </h2>
                
                <div class="mt-6 space-y-4">
                    <div>
                        <x-input-label for="contribution_amount_{{ $goal->id }}" :value="__('Montant')" />
                        <x-text-input id="contribution_amount_{{ $goal->id }}" name="amount" type="number" step="0.01" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>
                    
                    <div>
                        <x-input-label for="contribution_date_{{ $goal->id }}" :value="__('Date')" />
                        <x-text-input id="contribution_date_{{ $goal->id }}" name="date" type="date" class="mt-1 block w-full" :value="date('Y-m-d')" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>
                    
                    <div>
                        <x-input-label for="contribution_notes_{{ $goal->id }}" :value="__('Notes (optionnel)')" />
                        <textarea id="contribution_notes_{{ $goal->id }}" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Annuler') }}
                    </x-secondary-button>
                    
                    <x-primary-button class="ml-3">
                        {{ __('Ajouter') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
        
        <!-- Modal pour éditer l'objectif -->
        <x-modal name="edit-goal-{{ $goal->id }}" :show="false" focusable>
            <form method="POST" action="{{ route('financial-goals.update', $goal) }}" class="p-6">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Modifier l\'objectif') }}
                </h2>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="edit_name_{{ $goal->id }}" :value="__('Nom')" />
                        <x-text-input id="edit_name_{{ $goal->id }}" name="name" type="text" class="mt-1 block w-full" :value="$goal->name" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_target_amount_{{ $goal->id }}" :value="__('Montant cible')" />
                        <x-text-input id="edit_target_amount_{{ $goal->id }}" name="target_amount" type="number" step="0.01" class="mt-1 block w-full" :value="$goal->target_amount" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_current_amount_{{ $goal->id }}" :value="__('Montant actuel')" />
                        <x-text-input id="edit_current_amount_{{ $goal->id }}" name="current_amount" type="number" step="0.01" class="mt-1 block w-full" :value="$goal->current_amount" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_start_date_{{ $goal->id }}" :value="__('Date de début')" />
                        <x-text-input id="edit_start_date_{{ $goal->id }}" name="start_date" type="date" class="mt-1 block w-full" :value="$goal->start_date->format('Y-m-d')" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_target_date_{{ $goal->id }}" :value="__('Date cible')" />
                        <x-text-input id="edit_target_date_{{ $goal->id }}" name="target_date" type="date" class="mt-1 block w-full" :value="$goal->target_date->format('Y-m-d')" required />
                    </div>
                    
                    <div>
                        <x-input-label for="edit_priority_{{ $goal->id }}" :value="__('Priorité')" />
                        <select id="edit_priority_{{ $goal->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="medium" {{ $goal->priority === 'medium' ? 'selected' : '' }}>{{ __('Moyenne') }}</option>
                            <option value="high" {{ $goal->priority === 'high' ? 'selected' : '' }}>{{ __('Haute') }}</option>
                            <option value="low" {{ $goal->priority === 'low' ? 'selected' : '' }}>{{ __('Basse') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <div class="flex items-center mt-4">
                            <input id="edit_is_completed_{{ $goal->id }}" name="is_completed" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $goal->is_completed ? 'checked' : '' }}>
                            <label for="edit_is_completed_{{ $goal->id }}" class="ml-2 text-sm text-gray-600">{{ __('Objectif atteint') }}</label>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <x-input-label for="edit_notes_{{ $goal->id }}" :value="__('Notes (optionnel)')" />
                        <textarea id="edit_notes_{{ $goal->id }}" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $goal->notes }}</textarea>
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
        
        <!-- Modal pour supprimer l'objectif -->
        <x-modal name="delete-goal-{{ $goal->id }}" :show="false" focusable>
            <form method="POST" action="{{ route('financial-goals.destroy', $goal) }}" class="p-6">
                @csrf
                @method('DELETE')
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Supprimer l\'objectif') }}
                </h2>
                
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Êtes-vous sûr de vouloir supprimer cet objectif ? Cette action est irréversible.') }}
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du sélecteur de couleur
        const colorInput = document.getElementById('color');
        const colorHexInput = document.querySelector('input[name="color_hex"]');
        
        if (colorInput && colorHexInput) {
            colorInput.addEventListener('input', function() {
                colorHexInput.value = this.value;
            });
            
            colorHexInput.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                    colorInput.value = this.value;
                }
            });
        }
        
        // Validation des dates
        const startDateInput = document.getElementById('start_date');
        const targetDateInput = document.getElementById('target_date');
        
        if (startDateInput && targetDateInput) {
            startDateInput.addEventListener('change', function() {
                targetDateInput.min = this.value;
                if (targetDateInput.value && targetDateInput.value < this.value) {
                    targetDateInput.value = this.value;
                }
            });
        }
    });
</script>
@endpush 