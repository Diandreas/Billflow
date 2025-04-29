@extends('layouts.app')

@section('page_name', 'system_import_confirm')

@section('content')
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Confirmation d\'Importation du Système') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6 bg-red-50 dark:bg-red-900 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm leading-5 font-medium text-red-800 dark:text-red-200">
                                {{ __('Attention : Opération Irréversible !') }}
                            </h3>
                            <div class="mt-2 text-sm leading-5 text-red-700 dark:text-red-300">
                                <p>{{ __('Cette opération va remplacer TOUTES les données actuelles du système. Cette action est irréversible.') }}</p>
                                <p class="mt-1">{{ __('Assurez-vous d\'avoir fait une sauvegarde de vos données actuelles avant de continuer.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Détails du fichier d\'importation') }}
                </h3>

                <div class="mb-6">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Version') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $metadata['version'] ?? 'Non spécifiée' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date de création') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ isset($metadata['date']) ? \Carbon\Carbon::parse($metadata['date'])->format('d/m/Y H:i:s') : 'Non spécifiée' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tables à importer') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ count($tables) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <h4 class="font-medium text-gray-800 dark:text-gray-200 mb-2">{{ __('Tables contenues dans la sauvegarde') }}</h4>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mb-6">
                        @foreach ($tables as $table)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded px-3 py-2 text-sm">
                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $table }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $metadata['tables'][$table]['count'] ?? 0 }} {{ __('enregistrements') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
                    <a href="{{ route('system.export-import') }}" class="inline-flex justify-center items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Annuler') }}
                    </a>

                    <form action="{{ route('system.import.confirm') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            {{ __('Confirmer l\'Importation') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
