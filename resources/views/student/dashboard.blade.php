<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center gap-2">
            <x-icon name="academic-cap" class="w-6 h-6" /> Mon Espace Étudiant
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Messages flash --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Barre de progression --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="chart-bar" class="w-5 h-5 text-blue-600" /> Progression du TFC</h3>
                    @php
                        $progress = 0;
                        $steps = [];
                        if ($subject) {
                            $progress = 20;
                            $steps[] = ['label' => 'Sujet soumis', 'done' => true];
                            if ($subject->status === 'validated') {
                                $progress = 40;
                                $steps[] = ['label' => 'Sujet validé', 'done' => true];
                                if ($subject->teacher) {
                                    $progress = 50;
                                    $steps[] = ['label' => 'Encadreur assigné', 'done' => true];
                                } else {
                                    $steps[] = ['label' => 'Encadreur assigné', 'done' => false];
                                }
                                if ($subject->thesisFiles->where('version_type', 'jury')->count() > 0) {
                                    $progress = 70;
                                    $steps[] = ['label' => 'Version Jury déposée', 'done' => true];
                                    if ($subject->thesisFiles->where('version_type', 'final')->count() > 0) {
                                        $progress = 100;
                                        $steps[] = ['label' => 'Version Finale déposée', 'done' => true];
                                    } else {
                                        $steps[] = ['label' => 'Version Finale déposée', 'done' => false];
                                    }
                                } else {
                                    $steps[] = ['label' => 'Version Jury déposée', 'done' => false];
                                    $steps[] = ['label' => 'Version Finale déposée', 'done' => false];
                                }
                            } elseif ($subject->status === 'rejected') {
                                $progress = 10;
                                $steps[] = ['label' => 'Sujet validé', 'done' => false];
                                $steps[] = ['label' => 'Encadreur assigné', 'done' => false];
                                $steps[] = ['label' => 'Version Jury déposée', 'done' => false];
                                $steps[] = ['label' => 'Version Finale déposée', 'done' => false];
                            } else {
                                $steps[] = ['label' => 'Sujet validé', 'done' => false];
                                $steps[] = ['label' => 'Encadreur assigné', 'done' => false];
                                $steps[] = ['label' => 'Version Jury déposée', 'done' => false];
                                $steps[] = ['label' => 'Version Finale déposée', 'done' => false];
                            }
                        } else {
                            $steps = [
                                ['label' => 'Sujet soumis', 'done' => false],
                                ['label' => 'Sujet validé', 'done' => false],
                                ['label' => 'Encadreur assigné', 'done' => false],
                                ['label' => 'Version Jury déposée', 'done' => false],
                                ['label' => 'Version Finale déposée', 'done' => false],
                            ];
                        }
                    @endphp

                    {{-- Barre --}}
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ $progress }}% complété</p>

                    {{-- Étapes --}}
                    <div class="grid grid-cols-5 gap-2 text-center text-xs">
                        @foreach($steps as $step)
                            <div class="{{ $step['done'] ? 'text-green-600 font-bold' : 'text-gray-400' }}">
                                <span class="inline-flex items-center justify-center gap-1">@if($step['done'])<x-icon name="check-circle" class="w-4 h-4 text-green-500" />@else<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><circle cx="12" cy="12" r="9"/></svg>@endif {{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Section Sujet --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(!$subject || ($subject && $subject->status === 'rejected'))
                        {{-- Invitation à remplir la fiche de proposition --}}
                        @if($subject && $subject->status === 'rejected')
                            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-red-800 mb-1 flex items-center gap-1.5"><x-icon name="x-circle" class="w-5 h-5" /> Votre sujet a été rejeté</h4>
                                <p class="text-sm text-red-700"><strong>Motif :</strong> {{ $subject->rejection_reason }}</p>
                                <p class="text-sm text-red-600 mt-2">Vous pouvez soumettre un nouveau sujet en tenant compte des remarques.</p>
                            </div>
                        @endif

                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="pencil-square" class="w-5 h-5 text-blue-600" /> Proposer un sujet de TFC</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Remplissez la fiche de proposition de sujet structurée en 5 étapes (informations générales, titre, problématique, objectifs, cadre scientifique).
                        </p>
                        <a href="{{ route('subjects.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition">
                            <x-icon name="document-text" class="w-5 h-5" /> Remplir la Fiche de Proposition
                        </a>
                        <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <p class="text-xs text-amber-700 flex items-start gap-1.5">
                                <x-icon name="information-circle" class="w-4 h-4 mt-0.5 shrink-0" />
                                <span>Note : Une fois soumise, votre fiche sera examinée par le Chef de Filière.
                                <strong>Vous ne pourrez plus la modifier</strong> après soumission, sauf en cas de rejet.</span>
                            </p>
                        </div>
                    @else
                        {{-- Affichage structuré du sujet existant --}}
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="clipboard-document-list" class="w-5 h-5 text-blue-600" /> Ma Fiche de Proposition</h3>
                        <div class="border rounded-lg overflow-hidden mb-4">
                            {{-- En-tête --}}
                            <div class="bg-blue-50 px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                                <h4 class="font-bold text-lg text-gray-900">{{ $subject->title }}</h4>
                                <div class="flex items-center gap-3">
                                    @if($subject->subject_type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $subject->status === 'validated' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $subject->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ $subject->status === 'validated' ? 'Validé' : '' }}
                                        {{ $subject->status === 'pending' ? 'En attente' : '' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Contenu structuré en accordéon --}}
                            <div x-data="{ openSection: null }" class="divide-y divide-gray-100">
                                {{-- Problématique --}}
                                <div>
                                    <button @click="openSection = openSection === 'problem' ? null : 'problem'" type="button"
                                        class="w-full px-4 py-3 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 flex justify-between items-center">
                                        <span class="flex items-center gap-1.5"><x-icon name="magnifying-glass" class="w-4 h-4 text-gray-400" /> Construction du Problème</span>
                                        <svg class="w-4 h-4 transform transition-transform" :class="openSection === 'problem' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="openSection === 'problem'" x-collapse class="px-4 pb-4 space-y-3">
                                        @if($subject->context_relevance)
                                            <div><p class="text-xs font-semibold text-gray-500 uppercase">Contexte et Pertinence</p><p class="text-sm text-gray-700 mt-1">{{ $subject->context_relevance }}</p></div>
                                        @endif
                                        @if($subject->challenges)
                                            <div><p class="text-xs font-semibold text-gray-500 uppercase">Défis et Lacunes</p><p class="text-sm text-gray-700 mt-1">{{ $subject->challenges }}</p></div>
                                        @endif
                                        @if($subject->research_question)
                                            <div><p class="text-xs font-semibold text-gray-500 uppercase">Question de Recherche</p><p class="text-sm text-blue-800 font-medium mt-1">{{ $subject->research_question }}</p></div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Objectifs --}}
                                <div>
                                    <button @click="openSection = openSection === 'objectives' ? null : 'objectives'" type="button"
                                        class="w-full px-4 py-3 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 flex justify-between items-center">
                                        <span class="flex items-center gap-1.5"><x-icon name="light-bulb" class="w-4 h-4 text-gray-400" /> Solution et Objectifs</span>
                                        <svg class="w-4 h-4 transform transition-transform" :class="openSection === 'objectives' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="openSection === 'objectives'" x-collapse class="px-4 pb-4 space-y-3">
                                        @if($subject->hypothesis)
                                            <div><p class="text-xs font-semibold text-gray-500 uppercase">Hypothèse</p><p class="text-sm text-gray-700 mt-1">{{ $subject->hypothesis }}</p></div>
                                        @endif
                                        @if($subject->general_objective)
                                            <div><p class="text-xs font-semibold text-gray-500 uppercase">Objectif Général</p><p class="text-sm text-gray-700 mt-1">{{ $subject->general_objective }}</p></div>
                                        @endif
                                        @if($subject->specific_objectives && count($subject->specific_objectives) > 0)
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Objectifs Spécifiques</p>
                                                <ul class="mt-1 list-disc list-inside text-sm text-gray-700 space-y-1">
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
                                    <button @click="openSection = openSection === 'science' ? null : 'science'" type="button"
                                        class="w-full px-4 py-3 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 flex justify-between items-center">
                                        <span class="flex items-center gap-1.5"><x-icon name="book-open" class="w-4 h-4 text-gray-400" /> Cadre Scientifique</span>
                                        <svg class="w-4 h-4 transform transition-transform" :class="openSection === 'science' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="openSection === 'science'" x-collapse class="px-4 pb-4 space-y-3">
                                        @if($subject->state_of_art && count($subject->state_of_art) > 0)
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">État de l'art</p>
                                                <div class="overflow-x-auto">
                                                    <table class="text-xs w-full">
                                                        <thead><tr class="bg-gray-50"><th class="px-2 py-1 text-left">Auteur</th><th class="px-2 py-1 text-left">Institution</th><th class="px-2 py-1 text-left">Apport</th></tr></thead>
                                                        <tbody>
                                                            @foreach($subject->state_of_art as $ref)
                                                                <tr class="border-b"><td class="px-2 py-1">{{ $ref['author'] ?? '' }}</td><td class="px-2 py-1">{{ $ref['institution'] ?? '' }}</td><td class="px-2 py-1">{{ $ref['contribution'] ?? '' }}</td></tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        @if($subject->demarcations)
                                            <div><p class="text-xs font-semibold text-gray-500 uppercase">Démarcations</p><p class="text-sm text-gray-700 mt-1">{{ $subject->demarcations }}</p></div>
                                        @endif
                                        @if($subject->methodologies)
                                            <div><p class="text-xs font-semibold text-gray-500 uppercase">Méthodologies</p><p class="text-sm text-gray-700 mt-1">{{ $subject->methodologies }}</p></div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Encadreur --}}
                            <div class="px-4 py-3 bg-gray-50 border-t">
                                @if($subject->teacher)
                                    <span class="text-sm text-gray-600 flex items-center gap-1.5"><x-icon name="user" class="w-4 h-4" /> Encadreur : <strong>{{ $subject->teacher->name }}</strong></span>
                                @elseif($subject->status === 'validated')
                                    <span class="text-sm text-orange-500">Encadreur en cours d'assignation...</span>
                                @elseif($subject->status === 'pending')
                                    <span class="text-sm text-blue-700 flex items-center gap-1.5"><x-icon name="lock-closed" class="w-4 h-4" /> Fiche verrouillée — En attente d'examen par le Chef de Filière.</span>
                                @endif
                            </div>
                        </div>

                        {{-- Upload PDF (uniquement si sujet validé) --}}
                        @if($subject->isValidated())
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 mt-6 flex items-center gap-2"><x-icon name="document-arrow-up" class="w-5 h-5 text-blue-600" /> Déposer mon TFC (PDF)</h3>

                            @php
                                $hasJury = $subject->thesisFiles->where('version_type', 'jury')->count() > 0;
                                $hasFinal = $subject->thesisFiles->where('version_type', 'final')->count() > 0;
                                $canUploadFinal = $subject->defense_validated ?? false;
                            @endphp

                            <form action="{{ route('thesis.upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="pdf" class="block text-sm font-medium text-gray-700">Fichier PDF</label>
                                    <input type="file" name="pdf" id="pdf" accept=".pdf" required
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @error('pdf') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="version_type" class="block text-sm font-medium text-gray-700">Type de version</label>
                                    <select name="version_type" id="version_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="jury">Version Jury</option>
                                        @if($canUploadFinal)
                                            <option value="final">Version Finale</option>
                                        @endif
                                    </select>
                                    @if(!$canUploadFinal)
                                        <p class="mt-1 text-xs text-amber-600">Le dépôt de la version finale est bloqué tant que la défense n'est pas validée.</p>
                                    @endif
                                </div>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2">
                                    <x-icon name="arrow-up-tray" class="w-5 h-5" /> Déposer le fichier
                                </button>
                            </form>
                        @endif

                        {{-- Liste des fichiers déposés (sans scores IA détaillés pour l'étudiant) --}}
                        @if($subject->thesisFiles->count() > 0)
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 mt-6 flex items-center gap-2"><x-icon name="folder-open" class="w-5 h-5 text-blue-600" /> Mes fichiers déposés</h3>
                            <div class="space-y-3">
                                @foreach($subject->thesisFiles as $file)
                                    <div class="border rounded-lg p-4 flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">{{ $file->original_name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $file->version_type === 'jury' ? 'Version Jury' : 'Version Finale' }}
                                                — {{ $file->created_at->format('d/m/Y à H:i') }}
                                            </p>
                                            @if($file->aiReport)
                                                {{-- L'étudiant ne voit PAS le score exact — seulement un indicateur global --}}
                                                <div class="mt-2">
                                                    @php
                                                        $globalRisk = max($file->aiReport->ai_score, $file->aiReport->similarity_score);
                                                    @endphp
                                                    @if($globalRisk < 30)
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            <x-icon name="shield-check" class="w-4 h-4" /> Analyse IA : Aucun problème détecté
                                                        </span>
                                                    @elseif($globalRisk < 60)
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <x-icon name="exclamation-triangle" class="w-4 h-4" /> Analyse IA : Attention modérée requise
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                                            <x-icon name="shield-exclamation" class="w-4 h-4" /> Analyse IA : Problème détecté — Consultez votre encadreur
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('thesis.download', $file) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center gap-1">
                                            <x-icon name="arrow-down-tray" class="w-4 h-4" /> Télécharger
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Rappel des règles pour l'étudiant --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-800 mb-2 flex items-center gap-1.5"><x-icon name="information-circle" class="w-5 h-5" /> Rappel — Votre rôle</h4>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li>• Vous ne pouvez voir que <strong>vos propres données</strong> (sujet, fichiers, progression).</li>
                    <li>• <strong>Vous ne choisissez pas</strong> votre encadreur — c'est le Chef de Filière qui l'assigne.</li>
                    <li>• Le sujet <strong>ne peut plus être modifié</strong> après soumission (sauf rejet).</li>
                    <li>• Le dépôt de la <strong>version finale</strong> est bloqué tant que la défense n'est pas validée.</li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
