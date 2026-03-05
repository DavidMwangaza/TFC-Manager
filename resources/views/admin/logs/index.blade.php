<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center gap-2">
            <x-icon name="clock" class="w-6 h-6" /> Journal d'Activité
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Activités']]" />
            <div class="flex gap-6">
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                <div class="flex-1 space-y-6">
                    {{-- Filtres --}}
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <select name="action" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Toutes les actions</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Du"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Au"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700 transition">Filtrer</button>
                                <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition">Reset</a>
                            </div>
                        </form>
                    </div>

                    {{-- Tableau des logs --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($logs as $log)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $log->user?->name ?? 'Système' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @php
                                                    $actionColors = [
                                                        'created' => 'bg-green-100 text-green-800',
                                                        'updated' => 'bg-blue-100 text-blue-800',
                                                        'deleted' => 'bg-red-100 text-red-800',
                                                        'blocked' => 'bg-red-100 text-red-800',
                                                        'unblocked' => 'bg-green-100 text-green-800',
                                                        'login' => 'bg-indigo-100 text-indigo-800',
                                                        'logout' => 'bg-gray-100 text-gray-800',
                                                        'password_reset' => 'bg-yellow-100 text-yellow-800',
                                                        'role_changed' => 'bg-purple-100 text-purple-800',
                                                        'year_closed' => 'bg-orange-100 text-orange-800',
                                                    ];
                                                    $aColor = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $aColor }}">
                                                    {{ $log->action }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 max-w-md truncate">
                                                {{ $log->description }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">
                                                {{ $log->ip_address ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                Aucune activité enregistrée pour le moment.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="px-4 py-3 border-t">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
