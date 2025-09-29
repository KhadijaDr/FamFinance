<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Objectif') }}: {{ $financialGoal->name }}
            </h2>
            <a href="{{ route('financial-goals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Retour aux objectifs') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informations sur l'objectif -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('Informations') }}</h3>
                            <div class="space-y-3 text-gray-700">
                                <p><strong>{{ __('Nom') }}:</strong> {{ $financialGoal->name }}</p>
                                <p><strong>{{ __('Montant cible') }}:</strong> <span class="font-semibold text-indigo-600">{{ number_format($financialGoal->target_amount, 2, ',', ' ') }} MAD</span></p>
                                <p><strong>{{ __('Montant actuel') }}:</strong> <span class="font-semibold text-green-600">{{ number_format($financialGoal->current_amount, 2, ',', ' ') }} MAD</span></p>
                                <p><strong>{{ __('Date de début') }}:</strong> {{ $financialGoal->start_date->format('d/m/Y') }}</p>
                                <p><strong>{{ __('Date cible') }}:</strong> {{ $financialGoal->target_date->format('d/m/Y') }}</p>
                                <p><strong>{{ __('Priorité') }}:</strong> 
                                    @php
                                        $priorityColor = $financialGoal->priority === 'high' ? 'bg-red-100 text-red-800' : ($financialGoal->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColor }}">
                                        {{ ucfirst($financialGoal->priority) }}
                                    </span>
                                </p>
                                <p><strong>{{ __('Statut') }}:</strong> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $financialGoal->is_completed ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $financialGoal->is_completed ? __('Atteint') : __('Actif') }}
                                    </span>
                                </p>

                                @if($financialGoal->notes)
                                    <div class="mt-6 p-4 bg-gray-50 rounded-md border border-gray-200">
                                        <p class="font-semibold text-gray-800">{{ __('Notes') }}:</p>
                                        <p class="text-gray-700 mt-1">{{ $financialGoal->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Progression et actions -->
                        <div>
                             <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('Progression & Délai') }}</h3>
                             @php
                                 $percentage = $financialGoal->target_amount > 0 ? min(100, ($financialGoal->current_amount / $financialGoal->target_amount) * 100) : 0;
                                 $progressBarColor = $financialGoal->is_completed ? 'bg-green-500' : 'bg-blue-500';
                             @endphp

                             <div class="mb-4">
                                 <div class="flex justify-between mb-1 text-sm text-gray-700">
                                     <span class="font-medium">{{ __('Progression') }}: {{ round($percentage) }}%</span>
                                     <span class="font-semibold">{{ number_format($financialGoal->current_amount, 2, ',', ' ') }} MAD / {{ number_format($financialGoal->target_amount, 2, ',', ' ') }} MAD</span>
                                 </div>
                                 <div class="w-full bg-gray-200 rounded-full h-3.5">
                                     <div class="{{ $progressBarColor }} h-3.5 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $percentage }}%"></div>
                                 </div>
                             </div>

                            @if($financialGoal->target_date)
                                <div class="mt-6 text-sm text-gray-700 p-4 bg-yellow-50 rounded-md border border-yellow-200">
                                    @php
                                        $diff = now()->diff($financialGoal->target_date);
                                        $parts = [];

                                        if ($diff->days > 0) {
                                            $parts[] = $diff->days . ' ' . ($diff->days > 1 ? __('jours') : __('jour'));
                                        }

                                        if ($diff->days == 0 || $diff->h > 0) {
                                             if ($diff->h > 0) {
                                                $parts[] = $diff->h . ' ' . ($diff->h > 1 ? __('heures') : __('heure'));
                                            }
                                             if ($diff->i > 0) {
                                                $parts[] = $diff->i . ' ' . ($diff->i > 1 ? __('minutes') : __('minute'));
                                             }
                                        }

                                        $remaining = implode(' et ', array_filter([implode(', ', array_slice($parts, 0, -1)), last($parts)]));

                                        if ($diff->invert) { // Date in the past
                                            if ($diff->days < 1) {
                                                 $message = __('Date limite dépassée de moins d\'un jour');
                                            } else {
                                                 $message = __('Date limite dépassée de') . ' ' . $remaining;
                                            }
                                        } else { // Date in the future or today
                                            if ($diff->days < 1 && count($parts) == 0) {
                                                 $message = __('Date limite aujourd\'hui');
                                            } elseif (empty($remaining)){
                                                 $message = __('Moins d\'une minute restante');
                                            }
                                             else {
                                                $message = __('Date limite dans') . ' ' . $remaining;
                                            }
                                        }
                                    @endphp
                                    <p class="font-semibold">{{ __('Temps restant') }}: <span class="{{ $diff->invert ? 'text-red-700' : 'text-green-700' }}">{{ $message }}</span></p>
                                </div>
                            @endif

                             <div class="mt-8 flex space-x-2">
                                 <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-contribution-{{ $financialGoal->id }}')" class="inline-flex items-center px-2 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                     <i class="fas fa-plus-circle mr-2"></i>
                                     {{ __('Ajouter une contribution') }}
                                 </button>
                                 <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-goal-{{ $financialGoal->id }}')" class="inline-flex items-center px-2 py-1.5 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                      <i class="fas fa-edit mr-2"></i>
                                     {{ __('Modifier') }}
                                 </button>
                                 <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-goal-{{ $financialGoal->id }}')" class="inline-flex items-center px-2 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                     <i class="fas fa-trash-alt mr-2"></i>
                                     {{ __('Supprimer') }}
                                 </button>
                             </div>
                        </div>
                    </div>

                    @if($transactions->count())
                        <div class="mt-12 pt-8 border-t border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('Transactions liées') }}</h3>
                            <div class="bg-gray-50 shadow overflow-hidden sm:rounded-md">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <li class="px-6 py-4 hover:bg-gray-100 transition duration-150 ease-in-out">
                                            <div class="flex items-center justify-between">
                                                <p class="text-base font-medium text-indigo-700 truncate">{{ $transaction->description }}</p>
                                                <div class="ml-4 flex-shrink-0 flex">
                                                    <p class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ number_format($transaction->amount, 2, ',', ' ') }} MAD
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2 sm:flex sm:justify-between text-sm text-gray-600">
                                                <div class="sm:flex space-x-4">
                                                    <p class="flex items-center">
                                                        <svg class="flex-shrink-0 mr-1 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y H:i') : __('Date inconnue') }}
                                                    </p>
                                                    <p class="flex items-center">
                                                         <svg class="flex-shrink-0 mr-1 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5.99L17 9v10a1 1 0 01-1 1H8a1 1 0 01-1-1V7z" />
                                                        </svg>
                                                        {{ $transaction->category->name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                             @if($transaction->notes)
                                                <p class="mt-2 text-sm text-gray-500">{{ __('Notes') }}: {{ $transaction->notes }}</p>
                                             @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for actions (ajouter contribution, modifier, supprimer) -->
    <x-modal name="add-contribution-{{ $financialGoal->id }}" :show="false" focusable>
        <form method="POST" action="{{ route('financial-goals.addContribution', $financialGoal) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ajouter une contribution à') }} "{{ $financialGoal->name }}"
            </h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="contribution_amount_{{ $financialGoal->id }}" :value="__('Montant')" />
                    <x-text-input id="contribution_amount_{{ $financialGoal->id }}" name="amount" type="number" step="0.01" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="contribution_date_{{ $financialGoal->id }}" :value="__('Date')" />
                    <x-text-input id="contribution_date_{{ $financialGoal->id }}" name="date" type="date" class="mt-1 block w-full" :value="date('Y-m-d')" required />
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="contribution_notes_{{ $financialGoal->id }}" :value="__('Notes (optionnel)')" />
                    <textarea id="contribution_notes_{{ $financialGoal->id }}" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
                 <div class="flex items-center mt-4">
                    <input id="create_transaction_{{ $financialGoal->id }}" name="create_transaction" type="checkbox" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="create_transaction_{{ $financialGoal->id }}" class="ml-2 text-sm text-gray-600">{{ __('Créer une transaction associée') }}</label>
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

    <x-modal name="edit-goal-{{ $financialGoal->id }}" :show="false" focusable>
        <form method="POST" action="{{ route('financial-goals.update', $financialGoal) }}" class="p-6">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Modifier l\'objectif') }}
            </h2>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="edit_name_{{ $financialGoal->id }}" :value="__('Nom')" />
                    <x-text-input id="edit_name_{{ $financialGoal->id }}" name="name" type="text" class="mt-1 block w-full" :value="$financialGoal->name" required />
                </div>

                <div>
                    <x-input-label for="edit_target_amount_{{ $financialGoal->id }}" :value="__('Montant cible')" />
                    <x-text-input id="edit_target_amount_{{ $financialGoal->id }}" name="target_amount" type="number" step="0.01" class="mt-1 block w-full" :value="$financialGoal->target_amount" required />
                </div>

                <div>
                    <x-input-label for="edit_current_amount_{{ $financialGoal->id }}" :value="__('Montant actuel')" />
                    <x-text-input id="edit_current_amount_{{ $financialGoal->id }}" name="current_amount" type="number" step="0.01" class="mt-1 block w-full" :value="$financialGoal->current_amount" required />
                </div>

                <div>
                    <x-input-label for="edit_start_date_{{ $financialGoal->id }}" :value="__('Date de début')" />
                    <x-text-input id="edit_start_date_{{ $financialGoal->id }}" name="start_date" type="date" class="mt-1 block w-full" :value="$financialGoal->start_date->format('Y-m-d')" required />
                </div>

                <div>
                    <x-input-label for="edit_target_date_{{ $financialGoal->id }}" :value="__('Date cible')" />
                    <x-text-input id="edit_target_date_{{ $financialGoal->id }}" name="target_date" type="date" class="mt-1 block w-full" :value="$financialGoal->target_date->format('Y-m-d')" required />
                </div>

                <div>
                    <x-input-label for="edit_priority_{{ $financialGoal->id }}" :value="__('Priorité')" />
                    <select id="edit_priority_{{ $financialGoal->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="low" {{ $financialGoal->priority === 'low' ? 'selected' : '' }}>{{ __('Basse') }}</option>
                        <option value="medium" {{ $financialGoal->priority === 'medium' ? 'selected' : '' }}>{{ __('Moyenne') }}</option>
                        <option value="high" {{ $financialGoal->priority === 'high' ? 'selected' : '' }}>{{ __('Haute') }}</option>
                    </select>
                </div>

                <div>
                    <div class="flex items-center mt-4">
                        <input id="edit_is_completed_{{ $financialGoal->id }}" name="is_completed" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $financialGoal->is_completed ? 'checked' : '' }}>
                        <label for="edit_is_completed_{{ $financialGoal->id }}" class="ml-2 text-sm text-gray-600">{{ __('Objectif atteint') }}</label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="edit_notes_{{ $financialGoal->id }}" :value="__('Notes (optionnel)')" />
                    <textarea id="edit_notes_{{ $financialGoal->id }}" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $financialGoal->notes }}</textarea>
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

    <x-modal name="delete-goal-{{ $financialGoal->id }}" :show="false" focusable>
        <form method="POST" action="{{ route('financial-goals.destroy', $financialGoal) }}" class="p-6">
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

</x-app-layout> 