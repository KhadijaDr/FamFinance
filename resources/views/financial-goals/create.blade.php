<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un objectif financier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('financial-goals.store') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        
                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                                {{ __('Annuler') }}
                            </x-secondary-button>
                            
                            <x-primary-button>
                                {{ __('Créer l\'objectif') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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