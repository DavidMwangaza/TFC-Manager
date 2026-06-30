<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight inline-flex items-center gap-2">
                <x-icon name="calendar" class="w-6 h-6" /> Années Académiques
            </h2>
            <a href="{{ route('admin.academic-years.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 focus:ring-2 focus:ring-zinc-900 focus:ring-offset-1 text-white text-sm font-semibold rounded shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nouvelle année
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Années Académiques']]" />
            <div class="flex gap-6">

                <div class="flex-1 space-y-6">
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">{{ session('error') }}</div>
                    @endif

                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Année</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Début</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fin</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Sujets</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Statut</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @forelse($years as $year)
                                        <tr class="hover:bg-slate-50 {{ $year->is_current ? 'bg-blue-50' : '' }}">
                                            <td class="px-4 py-3 text-sm font-medium text-slate-900">
                                                {{ $year->name }}
                                                @if($year->is_current)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">En cours</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $year->start_date->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $year->end_date->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $year->subjects_count }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($year->is_closed)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800"><x-icon name="lock-closed" class="w-3.5 h-3.5" /> Clôturée</span>
                                                @elseif($year->is_current)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><x-icon name="check-circle" class="w-3.5 h-3.5" /> Active</span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800"><x-icon name="clock" class="w-3.5 h-3.5" /> Inactive</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right">
                                                <div class="flex justify-end gap-1">
                                                    @if(!$year->is_current && !$year->is_closed)
                                                        <form method="POST" action="{{ route('admin.academic-years.set-current', $year) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100" title="Définir comme année en cours">
                                                                Activer
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if(!$year->is_closed)
                                                        <form method="POST" action="{{ route('admin.academic-years.close', $year) }}" class="inline"
                                                              onsubmit="return confirm('Clôturer l\'année {{ $year->name }} ? Les TFC seront archivés.')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="px-2 py-1 text-xs bg-orange-50 text-orange-700 rounded hover:bg-orange-100" title="Clôturer">
                                                                Clôturer
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($year->subjects_count === 0)
                                                        <form method="POST" action="{{ route('admin.academic-years.destroy', $year) }}" class="inline"
                                                              onsubmit="return confirm('Supprimer l\'année {{ $year->name }} ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-2 py-1 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100" title="Supprimer">
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                                Aucune année académique configurée.
                                                <a href="{{ route('admin.academic-years.create') }}" class="text-blue-600 hover:underline">Créer la première</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
