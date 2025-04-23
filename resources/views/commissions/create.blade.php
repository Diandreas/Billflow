<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-500 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    {{ __('Créer une nouvelle commission') }}
                </h2>
                <a href="{{ route('commissions.index') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-purple-700 rounded-md hover:bg-purple-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    @if (session('status'))
                        <div class="mb-2 bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm rounded" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('commissions.store') }}" method="POST" class="space-y-3">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Vendeur -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Vendeur') }}</label>
                                <select id="user_id" name="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('user_id') border-red-300 @enderror" required>
                                    <option value="">{{ __('Sélectionnez un vendeur') }}</option>
                                    @foreach ($sellers as $seller)
                                        <option value="{{ $seller->id }}" {{ old('user_id') == $seller->id ? 'selected' : '' }}>
                                            {{ $seller->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Boutique -->
                            <div>
                                <label for="shop_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Boutique') }}</label>
                                <select id="shop_id" name="shop_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('shop_id') border-red-300 @enderror" required>
                                    <option value="">{{ __('Sélectionnez une boutique') }}</option>
                                    @foreach ($shops as $shop)
                                        <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                            {{ $shop->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('shop_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- Montant -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Montant') }}</label>
                                <div class="relative">
                                    <input type="number" min="0" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" class="w-full pr-8 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('amount') border-red-300 @enderror" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <span class="text-gray-500">€</span>
                                    </div>
                                </div>
                                @error('amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mois -->
                            <div>
                                <label for="period_month" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Mois') }}</label>
                                <select id="period_month" name="period_month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('period_month') border-red-300 @enderror" required>
                                    <option value="">{{ __('Sélectionnez un mois') }}</option>
                                    <option value="Janvier" {{ old('period_month') == 'Janvier' ? 'selected' : '' }}>{{ __('Janvier') }}</option>
                                    <option value="Février" {{ old('period_month') == 'Février' ? 'selected' : '' }}>{{ __('Février') }}</option>
                                    <option value="Mars" {{ old('period_month') == 'Mars' ? 'selected' : '' }}>{{ __('Mars') }}</option>
                                    <option value="Avril" {{ old('period_month') == 'Avril' ? 'selected' : '' }}>{{ __('Avril') }}</option>
                                    <option value="Mai" {{ old('period_month') == 'Mai' ? 'selected' : '' }}>{{ __('Mai') }}</option>
                                    <option value="Juin" {{ old('period_month') == 'Juin' ? 'selected' : '' }}>{{ __('Juin') }}</option>
                                    <option value="Juillet" {{ old('period_month') == 'Juillet' ? 'selected' : '' }}>{{ __('Juillet') }}</option>
                                    <option value="Août" {{ old('period_month') == 'Août' ? 'selected' : '' }}>{{ __('Août') }}</option>
                                    <option value="Septembre" {{ old('period_month') == 'Septembre' ? 'selected' : '' }}>{{ __('Septembre') }}</option>
                                    <option value="Octobre" {{ old('period_month') == 'Octobre' ? 'selected' : '' }}>{{ __('Octobre') }}</option>
                                    <option value="Novembre" {{ old('period_month') == 'Novembre' ? 'selected' : '' }}>{{ __('Novembre') }}</option>
                                    <option value="Décembre" {{ old('period_month') == 'Décembre' ? 'selected' : '' }}>{{ __('Décembre') }}</option>
                                </select>
                                @error('period_month')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Année -->
                            <div>
                                <label for="period_year" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Année') }}</label>
                                <select id="period_year" name="period_year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('period_year') border-red-300 @enderror" required>
                                    <option value="">{{ __('Sélectionnez une année') }}</option>
                                    @for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ old('period_year', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('period_year')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Créer la commission') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 