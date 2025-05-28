<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de l\'activité') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <a href="{{ route('activities.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Retour à la liste') }}
                        </a>
                    </div>

                    <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ __('Informations sur l\'activité') }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                {{ __('Détails complets de l\'action effectuée.') }}
                            </p>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                            <dl class="sm:divide-y sm:divide-gray-200">
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('ID') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $activity->id }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('Date et heure') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $activity->created_at->format('d/m/Y H:i:s') }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('Utilisateur') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        @if($activity->user)
                                            {{ $activity->user->name }} ({{ $activity->user->email }})
                                        @else
                                            {{ __('Système') }}
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('Action') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $activity->action === 'create' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $activity->action === 'update' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $activity->action === 'delete' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $activity->action === 'view' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $activity->action === 'login' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                            {{ $activity->action === 'logout' ? 'bg-purple-100 text-purple-800' : '' }}
                                            ">
                                            {{ ucfirst($activity->action) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('Description') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $activity->description }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('Type d\'entité') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $activity->model_type ?? __('Non spécifié') }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('ID de l\'entité') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $activity->model_id ?? __('Non spécifié') }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('Adresse IP') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $activity->ip_address }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('Appareil') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ ucfirst($activity->device) }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ __('User Agent') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="text-xs">{{ $activity->user_agent }}</span>
                                    </dd>
                                </div>

                                @if($activity->old_values || $activity->new_values)
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">
                                            {{ __('Modifications') }}
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                {{ __('Champ') }}
                                                            </th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                {{ __('Ancienne valeur') }}
                                                            </th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                {{ __('Nouvelle valeur') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @if($activity->old_values && $activity->new_values)
                                                            @foreach(array_keys(array_merge($activity->old_values, $activity->new_values)) as $key)
                                                                <tr>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                        {{ $key }}
                                                                    </td>
                                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                                        @if(isset($activity->old_values[$key]))
                                                                            @if(is_array($activity->old_values[$key]))
                                                                                <pre class="text-xs">{{ json_encode($activity->old_values[$key], JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                {{ $activity->old_values[$key] }}
                                                                            @endif
                                                                        @else
                                                                            <span class="text-gray-400">{{ __('Non défini') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                                        @if(isset($activity->new_values[$key]))
                                                                            @if(is_array($activity->new_values[$key]))
                                                                                <pre class="text-xs">{{ json_encode($activity->new_values[$key], JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                {{ $activity->new_values[$key] }}
                                                                            @endif
                                                                        @else
                                                                            <span class="text-gray-400">{{ __('Non défini') }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @elseif($activity->old_values)
                                                            @foreach($activity->old_values as $key => $value)
                                                                <tr>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                        {{ $key }}
                                                                    </td>
                                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                                        @if(is_array($value))
                                                                            <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                                        <span class="text-gray-400">{{ __('Supprimé') }}</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @elseif($activity->new_values)
                                                            @foreach($activity->new_values as $key => $value)
                                                                <tr>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                        {{ $key }}
                                                                    </td>
                                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                                        <span class="text-gray-400">{{ __('Non défini') }}</span>
                                                                    </td>
                                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                                        @if(is_array($value))
                                                                            <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 