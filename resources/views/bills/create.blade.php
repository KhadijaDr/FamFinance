<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter une facture') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('bills.store') }}">
                        @csrf

                        <!-- Nom -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Nom de la facture') }}</label>
                            <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="name" value="{{ old('name') }}" required autofocus />
                            @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Montant -->
                        <div class="mt-4">
                            <label for="amount" class="block font-medium text-sm text-gray-700">{{ __('Montant') }}</label>
                            <input id="amount" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" name="amount" value="{{ old('amount') }}" required step="0.01" min="0.01" />
                            @error('amount')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date d'échéance -->
                        <div class="mt-4">
                            <label for="due_date" class="block font-medium text-sm text-gray-700">{{ __('Date d\'échéance') }}</label>
                            <input id="due_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="date" name="due_date" value="{{ old('due_date') }}" required />
                            @error('due_date')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catégorie -->
                        <div class="mt-4">
                            <label for="category_id" class="block font-medium text-sm text-gray-700">{{ __('Catégorie') }}</label>
                            <select id="category_id" name="category_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">{{ __('Sélectionner une catégorie') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fréquence -->
                        <div class="mt-4">
                            <label for="frequency" class="block font-medium text-sm text-gray-700">{{ __('Fréquence') }}</label>
                            <select id="frequency" name="frequency" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                @foreach($recurrenceOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('frequency') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('frequency')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Méthode de paiement (optionnel) -->
                        <div class="mt-4">
                            <label for="payment_method" class="block font-medium text-sm text-gray-700">{{ __('Méthode de paiement (optionnel)') }}</label>
                            <input id="payment_method" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="payment_method" value="{{ old('payment_method') }}" />
                            @error('payment_method')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                         <!-- Paiement automatique (optionnel) -->
                         <div class="mt-4">
                            <label for="auto_pay" class="flex items-center">
                                <input id="auto_pay" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="auto_pay" value="1" {{ old('auto_pay') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Paiement automatique') }}</span>
                            </label>
                            @error('auto_pay')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes (optionnel) -->
                        <div class="mt-4">
                            <label for="notes" class="block font-medium text-sm text-gray-700">{{ __('Notes (optionnel)') }}</label>
                            <textarea id="notes" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="notes">{{ old('notes') }}</textarea>
                             @error('notes')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Ajouter la facture') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 