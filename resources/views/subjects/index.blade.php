<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center gap-2">
                <x-icon name="clipboard-document-list" class="w-6 h-6" /> Liste des Sujets
            </h2>
            @if(Auth::user()->hasRole('Etudiant'))
                <a href="{{ route('subjects.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-800 transition">
                    + Proposer un sujet
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <x-breadcrumb :items="[['label' => 'Sujets']]" />
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Messages flash --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Barre de recherche et filtres --}}
                    <form method="GET" action="{{ route('subjects.index') }}" class="mb-6">
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre ou nom de l'étudiant..."
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="w-40">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                                <select name="status" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Tous</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Validés</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejetés</option>
                                </select>
                            </div>
                            @if(Auth::user()->hasRole('Admin') && $departments->count() > 0)
                                <div class="w-48">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Filière</label>
                                    <select name="department_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Toutes</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700 transition inline-flex items-center gap-1.5">
                                <x-icon name="funnel" class="w-4 h-4" /> Filtrer
                            </button>
                            @if(request()->hasAny(['search', 'status', 'department_id']))
                                <a href="{{ route('subjects.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">✕ Réinitialiser</a>
                            @endif
                            @hasanyrole('Admin|Chef Departement')
                            <a href="{{ route('subjects.export', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition ml-auto inline-flex items-center gap-1.5">
                                <x-icon name="arrow-down-tray" class="w-4 h-4" /> Exporter CSV
                            </a>
                            @endhasanyrole
                        </div>
                    </form>

                    @if($subjects->total() === 0)
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun sujet</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(Auth::user()->hasRole('Etudiant'))
                                    Vous n'avez pas encore soumis de sujet de TFC.
                                @else
                                    Aucun sujet n'a &eacute;t&eacute; trouv&eacute; pour votre p&eacute;rim&egrave;tre.
                                @endif
                            </p>
                            @if(Auth::user()->hasRole('Etudiant'))
                                <div class="mt-6">
                                    <a href="{{ route('subjects.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-800 transition">
                                        + Proposer un sujet
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Compteurs par statut --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $counts['pending'] }}</div>
                                <div class="text-sm text-gray-600">En attente</div>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $counts['validated'] }}</div>
                                <div class="text-sm text-gray-600">Valid&eacute;s</div>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $counts['rejected'] }}</div>
                                <div class="text-sm text-gray-600">Rejet&eacute;s</div>
                            </div>
                        </div>

                        {{-- Tableau des sujets --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Titre
                                        </th>
                                        @unless(Auth::user()->hasRole('Etudiant'))
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            &Eacute;tudiant
                                        </th>
                                        @endunless
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Encadreur
                                        </th>
                                        @if(Auth::user()->hasRole('Admin'))
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Filière
                                        </th>
                                        @endif
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subjects as $subject)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <a href="{{ route('subjects.show', $subject) }}" class="text-sm font-medium text-blue-700 hover:text-blue-900 hover:underline">{{ $subject->title }}</a>
                                                <div class="text-sm text-gray-500">{{ Str::limit($subject->description, 80) }}</div>
                                            </td>
                                            @unless(Auth::user()->hasRole('Etudiant'))
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $subject->student->name ?? '—' }}</div>
                                                <div class="text-xs text-gray-500">{{ $subject->student->email ?? '' }}</div>
                                            </td>
                                            @endunless
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $subject->teacher->name ?? 'Non assign&eacute;' }}</div>
                                            </td>
                                            @if(Auth::user()->hasRole('Admin'))
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $subject->department->name ?? '—' }}</div>
                                            </td>
                                            @endif
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($subject->status === 'pending')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <x-icon name="clock" class="w-3.5 h-3.5" /> En attente
                                                    </span>
                                                @elseif($subject->status === 'validated')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <x-icon name="check-circle" class="w-3.5 h-3.5" /> Valid&eacute;
                                                    </span>
                                                @elseif($subject->status === 'rejected')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <x-icon name="x-circle" class="w-3.5 h-3.5" /> Rejet&eacute;
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $subject->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>

                                        {{-- Afficher le motif de rejet si applicable --}}
                                        @if($subject->status === 'rejected' && $subject->rejection_reason)
                                            <tr class="bg-red-50">
                                                <td colspan="6" class="px-6 py-2">
                                                    <p class="text-sm text-red-600">
                                                        <strong>Motif du rejet :</strong> {{ $subject->rejection_reason }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $subjects->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
