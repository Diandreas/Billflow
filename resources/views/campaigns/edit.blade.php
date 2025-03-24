<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier la campagne') }}
            </h2>
            <a href="{{ route('campaigns.show', $campaign) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <form action="{{ route('campaigns.update', $campaign) }}" method="POST" id="campaignForm" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom de la campagne</label>
                                <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Nom de la campagne" value="{{ old('name', $campaign->name) }}" required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type de campagne</label>
                                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">-- Sélectionnez un type --</option>
                                    <option value="birthday" {{ old('type', $campaign->type) == 'birthday' ? 'selected' : '' }}>Anniversaire</option>
                                    <option value="holiday" {{ old('type', $campaign->type) == 'holiday' ? 'selected' : '' }}>Jour férié/Fête</option>
                                    <option value="promotion" {{ old('type', $campaign->type) == 'promotion' ? 'selected' : '' }}>Promotion</option>
                                    <option value="custom" {{ old('type', $campaign->type) == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea name="message" id="message" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Votre message SMS (max 160 caractères)" required>{{ old('message', $campaign->message) }}</textarea>
                                <div class="mt-1 flex justify-between">
                                    <span id="charCount" class="text-xs text-gray-500">0/160 caractères</span>
                                    <div id="messageVariables" class="text-xs text-blue-500">
                                        Variables: <span class="cursor-pointer" onclick="addVariable('#nom#')">Nom</span>, 
                                        <span class="cursor-pointer" onclick="addVariable('#date#')">Date</span>
                                    </div>
                                </div>
                                @error('message')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="target_segments" class="block text-sm font-medium text-gray-700">Ciblage (optionnel)</label>
                                    <span class="text-xs text-gray-500">{{ count($clients) }} clients disponibles</span>
                                </div>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="target_segments[]" id="segment_all" value="all" {{ in_array('all', old('target_segments', $campaign->target_segments ?? [])) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="segment_all" class="ml-2 text-sm text-gray-700">Tous les clients</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="target_segments[]" id="segment_recent" value="recent" {{ in_array('recent', old('target_segments', $campaign->target_segments ?? [])) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="segment_recent" class="ml-2 text-sm text-gray-700">Clients récents (3 derniers mois)</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="target_segments[]" id="segment_birthday" value="birthday_this_month" {{ in_array('birthday_this_month', old('target_segments', $campaign->target_segments ?? [])) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="segment_birthday" class="ml-2 text-sm text-gray-700">Anniversaires du mois</label>
                                    </div>
                                </div>
                                @error('target_segments')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="scheduled_at" class="flex items-center">
                                    <input type="checkbox" id="schedule_toggle" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $campaign->scheduled_at ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Planifier l'envoi</span>
                                </label>
                                <div id="schedule_fields" class="mt-2 {{ $campaign->scheduled_at ? '' : 'hidden' }}">
                                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('scheduled_at')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-between pt-4">
                                <button type="button" onclick="window.location.href='{{ route('campaigns.show', $campaign) }}'" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Annuler
                                </button>
                                <div class="flex space-x-2">
                                    <button type="submit" name="save_draft" value="1" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Enregistrer comme brouillon
                                    </button>
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageField = document.getElementById('message');
            const charCount = document.getElementById('charCount');
            const scheduleToggle = document.getElementById('schedule_toggle');
            const scheduleFields = document.getElementById('schedule_fields');

            // Compteur de caractères
            function updateCharCount() {
                const count = messageField.value.length;
                charCount.textContent = `${count}/160 caractères`;
                if (count > 160) {
                    charCount.classList.add('text-red-500');
                    charCount.classList.remove('text-gray-500');
                } else {
                    charCount.classList.add('text-gray-500');
                    charCount.classList.remove('text-red-500');
                }
            }

            messageField.addEventListener('input', updateCharCount);
            updateCharCount(); // Exécute immédiatement pour afficher le compte initial

            // Toggle champs de planification
            scheduleToggle.addEventListener('change', function() {
                if (this.checked) {
                    scheduleFields.classList.remove('hidden');
                } else {
                    scheduleFields.classList.add('hidden');
                    document.getElementById('scheduled_at').value = '';
                }
            });
        });

        // Fonction pour ajouter des variables au message
        function addVariable(variable) {
            const messageField = document.getElementById('message');
            const currentPos = messageField.selectionStart;
            const text = messageField.value;
            
            messageField.value = text.substring(0, currentPos) + variable + text.substring(currentPos);
            
            // Mettre à jour le compteur de caractères
            const charCount = document.getElementById('charCount');
            const count = messageField.value.length;
            charCount.textContent = `${count}/160 caractères`;
            
            // Replacer le curseur après la variable insérée
            messageField.focus();
            messageField.selectionStart = currentPos + variable.length;
            messageField.selectionEnd = currentPos + variable.length;
        }
    </script>
    @endpush
</x-app-layout> 