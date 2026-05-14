<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('subjects.index') }}" class="hover:text-blue-600 transition">Sujets</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">{{ Str::limit($subject->title, 50) }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-breadcrumb :items="[['label' => 'Sujets', 'url' => route('subjects.index')], ['label' => Str::limit($subject->title, 40)]]" />

            {{-- Messages flash --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                    {{ session('info') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
                    <p class="font-semibold text-sm mb-1">Erreur(s) de validation :</p>
                    <ul class="text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- En-tête du sujet --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r {{ $subject->status === 'validated' ? 'from-green-600 to-green-700' : ($subject->status === 'rejected' ? 'from-red-600 to-red-700' : 'from-blue-600 to-blue-700') }} p-6 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h1 class="text-xl font-bold leading-tight">{{ $subject->title }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm opacity-90">
                                @if($subject->subject_type)
                                    <span class="bg-white/20 px-2 py-0.5 rounded text-xs font-medium">{{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}</span>
                                @endif
                                <span>{{ $subject->created_at->format('d/m/Y') }}</span>
                                @if($subject->academicYear)
                                    <span>{{ $subject->academicYear->name }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                            {{ $subject->status === 'validated' ? 'bg-white text-green-700' : '' }}
                            {{ $subject->status === 'pending' ? 'bg-white text-yellow-700' : '' }}
                            {{ $subject->status === 'rejected' ? 'bg-white text-red-700' : '' }}">
                            {{ $subject->status === 'validated' ? 'Validé' : '' }}
                            {{ $subject->status === 'pending' ? 'En attente' : '' }}
                            {{ $subject->status === 'rejected' ? 'Rejeté' : '' }}
                        </span>
                    </div>
                </div>

                {{-- Informations clés --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4 border-b">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Étudiant</p>
                        <p class="text-sm font-medium text-gray-900">{{ $subject->student->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $subject->student->matricule ?? '' }} · {{ $subject->student->email ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Encadreur</p>
                        <p class="text-sm font-medium text-gray-900">{{ $subject->teacher->name ?? 'Non assigné' }}</p>
                        @if($subject->teacher)
                            <p class="text-xs text-gray-500">{{ $subject->teacher->email }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Filière</p>
                        <p class="text-sm font-medium text-gray-900">{{ $subject->department->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $subject->department->faculty->name ?? '' }}</p>
                    </div>
                </div>

                @if($subject->status === 'rejected' && $subject->rejection_reason)
                    <div class="p-4 bg-red-50 border-b border-red-200">
                        <p class="text-sm text-red-700"><strong>Motif du rejet :</strong> {{ $subject->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Construction du Problème --}}
            @if($subject->context_relevance || $subject->challenges || $subject->research_question)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="magnifying-glass" class="w-5 h-5 text-blue-500" /> Construction du Problème</h2>
                    <div class="space-y-4">
                        @if($subject->context_relevance)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">A. Contexte et Pertinence</h3>
                                <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $subject->context_relevance }}</p>
                            </div>
                        @endif
                        @if($subject->challenges)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">B. Défis et Lacunes</h3>
                                <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $subject->challenges }}</p>
                            </div>
                        @endif
                        @if($subject->research_question)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">C. Question de Recherche</h3>
                                <p class="mt-1 text-blue-800 font-semibold text-lg">{{ $subject->research_question }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Solution et Objectifs --}}
            @if($subject->hypothesis || $subject->general_objective || ($subject->specific_objectives && count($subject->specific_objectives) > 0))
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="light-bulb" class="w-5 h-5 text-yellow-500" /> Solution et Objectifs</h2>
                    <div class="space-y-4">
                        @if($subject->hypothesis)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">Hypothèse</h3>
                                <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $subject->hypothesis }}</p>
                            </div>
                        @endif
                        @if($subject->general_objective)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">Objectif Général</h3>
                                <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $subject->general_objective }}</p>
                            </div>
                        @endif
                        @if($subject->specific_objectives && count($subject->specific_objectives) > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">Objectifs Spécifiques</h3>
                                <ol class="mt-1 list-decimal list-inside text-gray-700 space-y-1">
                                    @foreach($subject->specific_objectives as $obj)
                                        <li>{{ $obj }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Cadre Scientifique et Méthodologique --}}
            @if(($subject->state_of_art && count($subject->state_of_art) > 0) || $subject->demarcations || $subject->methodologies)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="book-open" class="w-5 h-5 text-purple-500" /> Cadre Scientifique et Méthodologique</h2>
                    <div class="space-y-4">
                        @if($subject->state_of_art && count($subject->state_of_art) > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">État de l'art</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Apport / Sujet</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($subject->state_of_art as $i => $ref)
                                                <tr>
                                                    <td class="px-4 py-2 text-gray-400">{{ $i + 1 }}</td>
                                                    <td class="px-4 py-2">{{ $ref['author'] ?? '' }}</td>
                                                    <td class="px-4 py-2">{{ $ref['institution'] ?? '' }}</td>
                                                    <td class="px-4 py-2">{{ $ref['contribution'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if($subject->demarcations)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">Démarcations</h3>
                                <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $subject->demarcations }}</p>
                            </div>
                        @endif
                        @if($subject->methodologies)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase">Méthodologies</h3>
                                <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $subject->methodologies }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Fichiers déposés (visible selon rôle) --}}
            @if($subject->thesisFiles && $subject->thesisFiles->count() > 0)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="folder-open" class="w-5 h-5 text-blue-500" /> Fichiers déposés</h2>
                    <div class="space-y-3">
                        @foreach($subject->thesisFiles as $file)
                            <div class="flex items-center justify-between border rounded-lg p-3">
                                <div>
                                    <p class="text-sm font-medium">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $file->version_type === 'jury' ? 'Version Jury' : 'Version Finale' }}
                                        — {{ $file->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                    @if($file->aiReport && Auth::user()->hasAnyRole(['Enseignant', 'Admin', 'Chef de département']))
                                        <div class="mt-1 flex gap-4 text-xs">
                                            <span class="{{ $file->aiReport->ai_score < 30 ? 'text-green-600' : ($file->aiReport->ai_score < 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                                IA: {{ $file->aiReport->ai_score }}%
                                            </span>
                                            <span class="{{ $file->aiReport->similarity_score < 30 ? 'text-green-600' : ($file->aiReport->similarity_score < 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                                Similarité: {{ $file->aiReport->similarity_score }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('thesis.download', $file) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium shrink-0 inline-flex items-center gap-1">
                                    <x-icon name="arrow-down-tray" class="w-4 h-4" /> Télécharger
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            {{-- Jalons / Milestones --}}
            @include('subjects.partials.milestones')

            {{-- Retour --}}
            <div class="flex justify-between items-center">
                <a href="{{ route('subjects.index') }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
                    ← Retour à la liste des sujets
                </a>
                <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                    Mon tableau de bord →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
