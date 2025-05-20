<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div x-data="{ 
                        notifications: [
                            {id: 1, title: 'Nouvelle facture', message: 'Votre facture d\'électricité est disponible', time: 'Il y a 30 min', date: '2023-06-28 14:30:00', unread: true, icon: 'fa-file-invoice-dollar', color: 'bg-blue-500', link: '#'},
                            {id: 2, title: 'Budget dépassé', message: 'Votre budget Restaurants a été dépassé de 15%', time: 'Il y a 2 heures', date: '2023-06-28 13:00:00', unread: true, icon: 'fa-chart-pie', color: 'bg-red-500', link: '#'},
                            {id: 3, title: 'Nouvel objectif atteint', message: 'Félicitations! Vous avez atteint votre objectif d\'épargne', time: 'Hier', date: '2023-06-27 09:15:00', unread: true, icon: 'fa-bullseye', color: 'bg-green-500', link: '#'},
                            {id: 4, title: 'Rappel de paiement', message: 'N\'oubliez pas de payer votre loyer avant le 5', time: 'Il y a 2 jours', date: '2023-06-26 10:30:00', unread: false, icon: 'fa-calendar-check', color: 'bg-yellow-500', link: '#'},
                            {id: 5, title: 'Transaction importante', message: 'Une transaction de 500€ a été effectuée sur votre compte principal', time: 'Il y a 3 jours', date: '2023-06-25 16:45:00', unread: false, icon: 'fa-exchange-alt', color: 'bg-purple-500', link: '#'},
                            {id: 6, title: 'Mise à jour du système', message: 'FamFinance a été mis à jour avec de nouvelles fonctionnalités', time: 'La semaine dernière', date: '2023-06-22 08:00:00', unread: false, icon: 'fa-sync-alt', color: 'bg-indigo-500', link: '#'},
                            {id: 7, title: 'Anniversaire de compte', message: 'Voilà un an que vous utilisez FamFinance! Découvrez vos statistiques.', time: 'Le mois dernier', date: '2023-05-28 12:00:00', unread: false, icon: 'fa-birthday-cake', color: 'bg-pink-500', link: '#'}
                        ],
                        activeTab: 'all',
                        filterNotifications() {
                            if (this.activeTab === 'all') return this.notifications;
                            if (this.activeTab === 'unread') return this.notifications.filter(n => n.unread);
                            return this.notifications.filter(n => !n.unread);
                        },
                        markAllAsRead() {
                            this.notifications.forEach(n => n.unread = false);
                        },
                        markAsRead(id) {
                            const notification = this.notifications.find(n => n.id === id);
                            if (notification) notification.unread = false;
                        }
                    }">
                        <div class="mb-6 flex justify-between items-center">
                            <div class="flex space-x-1">
                                <button 
                                    @click="activeTab = 'all'" 
                                    :class="{'bg-indigo-600 text-white': activeTab === 'all', 'bg-gray-200 text-gray-700 hover:bg-gray-300': activeTab !== 'all'}"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Toutes <span class="ml-1 text-xs" x-text="notifications.length"></span>
                                </button>
                                <button 
                                    @click="activeTab = 'unread'" 
                                    :class="{'bg-indigo-600 text-white': activeTab === 'unread', 'bg-gray-200 text-gray-700 hover:bg-gray-300': activeTab !== 'unread'}"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Non lues <span class="ml-1 text-xs" x-text="notifications.filter(n => n.unread).length"></span>
                                </button>
                                <button 
                                    @click="activeTab = 'read'" 
                                    :class="{'bg-indigo-600 text-white': activeTab === 'read', 'bg-gray-200 text-gray-700 hover:bg-gray-300': activeTab !== 'read'}"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Lues <span class="ml-1 text-xs" x-text="notifications.filter(n => !n.unread).length"></span>
                                </button>
                            </div>
                            <button 
                                @click="markAllAsRead" 
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center"
                                x-show="notifications.some(n => n.unread)">
                                <i class="fas fa-check-double mr-1"></i> Tout marquer comme lu
                            </button>
                        </div>

                        <div class="bg-white shadow overflow-hidden rounded-md">
                            <ul class="divide-y divide-gray-200">
                                <template x-for="notification in filterNotifications()" :key="notification.id">
                                    <li :class="{'bg-indigo-50': notification.unread}" class="transition-colors duration-150">
                                        <a :href="notification.link" @click="markAsRead(notification.id)" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 flex items-start sm:px-6">
                                                <div class="flex-shrink-0 mt-1">
                                                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white" :class="notification.color">
                                                        <i class="fas" :class="notification.icon"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <div class="flex justify-between">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900 flex items-center">
                                                                <span x-text="notification.title"></span>
                                                                <span x-show="notification.unread" class="ml-2 h-2 w-2 rounded-full bg-indigo-600"></span>
                                                            </p>
                                                            <p class="mt-1 text-sm text-gray-600" x-text="notification.message"></p>
                                                        </div>
                                                        <p class="text-xs text-gray-500" x-text="notification.time"></p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-xs text-gray-500">
                                                        <span class="mr-2" x-text="notification.date"></span>
                                                        <button 
                                                            @click.stop="markAsRead(notification.id)" 
                                                            x-show="notification.unread"
                                                            class="ml-auto text-indigo-600 hover:text-indigo-800">
                                                            Marquer comme lu
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </template>
                                <template x-if="filterNotifications().length === 0">
                                    <li class="px-4 py-8 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-bell-slash text-3xl mb-3"></i>
                                            <p class="text-lg font-medium">Aucune notification</p>
                                            <p class="mt-1" x-show="activeTab === 'unread'">Vous avez lu toutes vos notifications.</p>
                                            <p class="mt-1" x-show="activeTab === 'all'">Vous n'avez pas encore reçu de notifications.</p>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Précédent</a>
                                <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Suivant</a>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Affichage de <span class="font-medium">1</span> à <span class="font-medium" x-text="filterNotifications().length"></span> sur <span class="font-medium" x-text="filterNotifications().length"></span> résultats
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                        <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <span class="sr-only">Précédent</span>
                                            <i class="fas fa-chevron-left h-5 w-5"></i>
                                        </a>
                                        <a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">1</a>
                                        <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <span class="sr-only">Suivant</span>
                                            <i class="fas fa-chevron-right h-5 w-5"></i>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 