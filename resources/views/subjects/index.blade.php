<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b border-zinc-200 pb-4">
            <div>
                <x-breadcrumb :items="[['label' => 'Sujets']]" class="mb-2" />
                <h2 class="font-serif font-bold text-2xl text-zinc-800 tracking-tight flex items-center gap-2">
                    Liste des Sujets
                </h2>
            </div>
            @if(Auth::user()->hasRole('Etudiant'))
                <a href="{{ route('subjects.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 focus:ring-2 focus:ring-zinc-900 focus:ring-offset-1 text-white text-sm font-semibold rounded shadow-sm transition-colors">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Nouveau sujet</span>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 bg-zinc-50 min-h-screen">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Messages flash --}}
            @if(session('success'))
                <div class="mb-4 border-l-4 border-emerald-500 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm rounded shadow-sm" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 border-l-4 border-rose-500 bg-rose-50 text-rose-800 px-4 py-3 text-sm rounded shadow-sm" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if($subjects->total() > 0 || request()->hasAny(['search', 'status', 'department_id']))
                {{-- Compteurs par statut (Minimalistes) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white border border-zinc-200 border-l-2 border-l-amber-500 p-4 flex flex-col hover:border-zinc-300 transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">En attente</span>
                        <span class="text-2xl font-semibold text-zinc-800">{{ $counts['pending'] ?? 0 }}</span>
                    </div>
                    <div class="bg-white border border-zinc-200 border-l-2 border-l-emerald-500 p-4 flex flex-col hover:border-zinc-300 transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">Validés</span>
                        <span class="text-2xl font-semibold text-zinc-800">{{ $counts['validated'] ?? 0 }}</span>
                    </div>
                    <div class="bg-white border border-zinc-200 border-l-2 border-l-rose-500 p-4 flex flex-col hover:border-zinc-300 transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">Rejetés</span>
                        <span class="text-2xl font-semibold text-zinc-800">{{ $counts['rejected'] ?? 0 }}</span>
                    </div>
                </div>

                {{-- Barre d'outils dense --}}
                <div class="bg-white border border-zinc-200 p-3 mb-4 flex flex-wrap items-center justify-between gap-4">
                    <form method="GET" action="{{ route('subjects.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <x-icon name="magnifying-glass" class="w-4 h-4 text-zinc-400" />
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Recherche..."
                                class="w-full pl-9 pr-3 py-1.5 text-sm border border-zinc-200 focus:border-zinc-800 focus:ring-1 focus:ring-zinc-800 bg-zinc-50 rounded-none transition-colors">
                        </div>
                        
                        <select name="status" class="py-1.5 px-3 text-sm border border-zinc-200 focus:border-zinc-800 focus:ring-1 focus:ring-zinc-800 bg-zinc-50 rounded-none transition-colors w-full sm:w-auto">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Validés</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejetés</option>
                        </select>
                        
                        @if(Auth::user()->hasRole('Admin') && isset($departments) && $departments->count() > 0)
                            <select name="department_id" class="py-1.5 px-3 text-sm border border-zinc-200 focus:border-zinc-800 focus:ring-1 focus:ring-zinc-800 bg-zinc-50 rounded-none transition-colors w-full sm:w-auto">
                                <option value="">Toutes filières</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        @endif

                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 text-zinc-700 text-sm font-medium transition-colors focus:ring-2 focus:ring-zinc-800 focus:ring-offset-1 flex items-center gap-1.5">
                                <x-icon name="funnel" class="w-3.5 h-3.5" /> Filtrer
                            </button>
                            @if(request()->hasAny(['search', 'status', 'department_id']))
                                <a href="{{ route('subjects.index') }}" class="px-3 py-1.5 text-sm text-zinc-500 hover:text-zinc-800 hover:bg-zinc-100 font-medium transition-colors border border-transparent hover:border-zinc-200" title="Réinitialiser les filtres">
                                    Effacer
                                </a>
                            @endif
                        </div>
                    </form>

                    @hasanyrole('Admin|Chef de département')
                        <a href="{{ route('subjects.export', request()->query()) }}" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 text-sm font-medium transition-colors flex items-center gap-1.5 ml-auto">
                            <x-icon name="arrow-down-tray" class="w-3.5 h-3.5" /> Exporter
                        </a>
                    @endhasanyrole
                </div>

                {{-- Tableau Haute Densité --}}
                <div class="bg-white border border-zinc-200 overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead class="bg-zinc-50/80 border-b border-zinc-200 text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-3 py-2.5 border-r border-zinc-100">Sujet</th>
                                @unless(Auth::user()->hasRole('Etudiant'))
                                    <th class="px-3 py-2.5 border-r border-zinc-100">Étudiant</th>
                                @endunless
                                <th class="px-3 py-2.5 border-r border-zinc-100">Encadreur</th>
                                @if(Auth::user()->hasRole('Admin'))
                                    <th class="px-3 py-2.5 border-r border-zinc-100">Filière</th>
                                @endif
                                <th class="px-3 py-2.5 border-r border-zinc-100">Statut</th>
                                <th class="px-3 py-2.5 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 text-sm text-zinc-700">
                            @foreach($subjects as $subject)
                                <tr class="hover:bg-zinc-50/80 transition-colors group">
                                    <td class="px-3 py-2.5 max-w-md truncate whitespace-normal border-r border-zinc-100">
                                        <a href="{{ route('subjects.show', $subject) }}" class="font-medium text-zinc-900 group-hover:text-blue-700 group-hover:underline decoration-blue-300 underline-offset-2 transition-colors">
                                            {{ $subject->title }}
                                        </a>
                                        <div class="text-[11px] text-zinc-500 mt-0.5 truncate" title="{{ $subject->description }}">{{ Str::limit($subject->description, 70) }}</div>
                                    </td>
                                    @unless(Auth::user()->hasRole('Etudiant'))
                                        <td class="px-3 py-2.5 border-r border-zinc-100">
                                            <div class="font-medium text-zinc-800">{{ $subject->student->name ?? '—' }}</div>
                                            <div class="text-[11px] text-zinc-500">{{ $subject->student->email ?? '' }}</div>
                                        </td>
                                    @endunless
                                    <td class="px-3 py-2.5 font-medium text-zinc-600 border-r border-zinc-100">
                                        {{ $subject->teacher->name ?? '—' }}
                                    </td>
                                    @if(Auth::user()->hasRole('Admin'))
                                        <td class="px-3 py-2.5 text-[11px] text-zinc-600 border-r border-zinc-100">
                                            {{ $subject->department->name ?? '—' }}
                                        </td>
                                    @endif
                                    <td class="px-3 py-2.5 border-r border-zinc-100">
                                        @if($subject->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> En attente
                                            </span>
                                        @elseif($subject->status === 'validated')
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Validé
                                            </span>
                                        @elseif($subject->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-rose-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rejeté
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-[11px] text-zinc-500">
                                        {{ $subject->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination compacte --}}
                <div class="mt-4">
                    {{ $subjects->links() }}
                </div>
            @else
                {{-- Empty State Global --}}
                <div class="bg-white border border-zinc-200 p-12 text-center max-w-2xl mx-auto mt-12 shadow-sm">
                    <x-icon name="document-text" class="w-8 h-8 text-zinc-300 mx-auto mb-4" />
                    <h3 class="text-base font-bold text-zinc-800 mb-1">Aucun sujet</h3>
                    <p class="text-sm text-zinc-500 mb-6">
                        @if(Auth::user()->hasRole('Etudiant'))
                            Vous n'avez pas encore soumis de sujet de TFC.
                        @else
                            Aucun sujet n'est actuellement enregistré dans votre périmètre ou aucun résultat ne correspond à votre recherche.
                        @endif
                    </p>
                    @if(Auth::user()->hasRole('Etudiant'))
                        <a href="{{ route('subjects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 focus:ring-2 focus:ring-zinc-900 focus:ring-offset-1 text-white text-sm font-semibold transition-colors">
                            <x-icon name="plus" class="w-4 h-4" />
                            Nouveau sujet
                        </a>
                    @endif
                    @if(request()->hasAny(['search', 'status', 'department_id']))
                        <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-sm font-semibold transition-colors mt-4">
                            Effacer la recherche
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
