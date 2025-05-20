<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('budgets.store') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="name" :value="__('Nom du budget')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            
                            <div class="mt-4">
                                <x-input-label for="category_id" :value="__('Catégorie')" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Sélectionnez une catégorie') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }} ({{ $category->type === 'income' ? __('Revenu') : __('Dépense') }})
                                        </option>
                                    @endforeach
                                </select>
                                @if(count($categories) === 0)
                                    <div class="mt-2 text-sm text-orange-600">
                                        <p>{{ __('Vous n\'avez pas encore de catégories. ') }}
                                            <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                {{ __('Cliquez ici pour en créer') }}
                                            </a>
                                        </p>
                                    </div>
                                @endif
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="amount" :value="__('Montant')" />
                                <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('amount')" required />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="period" :value="__('Période')" />
                                <select id="period" name="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="monthly" {{ old('period') === 'monthly' ? 'selected' : '' }}>{{ __('Mensuel') }}</option>
                                    <option value="yearly" {{ old('period') === 'yearly' ? 'selected' : '' }}>{{ __('Annuel') }}</option>
                                    <option value="weekly" {{ old('period') === 'weekly' ? 'selected' : '' }}>{{ __('Hebdomadaire') }}</option>
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
                        
                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                                {{ __('Annuler') }}
                            </x-secondary-button>
                            
                            <x-primary-button>
                                {{ __('Créer le budget') }}
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
        // Validation des dates
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
                if (endDateInput.value && endDateInput.value < this.value) {
                    endDateInput.value = this.value;
                }
            });
        }
    });
</script>
@endpush 