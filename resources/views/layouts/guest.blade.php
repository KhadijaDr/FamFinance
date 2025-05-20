<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FamFinance') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('images/logo.png') }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-indigo-100 to-purple-100">
            <div class="transform hover:scale-105 transition-transform duration-300">
                <a href="/" class="flex flex-col items-center">
                    <x-application-logo class="w-28 h-28 object-contain" />
                    <span class="mt-2 text-2xl font-bold text-indigo-600">FamFinance</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-6 bg-white shadow-xl overflow-hidden sm:rounded-xl border border-indigo-100">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} FamFinance. {{ __('Tous droits réservés.') }}</p>
                <p class="mt-2">{{ __('Gérez vos finances familiales simplement et efficacement.') }}</p>
            </div>
        </div>

        <!-- Password Toggle Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.toggle-password').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const input = document.getElementById(targetId);
                        const icon = this.querySelector('i');
                        
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });
                });
            });
        </script>
    </body>
</html>
