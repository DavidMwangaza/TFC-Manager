<x-app-layout>
    <x-slot name="header">
        Archives Publiques
    </x-slot>

    {{-- EN-TETE --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-950 py-10 lg:py-16 text-white rounded-2xl mb-6 shadow-sm">
        <!-- Abstract glowing circles -->
        <div class="absolute -right-24 -top-24 w-96 h-96 bg-accent/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-24 -bottom-24 w-96 h-96 bg-primary-light/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight tracking-tight text-white mb-4">
                <svg class="w-8 h-8 lg:w-10 lg:h-10 inline-block mr-2 -mt-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                Archives des <span class="text-accent bg-clip-text">Travaux Défendus</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Consultez l'ensemble des travaux de fin de cycle ayant été défendus avec succès à l'Université Don Bosco de Lubumbashi.
            </p>
        </div>
    </section>

    {{-- FILTRES --}}
    <section class="bg-white border border-slate-200 shadow-sm rounded-2xl mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('archives.index') }}">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre du travail ou nom de l'&eacute;tudiant..."
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="w-48">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Fili&egrave;re</label>
                        <select name="department_id" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Toutes</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-48">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Ann&eacute;e acad&eacute;mique</label>
                        <select name="academic_year_id" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Toutes</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-36">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
                        <select name="subject_type" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tous</option>
                            <option value="tfc" {{ request('subject_type') === 'tfc' ? 'selected' : '' }}>TFC</option>
                            <option value="memoire" {{ request('subject_type') === 'memoire' ? 'selected' : '' }}>M&eacute;moire</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 mb-2 ml-2">
                        <input type="checkbox" name="semantic" id="semantic" value="1" {{ request('semantic') ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="semantic" class="text-sm font-medium text-slate-700 cursor-pointer flex items-center gap-1" title="Utilise l'algorithme TF-IDF et l'extension par synonymes (Concepts)">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            Recherche S&eacute;mantique NLP
                        </label>
                    </div>
                    <div class="flex gap-3 mb-0.5">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            Rechercher
                        </button>
                        @if(request()->hasAny(['search', 'department_id', 'academic_year_id', 'subject_type', 'semantic']))
                            <a href="{{ route('archives.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-700 rounded-xl text-sm font-bold border border-slate-200 shadow-sm hover:bg-slate-50 hover:-translate-y-0.5 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                R&eacute;initialiser
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- RESULTATS --}}
    <section>
        <div>

            <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
                <p class="text-sm text-slate-500">
                    <span class="font-semibold text-slate-800">{{ $subjects->total() }}</span> travail{{ $subjects->total() > 1 ? 'x' : '' }} d&eacute;fendu{{ $subjects->total() > 1 ? 's' : '' }}
                </p>
                {{-- Lien OAI-PMH pour les répertoires institutionnels --}}
                <a href="{{ route('archives.oai') }}"
                   target="_blank"
                   title="Exporter les métadonnées au format XML OAI-PMH (Dublin Core) pour moissonneur institutionnel"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-300 bg-white text-xs text-slate-600 hover:bg-slate-50 hover:border-blue-400 hover:text-blue-700 transition font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                    </svg>
                    Export XML OAI-PMH
                </a>
            </div>

            @if($subjects->isEmpty())
                <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <h3 class="text-lg font-semibold text-slate-700 mb-1">Aucun travail trouv&eacute;</h3>
                    <p class="text-sm text-slate-500">Aucun travail d&eacute;fendu ne correspond &agrave; vos crit&egrave;res de recherche.</p>
                </div>
            @else
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">N&deg;</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Titre du travail</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">&Eacute;tudiant</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Directeur</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Fili&egrave;re</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ann&eacute;e</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                                    @if(isset($semanticScores))
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Score NLP</th>
                                    @endif
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">T&eacute;l&eacute;charger</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($subjects as $index => $subject)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-sm text-slate-400 whitespace-nowrap">{{ $subjects->firstItem() + $index }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-slate-900 max-w-xs">
                                            <span class="line-clamp-2">{{ $subject->title }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $subject->student->name ?? '&mdash;' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $subject->teacher->name ?? 'Non assign&eacute;' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $subject->department->name ?? '&mdash;' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-500 whitespace-nowrap">{{ $subject->academicYear->name ?? '&mdash;' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($subject->subject_type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $subject->subject_type === 'tfc' ? 'bg-blue-100 text-blue-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $subject->subject_type === 'tfc' ? 'TFC' : 'M&eacute;moire' }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs">&mdash;</span>
                                            @endif
                                        </td>
                                        @if(isset($semanticScores))
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                    {{ $semanticScores[$subject->id] ?? 0 }}%
                                                </span>
                                            </td>
                                        @endif
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @php $finalFile = $subject->thesisFiles->firstWhere('version_type', 'final'); @endphp
                                            @if($finalFile)
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('archives.view', $finalFile) }}"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg shadow-sm shadow-green-500/30 hover:bg-green-500 hover:-translate-y-0.5 transition-all"
                                                       title="Visualiser la version finale">
                                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.5 12s3.5-7.5 9.5-7.5S21.5 12 21.5 12s-3.5 7.5-9.5 7.5S2.5 12 2.5 12z" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5"/></svg>
                                                        Voir
                                                    </a>

                                                    <a href="{{ route('archives.download', $finalFile) }}"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg shadow-sm shadow-blue-500/30 hover:bg-blue-500 hover:-translate-y-0.5 transition-all"
                                                       title="T&eacute;l&eacute;charger la version finale">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                                        PDF
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-slate-300 text-xs">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $subjects->links() }}
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
