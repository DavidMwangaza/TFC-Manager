<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Archives des Travaux D&eacute;fendus &mdash; UDBL TFC Manager</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- BARRE INSTITUTIONNELLE --}}
    <div class="bg-blue-900 text-blue-100 text-xs py-1.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <span>Universit&eacute; Don Bosco de Lubumbashi</span>
            <span>Archives des Travaux D&eacute;fendus</span>
        </div>
    </div>

    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-3 hover:opacity-80 transition">
                    <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-10 w-10 object-contain">
                    <div>
                        <span class="text-lg font-bold text-gray-800 block leading-tight">TFC Manager</span>
                        <span class="text-xs text-gray-400 leading-tight">Archives des Travaux</span>
                    </div>
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900 font-medium py-2 px-4 text-sm transition">
                        &larr; Accueil
                    </a>
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-5 rounded-lg text-sm transition">
                            Mon espace
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-5 rounded-lg text-sm transition">
                            Connexion
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- EN-TETE --}}
    <section class="bg-gradient-to-br from-blue-800 via-blue-700 to-blue-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3">
                <svg class="w-8 h-8 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                Archives des Travaux D&eacute;fendus
            </h1>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                Consultez l'ensemble des travaux de fin de cycle ayant &eacute;t&eacute; d&eacute;fendus avec succ&egrave;s &agrave; l'Universit&eacute; Don Bosco de Lubumbashi.
            </p>
        </div>
    </section>

    {{-- FILTRES --}}
    <section class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <form method="GET" action="{{ route('archives.index') }}">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre du travail ou nom de l'&eacute;tudiant..."
                            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Fili&egrave;re</label>
                        <select name="department_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Toutes</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Ann&eacute;e acad&eacute;mique</label>
                        <select name="academic_year_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Toutes</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                        <select name="subject_type" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tous</option>
                            <option value="tfc" {{ request('subject_type') === 'tfc' ? 'selected' : '' }}>TFC</option>
                            <option value="memoire" {{ request('subject_type') === 'memoire' ? 'selected' : '' }}>M&eacute;moire</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-md text-sm font-semibold hover:bg-blue-800 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            Rechercher
                        </button>
                        @if(request()->hasAny(['search', 'department_id', 'academic_year_id', 'subject_type']))
                            <a href="{{ route('archives.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition">
                                R&eacute;initialiser
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- RESULTATS --}}
    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-800">{{ $subjects->total() }}</span> travail{{ $subjects->total() > 1 ? 'x' : '' }} d&eacute;fendu{{ $subjects->total() > 1 ? 's' : '' }}
                </p>
            </div>

            @if($subjects->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-1">Aucun travail trouv&eacute;</h3>
                    <p class="text-sm text-gray-500">Aucun travail d&eacute;fendu ne correspond &agrave; vos crit&egrave;res de recherche.</p>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N&deg;</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Titre du travail</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">&Eacute;tudiant</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Directeur</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fili&egrave;re</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ann&eacute;e</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">T&eacute;l&eacute;charger</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($subjects as $index => $subject)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm text-gray-400 whitespace-nowrap">{{ $subjects->firstItem() + $index }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 max-w-xs">
                                            <span class="line-clamp-2">{{ $subject->title }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $subject->student->name ?? '&mdash;' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $subject->teacher->name ?? 'Non assign&eacute;' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $subject->department->name ?? '&mdash;' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $subject->academicYear->name ?? '&mdash;' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($subject->subject_type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $subject->subject_type === 'tfc' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $subject->subject_type === 'tfc' ? 'TFC' : 'M&eacute;moire' }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">&mdash;</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @php $finalFile = $subject->thesisFiles->firstWhere('version_type', 'final'); @endphp
                                            @if($finalFile)
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('archives.view', $finalFile) }}"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-md hover:bg-green-700 transition"
                                                       title="Visualiser la version finale">
                                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2.5 12s3.5-7.5 9.5-7.5S21.5 12 21.5 12s-3.5 7.5-9.5 7.5S2.5 12 2.5 12z" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5"/></svg>
                                                        Voir
                                                    </a>

                                                    <a href="{{ route('archives.download', $finalFile) }}"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-md hover:bg-blue-700 transition"
                                                       title="T&eacute;l&eacute;charger la version finale">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                                        PDF
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-gray-300 text-xs">&mdash;</span>
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

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-9 w-9 object-contain">
                        <span class="text-base font-bold text-white">UDBL TFC Manager</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Plateforme interne de gestion des Travaux de Fin de Cycle &mdash;
                        Universit&eacute; Don Bosco de Lubumbashi.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3 text-sm">Facult&eacute;s</h4>
                    <ul class="space-y-1.5 text-sm">
                        <li>ESIS &mdash; Sciences Informatiques</li>
                        <li>ECOPO &mdash; &Eacute;conomie &amp; Finance</li>
                        <li>KANSEBULA &mdash; Sciences Humaines</li>
                        <li>THEOLOGICUM &mdash; Th&eacute;ologie</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3 text-sm">Contact</h4>
                    <ul class="space-y-1.5 text-sm">
                        <li>Lubumbashi, R&eacute;publique D&eacute;mocratique du Congo</li>
                        <li>Universit&eacute; Don Bosco</li>
                        <li>info@udbl.ac.cd</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Universit&eacute; Don Bosco de Lubumbashi &mdash; Plateforme TFC Manager. Usage interne.
            </div>
        </div>
    </footer>

</body>
</html>
