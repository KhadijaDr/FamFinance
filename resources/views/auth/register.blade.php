<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-indigo-700">{{ __('Créez votre compte') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('Commencez à gérer vos finances familiales') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nom')" class="text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <x-text-input id="name" class="block mt-1 w-full pl-10" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Votre nom" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <x-text-input id="email" class="block mt-1 w-full pl-10" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="votre@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" class="text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input id="password" class="block mt-1 w-full pl-10 pr-10"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="8 caractères minimum" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none toggle-password" data-target="password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmez le mot de passe')" class="text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-check-double text-gray-400"></i>
                </div>
                <x-text-input id="password_confirmation" class="block mt-1 w-full pl-10 pr-10"
                            type="password"
                            name="password_confirmation" 
                            required autocomplete="new-password"
                            placeholder="Répétez votre mot de passe" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none toggle-password" data-target="password_confirmation">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-user-plus mr-2"></i> {{ __('S\'inscrire') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">{{ __('Vous avez déjà un compte?') }}</p>
        <a href="{{ route('login') }}" class="mt-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
            {{ __('Se connecter') }}
        </a>
    </div>

    <div class="mt-6 border-t border-gray-200 pt-4 text-xs text-gray-500 text-center">
        <p>{{ __('En vous inscrivant, vous acceptez nos conditions d\'utilisation et notre politique de confidentialité.') }}</p>
    </div>
</x-guest-layout>
