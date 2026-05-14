<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center gap-2">
            <x-icon name="academic-cap" class="w-6 h-6" /> Tableau de Bord — Enseignant / Directeur
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

            {{-- Statistiques rapides --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-blue-600">{{ $supervisedSubjects->count() }}</div>
                    <div class="text-sm text-gray-600">Étudiants encadrés</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-green-600">
                        {{ $supervisedSubjects->filter(fn($s) => $s->thesisFiles->where('version_type', 'jury')->count() > 0)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Versions Jury reçues</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-purple-600">
                        {{ $supervisedSubjects->filter(fn($s) => $s->thesisFiles->where('version_type', 'final')->count() > 0)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Versions Finales reçues</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-3xl font-bold text-orange-600">
                        {{ $supervisedSubjects->filter(fn($s) => !($s->defense_validated ?? false) && $s->thesisFiles->where('version_type', 'jury')->count() > 0)->count() }}
                    </div>
                    <div class="text-sm text-gray-600">En attente de votre feu vert</div>
                </div>
            </div>

            {{-- Liste des étudiants encadrés --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="users" class="w-5 h-5 text-blue-600" /> Mes étudiants</h3>

                    @if($supervisedSubjects->count() === 0)
                        <div class="text-center py-8">
                            <p class="text-gray-500 italic">Aucun étudiant ne vous a encore été assigné par le Chef de Filière.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($supervisedSubjects as $subject)
                                <div class="border rounded-lg overflow-hidden">
                                    {{-- En-tête étudiant --}}
                                    <div class="bg-gray-50 px-4 py-3 flex flex-wrap items-start justify-between gap-3 sm:items-center">
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-gray-900 break-words">{{ $subject->student->name }}</h4>
                                            <p class="text-sm text-gray-500 break-all sm:break-normal">{{ $subject->student->matricule ?? '—' }} · {{ $subject->student->email }}</p>
                                        </div>
                                        <div class="w-full sm:w-auto flex flex-wrap items-center gap-2 sm:justify-end">
                                            @if($subject->defense_validated ?? false)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <x-icon name="check-circle" class="w-4 h-4" /> Défense autorisée
                                                </span>
                                                @if($subject->defense_date)
                                                    <span class="inline-flex w-full sm:w-auto items-start gap-1 rounded-md bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-800">
                                                        <x-icon name="calendar" class="mt-0.5 h-4 w-4 shrink-0" />
                                                        <span class="leading-4 break-words sm:whitespace-nowrap">{{ \Carbon\Carbon::parse($subject->defense_date)->format('d/m/Y H:i') }} - Salle {{ $subject->defense_room ?? 'à définir' }}</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        {{-- Sujet structuré --}}
                                        <div class="mb-4">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-sm font-semibold text-blue-700">{{ $subject->title }}</p>
                                                @if($subject->subject_type)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Accordéon détails structurés --}}
                                            <div x-data="{ showDetails: false }" class="mt-2">
                                                <button @click="showDetails = !showDetails" type="button"
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                    <span x-text="showDetails ? '▾ Masquer les détails' : '▸ Voir la fiche complète'"></span>
                                                </button>
                                                <div x-show="showDetails" x-collapse class="mt-2 border rounded-lg bg-gray-50 p-3 space-y-3 text-sm text-gray-700">
                                                    @if($subject->research_question)
                                                        <div><span class="text-xs text-gray-500 font-semibold uppercase">Question de Recherche</span><p class="text-blue-800 font-medium mt-0.5">{{ $subject->research_question }}</p></div>
                                                    @endif
                                                    @if($subject->context_relevance)
                                                        <div><span class="text-xs text-gray-500 font-semibold uppercase">Contexte</span><p class="mt-0.5">{{ $subject->context_relevance }}</p></div>
                                                    @endif
                                                    @if($subject->challenges)
                                                        <div><span class="text-xs text-gray-500 font-semibold uppercase">Défis</span><p class="mt-0.5">{{ $subject->challenges }}</p></div>
                                                    @endif
                                                    @if($subject->hypothesis)
                                                        <div><span class="text-xs text-gray-500 font-semibold uppercase">Hypothèse</span><p class="mt-0.5">{{ $subject->hypothesis }}</p></div>
                                                    @endif
                                                    @if($subject->general_objective)
                                                        <div><span class="text-xs text-gray-500 font-semibold uppercase">Objectif Général</span><p class="mt-0.5">{{ $subject->general_objective }}</p></div>
                                                    @endif
                                                    @if($subject->specific_objectives && count($subject->specific_objectives) > 0)
                                                        <div>
                                                            <span class="text-xs text-gray-500 font-semibold uppercase">Objectifs Spécifiques</span>
                                                            <ul class="list-disc list-inside mt-0.5">
                                                                @foreach($subject->specific_objectives as $obj)
                                                                    <li>{{ $obj }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if($subject->state_of_art && count($subject->state_of_art) > 0)
                                                        <div>
                                                            <span class="text-xs text-gray-500 font-semibold uppercase">État de l'art</span>
                                                            <table class="text-xs w-full mt-1"><thead><tr class="bg-white"><th class="px-2 py-1 text-left">Auteur</th><th class="px-2 py-1 text-left">Institution</th><th class="px-2 py-1 text-left">Apport</th></tr></thead><tbody>
                                                            @foreach($subject->state_of_art as $ref)
                                                                <tr class="border-t"><td class="px-2 py-1">{{ $ref['author'] ?? '' }}</td><td class="px-2 py-1">{{ $ref['institution'] ?? '' }}</td><td class="px-2 py-1">{{ $ref['contribution'] ?? '' }}</td></tr>
                                                            @endforeach
                                                            </tbody></table>
                                                        </div>
                                                    @endif
                                                    @if($subject->methodologies)
                                                        <div><span class="text-xs text-gray-500 font-semibold uppercase">Méthodologies</span><p class="mt-0.5">{{ $subject->methodologies }}</p></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Fichiers déposés --}}
                                        @if($subject->thesisFiles->count() > 0)
                                            <div class="space-y-3">
                                                <h5 class="text-sm font-semibold text-gray-700 flex items-center gap-1.5"><x-icon name="paper-clip" class="w-4 h-4" /> Fichiers déposés :</h5>
                                                @foreach($subject->thesisFiles as $file)
                                                    <div class="bg-gray-50 rounded-lg p-4 border">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex-1">
                                                                <p class="text-sm font-medium">{{ $file->original_name }}</p>
                                                                <p class="text-xs text-gray-500">
                                                                    {{ $file->version_type === 'jury' ? 'Version Jury' : 'Version Finale' }}
                                                                    — {{ $file->created_at->format('d/m/Y à H:i') }}
                                                                </p>

                                                                {{-- Scores IA détaillés (visible UNIQUEMENT pour Enseignant/Directeur) --}}
                                                                @if($file->aiReport)
                                                                    <div class="mt-3 space-y-2">
                                                                        {{-- Jauge Score IA --}}
                                                                        <div>
                                                                            <div class="flex justify-between text-xs mb-1">
                                                                                <span class="font-medium text-gray-700 flex items-center gap-1"><x-icon name="cpu-chip" class="w-3.5 h-3.5" /> Score IA (contenu généré)</span>
                                                                                <span class="font-bold {{ $file->aiReport->ai_score < 20 ? 'text-green-700' : ($file->aiReport->ai_score < 50 ? 'text-yellow-700' : 'text-red-700') }}">
                                                                                    {{ $file->aiReport->ai_score }}%
                                                                                </span>
                                                                            </div>
                                                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                                                <div class="h-2.5 rounded-full {{ $file->aiReport->ai_score < 20 ? 'bg-green-500' : ($file->aiReport->ai_score < 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                                                     style="width: {{ min($file->aiReport->ai_score, 100) }}%"></div>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Jauge Similarité --}}
                                                                        <div>
                                                                            <div class="flex justify-between text-xs mb-1">
                                                                                <span class="font-medium text-gray-700 flex items-center gap-1"><x-icon name="magnifying-glass" class="w-3.5 h-3.5" /> Similarité (plagiat)</span>
                                                                                <span class="font-bold {{ $file->aiReport->similarity_score < 20 ? 'text-green-700' : ($file->aiReport->similarity_score < 50 ? 'text-yellow-700' : 'text-red-700') }}">
                                                                                    {{ $file->aiReport->similarity_score }}%
                                                                                </span>
                                                                            </div>
                                                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                                                <div class="h-2.5 rounded-full {{ $file->aiReport->similarity_score < 20 ? 'bg-green-500' : ($file->aiReport->similarity_score < 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                                                     style="width: {{ min($file->aiReport->similarity_score, 100) }}%"></div>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Détails du rapport --}}
                                                                        @if(isset($file->aiReport->details['sources']) && count($file->aiReport->details['sources']) > 0)
                                                                            <div class="mt-2 text-xs text-gray-500 bg-white rounded p-2 border">
                                                                                <p class="font-semibold mb-1">Sources détectées :</p>
                                                                                @foreach($file->aiReport->details['sources'] as $source)
                                                                                    <p>• {{ $source }}</p>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif

                                                                        @if(isset($file->aiReport->details['mode']) && $file->aiReport->details['mode'] === 'simulation')
                                                                            <p class="text-xs text-orange-500 mt-1">Résultat simulé (API non configurée)</p>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <p class="text-xs text-gray-400 mt-2">Pas encore analysé par le système IA</p>
                                                                @endif
                                                            </div>

                                                            <a href="{{ route('thesis.download', $file) }}"
                                                                class="ml-4 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold py-2 px-3 rounded shrink-0 inline-flex items-center gap-1">
                                                                <x-icon name="arrow-down-tray" class="w-4 h-4" /> Télécharger
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @php
                                                $hasJuryVersion = $subject->thesisFiles->where('version_type', 'jury')->count() > 0;
                                                $hasFinalVersion = $subject->thesisFiles->where('version_type', 'final')->count() > 0;
                                            @endphp

                                            {{-- Bouton "Feu Vert" pour autoriser la défense --}}
                                            @if($hasJuryVersion && !($subject->defense_validated ?? false))
                                                <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                                    <p class="text-sm text-amber-800 mb-3">
                                                        <strong>Action requise :</strong> Après avoir lu la version Jury et vérifié les scores IA, vous pouvez autoriser l'étudiant à déposer la version finale et passer en défense.
                                                    </p>
                                                    <form action="{{ route('subjects.authorize-defense', $subject) }}" method="POST"
                                                          onsubmit="return confirm('Confirmer l\'autorisation de défense pour {{ $subject->student->name }} ?')">
                                                        @csrf
                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow inline-flex items-center gap-2">
                                                            <x-icon name="rocket-launch" class="w-5 h-5" /> Autoriser le dépôt final / Valider pour défense
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif

                                            {{-- Retrait du Feu Vert --}}
                                            @if($subject->defense_validated ?? false)
                                                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                                    <p class="text-sm text-green-800 mb-3">
                                                        <strong>Feu Vert accordé :</strong> la soutenance est autorisée pour cet étudiant.
                                                    </p>

                                                    @if(!$hasFinalVersion)
                                                        <form action="{{ route('subjects.revoke-defense', $subject) }}" method="POST"
                                                              onsubmit="return confirm('Retirer le Feu Vert pour {{ $subject->student->name }} ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="mb-3">
                                                                <label for="defense_revocation_reason_{{ $subject->id }}" class="block text-sm font-medium text-green-800 mb-1">
                                                                    Motif du retrait <span class="text-red-600">*</span>
                                                                </label>
                                                                <textarea
                                                                    id="defense_revocation_reason_{{ $subject->id }}"
                                                                    name="defense_revocation_reason"
                                                                    rows="3"
                                                                    required
                                                                    minlength="10"
                                                                    maxlength="1000"
                                                                    class="w-full rounded-md border border-green-300 px-3 py-2 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500"
                                                                    placeholder="Expliquez brièvement pourquoi le Feu Vert est retiré (corrections demandées, anomalies détectées, etc.)"
                                                                >{{ old('defense_revocation_reason') }}</textarea>
                                                                @error('defense_revocation_reason')
                                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow">
                                                                Retirer le Feu Vert
                                                            </button>
                                                        </form>
                                                    @else
                                                        <p class="text-xs text-green-700">
                                                            Retrait indisponible : la version finale a déjà été déposée.
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-400 italic">Aucun fichier déposé pour le moment.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            {{-- ================================================================= --}}
            {{-- SECTION JALONS — Suivi de l'encadrement --}}
            {{-- ================================================================= --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="flag" class="w-5 h-5 text-indigo-600" /> Suivi par Jalons — Encadrement
                    </h3>

                    {{-- Stats jalons --}}
                    @if($milestoneStats['total'] > 0)
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                            <div class="text-center rounded-lg bg-gray-50 border px-3 py-3">
                                <p class="text-2xl font-bold text-gray-800">{{ $milestoneStats['total'] }}</p>
                                <p class="text-xs text-gray-500">Total jalons</p>
                            </div>
                            <div class="text-center rounded-lg bg-yellow-50 border border-yellow-200 px-3 py-3">
                                <p class="text-2xl font-bold text-yellow-700">{{ $milestoneStats['pending'] }}</p>
                                <p class="text-xs text-yellow-600">En attente</p>
                            </div>
                            <div class="text-center rounded-lg bg-blue-50 border border-blue-200 px-3 py-3">
                                <p class="text-2xl font-bold text-blue-700">{{ $milestoneStats['submitted'] }}</p>
                                <p class="text-xs text-blue-600">À corriger</p>
                            </div>
                            <div class="text-center rounded-lg bg-green-50 border border-green-200 px-3 py-3">
                                <p class="text-2xl font-bold text-green-700">{{ $milestoneStats['validated'] }}</p>
                                <p class="text-xs text-green-600">Validés</p>
                            </div>
                            <div class="text-center rounded-lg bg-red-50 border border-red-200 px-3 py-3">
                                <p class="text-2xl font-bold text-red-700">{{ $milestoneStats['rejected'] }}</p>
                                <p class="text-xs text-red-600">Rejetés</p>
                            </div>
                        </div>
                    @endif

                    {{-- Jalons en attente de correction --}}
                    @if($pendingMilestones->count() > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <x-icon name="exclamation-circle" class="w-5 h-5 text-amber-500" />
                                Action requise : {{ $pendingMilestones->count() }} jalon(s) en attente de correction
                            </h4>

                            <div class="space-y-3">
                                @foreach($pendingMilestones as $milestone)
                                    @php
                                        $isOverdue = $milestone->correction_deadline && $milestone->correction_deadline->isPast();
                                    @endphp
                                    <div class="border rounded-lg overflow-hidden {{ $isOverdue ? 'border-red-300 bg-red-50/40' : 'border-blue-200 bg-blue-50/30' }}">
                                        <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                                            {{-- Indicateur urgent --}}
                                            <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $isOverdue ? 'bg-red-500' : 'bg-blue-500' }} text-white">
                                                @if($isOverdue)
                                                    <x-icon name="exclamation-triangle" class="w-5 h-5" />
                                                @else
                                                    <x-icon name="pencil-square" class="w-5 h-5" />
                                                @endif
                                            </div>

                                            {{-- Infos --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-semibold text-sm text-gray-900">{{ $milestone->title }}</p>
                                                    @if($isOverdue)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800 animate-pulse">
                                                            SLA dépassé !
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                                                    <span class="font-medium text-gray-700">
                                                        {{ $milestone->subject->student->name ?? '—' }}
                                                    </span>
                                                    <span class="text-gray-400">·</span>
                                                    <span>{{ Str::limit($milestone->subject->title, 35) }}</span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                                                    <span class="flex items-center gap-1">
                                                        <x-icon name="arrow-up-tray" class="w-3.5 h-3.5" />
                                                        Soumis le {{ $milestone->submission_date?->format('d/m/Y H:i') ?? '—' }}
                                                    </span>
                                                    @if($milestone->correction_deadline)
                                                        <span class="flex items-center gap-1 {{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
                                                            <x-icon name="clock" class="w-3.5 h-3.5" />
                                                            SLA : {{ $milestone->correction_deadline->format('d/m/Y H:i') }}
                                                            @if($isOverdue)
                                                                ({{ $milestone->correction_deadline->diffForHumans() }})
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Boutons d'action --}}
                                            <div class="shrink-0 flex items-center gap-2 flex-wrap">
                                                @if($milestone->thesisFile)
                                                    <a href="{{ route('thesis.download', $milestone->thesisFile) }}"
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded transition">
                                                        <x-icon name="arrow-down-tray" class="w-3.5 h-3.5" /> PDF
                                                    </a>
                                                @endif
                                                <a href="{{ route('subjects.show', $milestone->subject) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded transition">
                                                    <x-icon name="eye" class="w-3.5 h-3.5" /> Corriger
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($milestoneStats['total'] > 0)
                        <div class="text-center py-6 text-gray-500">
                            <x-icon name="check-circle" class="w-8 h-8 mx-auto text-green-400 mb-2" />
                            <p class="text-sm">Aucun jalon en attente de correction. Tout est à jour !</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic py-4 text-center">Aucun jalon n'a encore été défini. Créez-en depuis la fiche d'un sujet.</p>
                    @endif
                </div>
            </div>

            {{-- Rappel du rôle Enseignant --}}
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-purple-800 mb-2 flex items-center gap-1.5"><x-icon name="information-circle" class="w-5 h-5" /> Rappel — Votre rôle d'Encadreur</h4>
                <ul class="text-xs text-purple-700 space-y-1">
                    <li>• Vous ne voyez que les étudiants <strong>qui vous ont été assignés</strong> par le Chef de Filière.</li>
                    <li>• Vous pouvez <strong>télécharger et lire</strong> les fichiers PDF (Version Jury et Finale).</li>
                    <li>• Vous êtes le <strong>seul</strong> (avec le Jury) à voir les scores IA détaillés (jauges de couleur).</li>
                    <li>• Votre <strong>"Feu Vert"</strong> est nécessaire pour que l'étudiant puisse déposer la version finale.</li>
                    <li>• <strong>Vous ne validez pas</strong> les sujets — c'est le rôle du Chef de Filière.</li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
