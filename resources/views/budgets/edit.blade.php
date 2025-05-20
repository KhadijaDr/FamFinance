<!-- Catégorie -->
<div class="mt-4">
    <x-input-label for="category_id" :value="__('Catégorie')" />
    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">{{ __('Sélectionnez une catégorie') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $budget->category_id) == $category->id ? 'selected' : '' }}>
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