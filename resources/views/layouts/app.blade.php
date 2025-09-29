<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FamFinance') }}</title>

        <link rel="icon" href="{{ asset('images/logo.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .sidebar-hover-effect:hover .icon-container {
                transform: translateY(-5px) scale(1.1);
                transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            .sidebar-item-active .icon-container {
                transform: translateY(-3px) scale(1.05);
            }
            .sidebar-icon {
                transition: all 0.3s ease;
            }
            .sidebar-link:hover .sidebar-icon {
                filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.6));
                color: white;
            }
            .logo-shine {
                position: relative;
                overflow: hidden;
            }
            .logo-shine::after {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(
                    to right, 
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.4) 50%,
                    rgba(255, 255, 255, 0) 100%
                );
                transform: rotate(30deg);
                animation: shine 6s infinite;
            }
            @keyframes shine {
                0% {transform: translateX(-100%) rotate(30deg);}
                20%, 100% {transform: translateX(100%) rotate(30deg);}
            }
            .notification-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background-color: #EF4444;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .notification-pulse {
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0% {box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);}
                70% {box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);}
                100% {box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);}
            }
            .sidebar-gradient {
                background: linear-gradient(135deg, #4338ca, #3730a3, #312e81);
                background-size: 200% 200%;
                animation: gradientBG 15s ease infinite;
            }
            @keyframes gradientBG {
                0% {background-position: 0% 50%;}
                50% {background-position: 100% 50%;}
                100% {background-position: 0% 50%;}
            }
            .menu-item-appear {
                animation: fadeInRight 0.5s forwards;
                opacity: 0;
            }
            @keyframes fadeInRight {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            .sidebar-icon-pulse {
                animation: iconPulse 2s infinite;
            }
            @keyframes iconPulse {
                0% {transform: scale(1);}
                50% {transform: scale(1.1);}
                100% {transform: scale(1);}
            }
            /* Style personnalisé pour la barre de défilement */
            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(79, 70, 229, 0.1);
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(129, 140, 248, 0.5);
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(129, 140, 248, 0.8);
            }
            .main-content-scrollable {
                overflow-y: auto;
                max-height: calc(100vh - 60px);
            }
            .shimmer-effect {
                animation: shimmer 2.5s infinite linear;
                transform: translateX(-100%);
            }
            @keyframes shimmer {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(100%);
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="flex">
                @auth
                <div class="sidebar-gradient text-white min-h-screen w-20 md:w-72 transition-all duration-500 ease-in-out z-30 shadow-xl backdrop-blur-sm flex flex-col">
                    <div class="h-20 flex-shrink-0 flex items-center justify-center md:justify-start px-3 border-b border-indigo-700/50 backdrop-filter backdrop-blur-sm bg-indigo-900/70">
                        <div class="logo-shine flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center group">
                                <div class="h-10 w-10 mr-3 flex items-center justify-center rounded-lg shadow-lg bg-gradient-to-br from-indigo-600 to-indigo-800 transition-all duration-300 group-hover:scale-110 border border-indigo-400/30 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-indigo-300/10 to-transparent shimmer-effect"></div>
                                    <i class="fas fa-wallet text-white text-xl relative z-10"></i>
                                </div>
                                <span class="font-bold text-xl hidden md:inline-block bg-clip-text text-transparent bg-gradient-to-r from-white to-indigo-200 transition-all duration-300 group-hover:tracking-wide">FamFinance</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <nav class="mt-4 px-4">
                            <div class="space-y-3" style="--delay: 0.1s;">
                                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="sidebar-link sidebar-hover-effect menu-item-appear {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }}" style="animation-delay: calc(var(--delay) * 1);">
                                    <div class="icon-container transition-all duration-300 ease-in-out">
                                        <i class="fas fa-home sidebar-icon h-6 w-6 mr-4"></i>
                                    </div>
                                    <span class="hidden md:inline-block text-sm font-medium">Tableau de bord</span>
                                </x-sidebar-link>
                                
                                <x-sidebar-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="sidebar-link sidebar-hover-effect menu-item-appear {{ request()->routeIs('transactions.*') ? 'sidebar-item-active' : '' }}" style="animation-delay: calc(var(--delay) * 2);">
                                    <div class="icon-container transition-all duration-300 ease-in-out">
                                        <i class="fas fa-exchange-alt sidebar-icon h-6 w-6 mr-4"></i>
                                    </div>
                                    <span class="hidden md:inline-block text-sm font-medium">Transactions</span>
                                </x-sidebar-link>
                                
                                <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="sidebar-link sidebar-hover-effect menu-item-appear {{ request()->routeIs('categories.*') ? 'sidebar-item-active' : '' }}" style="animation-delay: calc(var(--delay) * 3);">
                                    <div class="icon-container transition-all duration-300 ease-in-out">
                                        <i class="fas fa-tags sidebar-icon h-6 w-6 mr-4"></i>
                                    </div>
                                    <span class="hidden md:inline-block text-sm font-medium">Catégories</span>
                                </x-sidebar-link>
                                
                                <x-sidebar-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')" class="sidebar-link sidebar-hover-effect menu-item-appear {{ request()->routeIs('budgets.*') ? 'sidebar-item-active' : '' }}" style="animation-delay: calc(var(--delay) * 4);">
                                    <div class="icon-container transition-all duration-300 ease-in-out">
                                        <i class="fas fa-chart-pie sidebar-icon h-6 w-6 mr-4"></i>
                                    </div>
                                    <span class="hidden md:inline-block text-sm font-medium">Budgets</span>
                                </x-sidebar-link>
                                
                                <x-sidebar-link :href="route('bills.index')" :active="request()->routeIs('bills.*')" class="sidebar-link sidebar-hover-effect menu-item-appear {{ request()->routeIs('bills.*') ? 'sidebar-item-active' : '' }}" style="animation-delay: calc(var(--delay) * 5);">
                                    <div class="icon-container transition-all duration-300 ease-in-out">
                                        <i class="fas fa-file-invoice-dollar sidebar-icon h-6 w-6 mr-4 {{ request()->routeIs('bills.*') ? 'sidebar-icon-pulse' : '' }}"></i>
                                    </div>
                                    <span class="hidden md:inline-block text-sm font-medium">Factures</span>
                                </x-sidebar-link>
                                
                                <x-sidebar-link :href="route('financial-goals.index')" :active="request()->routeIs('financial-goals.*')" class="sidebar-link sidebar-hover-effect menu-item-appear {{ request()->routeIs('financial-goals.*') ? 'sidebar-item-active' : '' }}" style="animation-delay: calc(var(--delay) * 6);">
                                    <div class="icon-container transition-all duration-300 ease-in-out">
                                        <i class="fas fa-bullseye sidebar-icon h-6 w-6 mr-4"></i>
                                    </div>
                                    <span class="hidden md:inline-block text-sm font-medium">Objectifs</span>
                                </x-sidebar-link>

                                <x-sidebar-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" class="sidebar-link sidebar-hover-effect menu-item-appear {{ request()->routeIs('notifications.*') ? 'sidebar-item-active' : '' }}" style="animation-delay: calc(var(--delay) * 7);">
                                    <div class="icon-container transition-all duration-300 ease-in-out relative">
                                        <i class="fas fa-bell sidebar-icon h-6 w-6 mr-4"></i>
                                        <span x-data="{ unreadCount: () => { return document.querySelectorAll('[x-data]').length > 0 ? Alpine.evaluate(document.querySelector('[x-data*=\'notifications\']'), 'notifications.filter(n => n.unread).length') : 0 } }" 
                                              x-text="unreadCount()" 
                                              x-show="unreadCount() > 0"
                                              class="absolute -top-2 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                                    </div>
                                    <span class="hidden md:inline-block text-sm font-medium">Notifications</span>
                                </x-sidebar-link>
                            </div>
                        </nav>
                    </div>

                    <div class="flex-shrink-0 px-4 py-4 border-t border-indigo-700/30">
                        <div class="px-3 py-3 rounded-lg bg-indigo-950/50 shadow-inner backdrop-blur-sm transition-all duration-300 hover:bg-indigo-900/60 border border-indigo-800/30">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full border-2 border-indigo-400 shadow-md transition-all duration-300 hover:scale-110" src="{{ Auth::user()->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ Auth::user()->name }}">
                                </div>
                                <div class="ml-3 hidden md:block">
                                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-indigo-200 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="ml-auto hidden md:flex">
                                    <a href="{{ route('profile.edit') }}" class="text-indigo-200 hover:text-white transition-colors duration-300">
                                        <i class="fas fa-cog hover:rotate-90 transition-transform duration-500"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-3 hidden md:block">
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-xs text-center py-1 rounded-md bg-indigo-800 hover:bg-indigo-700 transition-all duration-300 hover:shadow-md hover:shadow-indigo-500/30">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth

                <div class="flex-1 main-content-scrollable">

                    <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-20">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between h-16">
                                <div class="flex items-center md:hidden">
                                    <button type="button" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-bars text-xl"></i>
                                    </button>
                                </div>
                                
                                <!-- Search -->
                                <div class="flex-1 flex items-center justify-center md:justify-start px-4">
                                    <div class="w-full max-w-lg" x-data="{ searchQuery: '', searchResults: [], isSearching: false, showResults: false, searchTimeout: null }">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-search text-gray-400" :class="{'text-indigo-500': searchQuery.length > 0}"></i>
                                            </div>
                                            <input 
                                                type="search" 
                                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" 
                                                placeholder="Rechercher..." 
                                                x-model="searchQuery"
                                                @input="
                                                    clearTimeout(searchTimeout);
                                                    if (searchQuery.length > 2) {
                                                        isSearching = true;
                                                        searchTimeout = setTimeout(() => {
                                                            // Simule une recherche
                                                            searchResults = [
                                                                { id: 1, type: 'transaction', title: 'Paiement Loyer', description: 'Transaction du 28/06/2023', icon: 'fa-exchange-alt', path: '#' },
                                                                { id: 2, type: 'budget', title: 'Budget Alimentation', description: '75% utilisé ce mois', icon: 'fa-chart-pie', path: '#' },
                                                                { id: 3, type: 'facture', title: 'Facture Électricité', description: 'Échéance le 15/07/2023', icon: 'fa-file-invoice-dollar', path: '#' },
                                                                { id: 4, type: 'transaction', title: 'Revenu Salaire', description: 'Transaction du 25/06/2023', icon: 'fa-exchange-alt', path: '#' }
                                                            ].filter(item => 
                                                                item.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                                                item.description.toLowerCase().includes(searchQuery.toLowerCase())
                                                            );
                                                            isSearching = false;
                                                            showResults = searchResults.length > 0;
                                                        }, 300);
                                                    } else {
                                                        searchResults = [];
                                                        showResults = false;
                                                    }
                                                "
                                                @focus="if (searchQuery.length > 2) showResults = true"
                                                @click.away="showResults = false"
                                            >
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center" x-show="isSearching">
                                                <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Search Results Dropdown -->
                                        <div 
                                            x-show="showResults" 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute mt-2 w-full bg-white rounded-md shadow-lg z-50 overflow-hidden border border-gray-200"
                                            style="display: none;"
                                        >
                                            <div class="max-h-64 overflow-y-auto custom-scrollbar">
                                                <div class="p-2 border-b border-gray-100 bg-gray-50">
                                                    <p class="text-xs text-gray-500">Résultats pour "<span class="font-medium" x-text="searchQuery"></span>"</p>
                                                </div>
                                                <template x-if="searchResults.length > 0">
                                                    <div>
                                                        <template x-for="result in searchResults" :key="result.id">
                                                            <a :href="result.path" class="block hover:bg-indigo-50 transition-colors duration-150">
                                                                <div class="flex items-center px-4 py-3 border-b border-gray-100 last:border-0">
                                                                    <div class="flex-shrink-0 mr-3">
                                                                        <div class="h-9 w-9 rounded-full flex items-center justify-center text-white bg-indigo-600">
                                                                            <i class="fas" :class="result.icon"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-sm font-medium text-gray-900" x-text="result.title"></p>
                                                                        <p class="text-xs text-gray-600" x-text="result.description"></p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="searchResults.length === 0 && searchQuery.length > 2">
                                                    <div class="py-4 px-4 text-center text-sm text-gray-500">
                                                        Aucun résultat trouvé pour "<span class="font-medium" x-text="searchQuery"></span>"
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="p-2 bg-gray-50 border-t border-gray-100">
                                                <a href="#" class="block text-center text-xs font-medium text-indigo-600 hover:text-indigo-800 py-1">
                                                    Recherche avancée
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Side Elements -->
                                <div class="flex items-center">
                                    <!-- Notifications -->
                                    <div class="flex items-center mr-4">
                                        <div class="relative" x-data="{ open: false, notifications: [
                                            {id: 1, title: 'Nouvelle facture', message: 'Votre facture d\'électricité est disponible', time: 'Il y a 30 min', unread: true, icon: 'fa-file-invoice-dollar', color: 'bg-blue-500'},
                                            {id: 2, title: 'Budget dépassé', message: 'Votre budget Restaurants a été dépassé de 15%', time: 'Il y a 2 heures', unread: true, icon: 'fa-chart-pie', color: 'bg-red-500'},
                                            {id: 3, title: 'Nouvel objectif atteint', message: 'Félicitations! Vous avez atteint votre objectif d\'épargne', time: 'Hier', unread: true, icon: 'fa-bullseye', color: 'bg-green-500'},
                                            {id: 4, title: 'Rappel de paiement', message: 'N\'oubliez pas de payer votre loyer avant le 5', time: 'Il y a 2 jours', unread: false, icon: 'fa-calendar-check', color: 'bg-yellow-500'}
                                        ] }">
                                            <button @click="open = !open" type="button" class="p-1 text-gray-500 hover:text-gray-700 focus:outline-none relative">
                                                <i class="fas fa-bell text-xl" :class="{ 'notification-pulse': notifications.some(n => n.unread) }"></i>
                                                <span x-show="notifications.filter(n => n.unread).length > 0" x-text="notifications.filter(n => n.unread).length" class="notification-badge"></span>
                                            </button>
                                            
                                            <!-- Dropdown menu -->
                                            <div x-show="open" 
                                                 @click.away="open = false"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 overflow-hidden"
                                                 style="display: none;">
                                                
                                                <div class="px-4 py-3 border-b border-gray-100 bg-indigo-50">
                                                    <div class="flex justify-between items-center">
                                                        <h3 class="text-sm font-semibold text-indigo-800">Notifications</h3>
                                                        <button @click="notifications.forEach(n => n.unread = false)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                                            Tout marquer comme lu
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                                    <template x-for="notification in notifications" :key="notification.id">
                                                        <div class="border-b border-gray-100 last:border-0 transition-all duration-200 hover:bg-gray-50" :class="{ 'bg-indigo-50/50': notification.unread }">
                                                            <div class="flex p-4 cursor-pointer" @click="notification.unread = false">
                                                                <div class="flex-shrink-0 mr-3">
                                                                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white" :class="notification.color">
                                                                        <i class="fas" :class="notification.icon"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="flex justify-between">
                                                                        <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                                                        <p class="text-xs text-gray-500" x-text="notification.time"></p>
                                                                    </div>
                                                                    <p class="text-xs text-gray-600 mt-1 truncate" x-text="notification.message"></p>
                                                                </div>
                                                                <div class="ml-2 flex-shrink-0 self-center" x-show="notification.unread">
                                                                    <div class="h-2 w-2 rounded-full bg-indigo-600"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                                
                                                <div class="p-2 bg-gray-50 border-t border-gray-100">
                                                    <a href="#" class="block text-center text-xs font-medium text-indigo-600 hover:text-indigo-800 py-1">
                                                        Voir toutes les notifications
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Profile Dropdown -->
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-800 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                                <div class="flex items-center">
                                                    <img class="h-8 w-8 rounded-full object-cover border border-gray-200" src="{{ Auth::user()->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ Auth::user()->name }}">
                                                    <div class="ml-2 hidden md:block">
                                                        <div class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</div>
                                                    </div>
                                                </div>
                                                <div class="ml-1">
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Profile') }}
                                            </x-dropdown-link>

                                            <!-- Authentication -->
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')"
                                                        onclick="event.preventDefault();
                                                                    this.closest('form').submit();">
                                                    {{ __('Déconnexion') }}
                                                </x-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Page Heading -->
                    @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
                    @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
            </div>
        </div>
        
        <!-- Scripts -->
        @stack('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Script pour le toggle de mot de passe
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
