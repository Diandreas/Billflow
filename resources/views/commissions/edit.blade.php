<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier la commission') }}
            </h2>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('commissions.update', $commission) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- User ID (Vendor) -->
                        <div>
                            <x-input-label for="user_id" :value="__('Vendeur')" />
                            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                @foreach ($sellers as $seller)
                                    <option value="{{ $seller->id }}" {{ $commission->user_id == $seller->id ? 'selected' : '' }}>
                                        {{ $seller->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <!-- Shop ID -->
                        <div>
                            <x-input-label for="shop_id" :value="__('Boutique')" />
                            <select id="shop_id" name="shop_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->id }}" {{ $commission->shop_id == $shop->id ? 'selected' : '' }}>
                                        {{ $shop->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('shop_id')" class="mt-2" />
                        </div>

                        <!-- Amount -->
                        <div>
                            <x-input-label for="amount" :value="__('Montant')" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">FCFA</span>
                                </div>
                                <x-text-input id="amount" name="amount" type="number" min="0" step="0.01" class="pl-16 mt-1 block w-full" value="{{ old('amount', $commission->amount) }}" required />
                            </div>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <!-- Period Month and Year -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="period_month" :value="__('Mois de la période')" />
                                <select id="period_month" name="period_month" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                    <option value="01" {{ $commission->period_month == '01' ? 'selected' : '' }}>{{ __('Janvier') }}</option>
                                    <option value="02" {{ $commission->period_month == '02' ? 'selected' : '' }}>{{ __('Février') }}</option>
                                    <option value="03" {{ $commission->period_month == '03' ? 'selected' : '' }}>{{ __('Mars') }}</option>
                                    <option value="04" {{ $commission->period_month == '04' ? 'selected' : '' }}>{{ __('Avril') }}</option>
                                    <option value="05" {{ $commission->period_month == '05' ? 'selected' : '' }}>{{ __('Mai') }}</option>
                                    <option value="06" {{ $commission->period_month == '06' ? 'selected' : '' }}>{{ __('Juin') }}</option>
                                    <option value="07" {{ $commission->period_month == '07' ? 'selected' : '' }}>{{ __('Juillet') }}</option>
                                    <option value="08" {{ $commission->period_month == '08' ? 'selected' : '' }}>{{ __('Août') }}</option>
                                    <option value="09" {{ $commission->period_month == '09' ? 'selected' : '' }}>{{ __('Septembre') }}</option>
                                    <option value="10" {{ $commission->period_month == '10' ? 'selected' : '' }}>{{ __('Octobre') }}</option>
                                    <option value="11" {{ $commission->period_month == '11' ? 'selected' : '' }}>{{ __('Novembre') }}</option>
                                    <option value="12" {{ $commission->period_month == '12' ? 'selected' : '' }}>{{ __('Décembre') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('period_month')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="period_year" :value="__('Année de la période')" />
                                <select id="period_year" name="period_year" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                    @for ($year = date('Y'); $year >= date('Y') - 3; $year--)
                                        <option value="{{ $year }}" {{ $commission->period_year == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('period_year')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">{{ old('notes', $commission->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end">
                            <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                                {{ __('Annuler') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Enregistrer les modifications') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 