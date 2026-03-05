<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center gap-2">
            <x-icon name="building-library" class="w-6 h-6" /> Tableau de Bord — Chef de Filière
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Info filière --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-4 text-white">
                <p class="text-blue-100 text-sm">Ma filière</p>
                <p class="text-2xl font-bold">{{ Auth::user()->department?->name ?? 'Non assignée' }}</p>
                <p class="text-blue-200 text-sm mt-1">Faculté: {{ Auth::user()->department?->faculty ?? '—' }}</p>
            </div>

            {{-- Statistiques rapides --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-yellow-600">{{ $pendingSubjects->count() }}</div>
                    <div class="text-sm text-gray-600">Sujets en attente</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-green-600">{{ $allSubjects->where('status', 'validated')->count() }}</div>
                    <div class="text-sm text-gray-600">Sujets validés</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-red-600">{{ $allSubjects->where('status', 'rejected')->count() }}</div>
                    <div class="text-sm text-gray-600">Sujets rejetés</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-blue-600">{{ $teachers->count() }}</div>
                    <div class="text-sm text-gray-600">Enseignants disponibles</div>
                </div>
            </div>

            {{-- Sujets en attente --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="clock" class="w-5 h-5 text-yellow-600" /> Sujets en attente de validation</h3>

                    @if($pendingSubjects->count() === 0)
                        <div class="text-center py-8">
                            <p class="text-gray-500 italic">Aucun sujet en attente pour le moment.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($pendingSubjects as $subject)
                                <div class="border border-yellow-200 rounded-lg bg-yellow-50 overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-lg">{{ $subject->title }}</h4>
                                                @if($subject->subject_type)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                                        {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                                    </span>
                                                @endif
                                                <p class="text-sm text-gray-500 mt-2">
                                                    Étudiant: <strong>{{ $subject->student->name }}</strong>
                                                    ({{ $subject->student->matricule ?? '—' }})
                                                    — Soumis le {{ $subject->created_at->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Détails structurés en accordéon --}}
                                        <div x-data="{ openDetail: null }" class="mt-3 border rounded-lg bg-white divide-y divide-gray-100">
                                            {{-- Problématique --}}
                                            <div>
                                                <button @click="openDetail = openDetail === 'problem' ? null : 'problem'" type="button"
                                                    class="w-full px-3 py-2 text-left text-xs font-medium text-gray-600 hover:bg-gray-50 flex justify-between items-center">
                                                    <span>Problématique</span>
                                                    <svg class="w-3 h-3 transform transition-transform" :class="openDetail === 'problem' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                                <div x-show="openDetail === 'problem'" x-collapse class="px-3 pb-3 text-sm text-gray-700 space-y-2">
                                                    @if($subject->context_relevance)
                                                        <div><span class="text-xs text-gray-500 font-semibold">Contexte :</span> {{ $subject->context_relevance }}</div>
                                                    @endif
                                                    @if($subject->challenges)
                                                        <div><span class="text-xs text-gray-500 font-semibold">Défis :</span> {{ $subject->challenges }}</div>
                                                    @endif
                                                    @if($subject->research_question)
                                                        <div><span class="text-xs text-gray-500 font-semibold">Question :</span> <strong class="text-blue-800">{{ $subject->research_question }}</strong></div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Objectifs --}}
                                            <div>
                                                <button @click="openDetail = openDetail === 'obj' ? null : 'obj'" type="button"
                                                    class="w-full px-3 py-2 text-left text-xs font-medium text-gray-600 hover:bg-gray-50 flex justify-between items-center">
                                                    <span>Objectifs</span>
                                                    <svg class="w-3 h-3 transform transition-transform" :class="openDetail === 'obj' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                                <div x-show="openDetail === 'obj'" x-collapse class="px-3 pb-3 text-sm text-gray-700 space-y-2">
                                                    @if($subject->hypothesis)
                                                        <div><span class="text-xs text-gray-500 font-semibold">Hypothèse :</span> {{ $subject->hypothesis }}</div>
                                                    @endif
                                                    @if($subject->general_objective)
                                                        <div><span class="text-xs text-gray-500 font-semibold">Objectif Général :</span> {{ $subject->general_objective }}</div>
                                                    @endif
                                                    @if($subject->specific_objectives && count($subject->specific_objectives) > 0)
                                                        <div>
                                                            <span class="text-xs text-gray-500 font-semibold">Objectifs Spécifiques :</span>
                                                            <ul class="list-disc list-inside ml-2 mt-1">
                                                                @foreach($subject->specific_objectives as $obj)
                                                                    <li>{{ $obj }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Cadre scientifique --}}
                                            <div>
                                                <button @click="openDetail = openDetail === 'science' ? null : 'science'" type="button"
                                                    class="w-full px-3 py-2 text-left text-xs font-medium text-gray-600 hover:bg-gray-50 flex justify-between items-center">
                                                    <span>Cadre Scientifique</span>
                                                    <svg class="w-3 h-3 transform transition-transform" :class="openDetail === 'science' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                                <div x-show="openDetail === 'science'" x-collapse class="px-3 pb-3 text-sm text-gray-700 space-y-2">
                                                    @if($subject->state_of_art && count($subject->state_of_art) > 0)
                                                        <div class="overflow-x-auto">
                                                            <table class="text-xs w-full">
                                                                <thead><tr class="bg-gray-50"><th class="px-2 py-1 text-left">Auteur</th><th class="px-2 py-1 text-left">Institution</th><th class="px-2 py-1 text-left">Apport</th></tr></thead>
                                                                <tbody>
                                                                    @foreach($subject->state_of_art as $ref)
                                                                        <tr class="border-t"><td class="px-2 py-1">{{ $ref['author'] ?? '' }}</td><td class="px-2 py-1">{{ $ref['institution'] ?? '' }}</td><td class="px-2 py-1">{{ $ref['contribution'] ?? '' }}</td></tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                    @if($subject->demarcations)
                                                        <div><span class="text-xs text-gray-500 font-semibold">Démarcations :</span> {{ $subject->demarcations }}</div>
                                                    @endif
                                                    @if($subject->methodologies)
                                                        <div><span class="text-xs text-gray-500 font-semibold">Méthodologies :</span> {{ $subject->methodologies }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-4 pb-4 flex flex-col gap-4">
                                        {{-- Bouton Valider avec modale --}}
                                        <details class="relative">
                                            <summary class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded inline-flex items-center gap-1.5 cursor-pointer list-none">
                                                <x-icon name="check-circle" class="w-4 h-4" /> Valider & Assigner un Encadreur
                                            </summary>

                                            {{-- Formulaire d'assignation --}}
                                            <div class="mt-2 w-full max-w-md bg-white rounded-lg shadow-lg border p-4">
                                                <h5 class="font-semibold mb-3">Assigner un encadreur</h5>
                                                <form action="{{ route('subjects.validate', $subject) }}" method="POST">
                                                    @csrf
                                                    <select name="teacher_id" required
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-3">
                                                        <option value="">— Choisir un enseignant —</option>
                                                        @foreach($teachers as $teacher)
                                                            <option value="{{ $teacher->id }}">
                                                                {{ $teacher->name }}
                                                                ({{ $teacher->supervisedSubjects->count() }} étudiant(s) encadré(s))
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <p class="text-xs text-gray-500 mb-3">L'enseignant sera notifié automatiquement.</p>
                                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded">
                                                        Confirmer la validation
                                                    </button>
                                                </form>
                                            </div>
                                        </details>

                                        {{-- Bouton Rejeter --}}
                                        <details class="relative">
                                            <summary class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2 px-4 rounded inline-flex items-center gap-1.5 cursor-pointer list-none">
                                                <x-icon name="x-circle" class="w-4 h-4" /> Rejeter
                                            </summary>

                                            <div class="mt-2 w-full max-w-md bg-white rounded-lg shadow-lg border p-4">
                                                <h5 class="font-semibold mb-3">Motif du rejet</h5>
                                                <p class="text-xs text-gray-500 mb-2">Le commentaire est <strong>obligatoire</strong> et sera visible par l'étudiant.</p>
                                                <form action="{{ route('subjects.reject', $subject) }}" method="POST">
                                                    @csrf
                                                    <textarea name="rejection_reason" required rows="3"
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-3"
                                                        placeholder="Expliquez clairement les raisons du rejet..."></textarea>
                                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2 px-4 rounded">
                                                        Confirmer le rejet
                                                    </button>
                                                </form>
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vue globale : Tous les étudiants de la filière --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="clipboard-document-list" class="w-5 h-5 text-blue-600" /> Tous les sujets de la filière</h3>

                    @if($allSubjects->count() === 0)
                        <p class="text-gray-500 italic text-center py-4">Aucun sujet soumis dans votre filière.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujet</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encadreur</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fichiers</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($allSubjects as $subject)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $subject->student->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $subject->student->matricule ?? '—' }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ Str::limit($subject->title, 50) }}</div>
                                                @if($subject->subject_type)
                                                    <span class="text-xs text-blue-600">{{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}</span>
                                                @endif
                                                @if($subject->research_question)
                                                    <p class="text-xs text-gray-500 mt-0.5 italic">{{ Str::limit($subject->research_question, 80) }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $subject->teacher?->name ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $subject->status === 'validated' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $subject->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $subject->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $subject->status === 'validated' ? 'Validé' : '' }}
                                                    {{ $subject->status === 'pending' ? 'En attente' : '' }}
                                                    {{ $subject->status === 'rejected' ? 'Rejeté' : '' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $filesCount = $subject->thesisFiles->count();
                                                    $hasJury = $subject->thesisFiles->where('version_type', 'jury')->count() > 0;
                                                    $hasFinal = $subject->thesisFiles->where('version_type', 'final')->count() > 0;
                                                @endphp
                                                @if($filesCount === 0)
                                                    <span class="text-gray-400">Aucun</span>
                                                @else
                                                    @if($hasJury) <span class="text-blue-600">Jury</span> @endif
                                                    @if($hasFinal) <span class="text-green-600 ml-1">Final</span> @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Répartition des charges d'encadrement --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="users" class="w-5 h-5 text-blue-600" /> Charge d'encadrement par enseignant</h3>
                    @if($teachers->count() === 0)
                        <p class="text-gray-500 italic">Aucun enseignant dans votre filière.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($teachers as $teacher)
                                @php
                                    $count = $teacher->supervisedSubjects->count();
                                    $color = $count === 0 ? 'gray' : ($count <= 3 ? 'green' : ($count <= 6 ? 'yellow' : 'red'));
                                @endphp
                                <div class="border rounded-lg p-4 bg-{{ $color }}-50 border-{{ $color }}-200">
                                    <p class="font-semibold text-gray-900">{{ $teacher->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $teacher->email }}</p>
                                    <p class="mt-2 text-lg font-bold text-{{ $color }}-700">{{ $count }} étudiant(s)</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Rappel du rôle CP --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-800 mb-2 flex items-center gap-1.5"><x-icon name="information-circle" class="w-5 h-5" /> Rappel — Votre rôle de Chef de Filière</h4>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li>• <strong>Vous validez ou rejetez</strong> les sujets (avec commentaire obligatoire en cas de rejet).</li>
                    <li>• <strong>Vous assignez</strong> un enseignant encadreur à chaque sujet validé.</li>
                    <li>• Vous avez une vue sur <strong>tous les étudiants de votre filière</strong> uniquement.</li>
                    <li>• <strong>Vous ne modifiez pas</strong> le contenu des fichiers PDF des étudiants.</li>
                    <li>• Vous <strong>n'avez pas accès</strong> aux données des autres filières.</li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
