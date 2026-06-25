<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 text-primary rounded-xl">
                <x-icon name="document-text" class="w-6 h-6" />
            </div>
            Détail du Sujet de Recherche
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in-up" x-data="{ currentTab: 'overview' }">
        
        <x-breadcrumb :items="[['label' => 'Sujets', 'url' => route('subjects.index')], ['label' => Str::limit($subject->title, 40)]]" />

        {{-- SUBJECT HERO CARD --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100 bg-white">
            <div class="bg-gradient-to-br from-slate-900 via-primary-dark to-slate-950 p-6 lg:p-8 text-white relative">
                <!-- Glowing accent background -->
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-accent/5 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($subject->subject_type)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-white/10 text-accent border border-white/10 uppercase tracking-wider">
                                    {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                </span>
                            @endif
                            <span class="text-xs text-slate-300 font-semibold">{{ $subject->created_at->format('d/m/Y') }}</span>
                            @if($subject->academicYear)
                                <span class="text-xs text-slate-400 font-bold border-l border-white/20 pl-2">{{ $subject->academicYear->name }}</span>
                            @endif
                        </div>
                        
                        <h1 class="font-serif text-xl lg:text-2xl font-extrabold tracking-tight leading-relaxed">
                            {{ $subject->title }}
                        </h1>
                    </div>
                    
                    <span class="shrink-0">
                        <x-status-badge :status="$subject->status" class="py-1.5 px-4 font-black tracking-wide text-xs rounded-xl shadow-md" />
                    </span>
                </div>
            </div>

            {{-- 4 Columns Info Grid --}}
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 bg-slate-50/20 border-b border-slate-100">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Étudiant</span>
                    <p class="text-sm font-extrabold text-slate-800">{{ $subject->student->name ?? '—' }}</p>
                    <p class="text-[11px] text-slate-500 font-medium leading-none mt-0.5">{{ $subject->student->matricule ?? '' }} · {{ $subject->student->email ?? '' }}</p>
                </div>
                
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Directeur d'Encadrement</span>
                    <p class="text-sm font-extrabold text-slate-800">{{ $subject->teacher->name ?? 'Non Assigné' }}</p>
                    @if($subject->teacher)
                        <p class="text-[11px] text-slate-500 font-medium leading-none mt-0.5">{{ $subject->teacher->email }}</p>
                    @endif
                </div>

                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Filière / Faculté</span>
                    <p class="text-sm font-extrabold text-slate-800">{{ $subject->department->name ?? '—' }}</p>
                    <p class="text-[11px] text-slate-500 font-medium leading-none mt-0.5">{{ $subject->department->faculty->name ?? '' }}</p>
                </div>
            </div>
            
            @if($subject->status === 'rejected' && $subject->rejection_reason)
                <div class="px-6 py-4 bg-red-50 border-b border-red-100 flex items-start gap-2.5">
                    <x-icon name="exclamation-circle" class="h-5 w-5 text-red-500 shrink-0 mt-0.5" />
                    <div class="text-xs text-red-700 leading-normal font-semibold">
                        <strong>Motif du rejet académique :</strong> {{ $subject->rejection_reason }}
                    </div>
                </div>
            @endif
        </div>

        {{-- NLP SIMILARITY AUDIT BOX --}}
        @if($subject->similarity_score !== null && Auth::user()->hasAnyRole(['Chef de département', 'Doyen', 'Admin']))
            @php
                if ($subject->similarity_score >= 80) {
                    $cStyle = 'bg-red-50 border-red-200 text-red-800';
                    $textStyle = 'text-red-600';
                    $badgeStyle = 'bg-red-100 text-red-800 border-redNone-250';
                } elseif ($subject->similarity_score >= 50) {
                    $cStyle = 'bg-amber-50 border-amber-200 text-amber-800';
                    $textStyle = 'text-amber-600';
                    $badgeStyle = 'bg-amber-100 text-amber-800 border-amber-250';
                } else {
                    $cStyle = 'bg-green-50 border-green-200 text-green-800';
                    $textStyle = 'text-green-600';
                    $badgeStyle = 'bg-green-100 text-green-800 border-greenNone-250';
                }
            @endphp
            <div class="glass-card rounded-2xl p-5 border-l-4 {{ $subject->similarity_score >= 80 ? 'border-red-500' : ($subject->similarity_score >= 50 ? 'border-amber-500' : 'border-green-500') }} {{ $cStyle }} shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="p-2.5 rounded-xl bg-white/60 border border-white shrink-0">
                        <x-icon name="shield-check" class="h-6 w-6 text-slate-700" />
                    </div>
                    <div class="space-y-2 flex-1 min-w-0">
                        <h3 class="text-xs font-black uppercase tracking-wider block">Audit d'Intégrité Scientifique (Algorithme NLP TF-IDF)</h3>
                        
                        <p class="text-xs leading-normal font-semibold">
                            Taux de similarité sémantique détecté : 
                            <span class="text-lg font-black {{ $textStyle }}">{{ $subject->similarity_score }}%</span> 
                            par rapport à l'archive des TFC validés.
                        </p>
                        
                        @if($subject->similarity_score >= 80)
                            <p class="text-[11px] font-extrabold text-red-700 flex items-center gap-1">
                                 Risque de plagiat ou de sujet redondant extrêmement élevé.
                            </p>
                        @endif

                        @if($subject->similarity_details && count($subject->similarity_details) > 0)
                            <div class="pt-2 border-t border-slate-200/50 mt-2 space-y-1.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Sujets les plus proches identifiés :</span>
                                <ul class="space-y-1">
                                    @foreach($subject->similarity_details as $detail)
                                        <li class="text-xs flex items-center justify-between gap-4 font-semibold text-slate-650">
                                            <span>Sujet #{{ $detail['subject_id'] ?? '—' }} (Intensité : <strong>{{ $detail['score'] ?? 0 }}%</strong>)</span>
                                            @if(isset($detail['subject_id']) && $detail['subject_id'] !== 'NEW')
                                                <a href="{{ route('subjects.show', $detail['subject_id']) }}" class="text-xs font-bold text-primary hover:underline">Consulter sujet →</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- TAB NAVIGATION BUTTONS --}}
        <div class="flex border-b border-slate-200/60 gap-4 flex-wrap">
            <button @click="currentTab = 'overview'" 
                    class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all"
                    :class="currentTab === 'overview' ? 'border-primary text-primary font-black' : 'border-transparent text-slate-400 hover:text-slate-650'">
                Aperçu Général
            </button>
            <button @click="currentTab = 'problem'" 
                    class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all"
                    :class="currentTab === 'problem' ? 'border-primary text-primary font-black' : 'border-transparent text-slate-400 hover:text-slate-650'">
                Problématique
            </button>
            <button @click="currentTab = 'solution'" 
                    class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all"
                    :class="currentTab === 'solution' ? 'border-primary text-primary font-black' : 'border-transparent text-slate-400 hover:text-slate-650'">
                Objectifs & Hypothèses
            </button>
            <button @click="currentTab = 'science'" 
                    class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all"
                    :class="currentTab === 'science' ? 'border-primary text-primary font-black' : 'border-transparent text-slate-400 hover:text-slate-650'">
                Cadre & Fichiers
            </button>
        </div>

        {{-- TAB CONTENT PANELS --}}
        <div class="space-y-6">
            
            {{-- TAB 1: OVERVIEW --}}
            <div x-show="currentTab === 'overview'" x-cloak class="space-y-6">
                @include('subjects.partials.milestones')
            </div>

            {{-- TAB 2: PROBLEM --}}
            <div x-show="currentTab === 'problem'" x-cloak class="space-y-6">
                @if($subject->context_relevance || $subject->challenges || $subject->research_question)
                    <div class="glass-card rounded-2xl p-6 shadow-sm bg-white/80 backdrop-blur-md border border-slate-200/60 space-y-5">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                            <x-icon name="magnifying-glass" class="h-5 w-5 text-blue-500" />
                            <span>Construction Conceptuelle du Problème</span>
                        </h3>
                        
                        <div class="space-y-5">
                            @if($subject->context_relevance)
                                <div class="space-y-1">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">A. Contexte académique & Pertinence</h4>
                                    <p class="text-sm text-slate-650 leading-relaxed whitespace-pre-line">{{ $subject->context_relevance }}</p>
                                </div>
                            @endif
                            @if($subject->challenges)
                                <div class="space-y-1">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">B. Défis structurels & Lacunes de recherche</h4>
                                    <p class="text-sm text-slate-650 leading-relaxed whitespace-pre-line">{{ $subject->challenges }}</p>
                                </div>
                            @endif
                            @if($subject->research_question)
                                <div class="space-y-1 bg-blue-50/50 p-4 rounded-xl border border-blueNone-150 shadow-inner">
                                    <h4 class="text-xs font-bold text-blueNone-850 uppercase tracking-wider">C. Question Directrice de Recherche</h4>
                                    <p class="text-base text-slate-800 font-extrabold leading-relaxed">{{ $subject->research_question }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic text-center py-6">Aucune problématique renseignée pour ce sujet.</p>
                @endif
            </div>

            {{-- TAB 3: OBJECTIFS & HYPOTHÈSES --}}
            <div x-show="currentTab === 'solution'" x-cloak class="space-y-6">
                @if($subject->hypothesis || $subject->general_objective || ($subject->specific_objectives && count($subject->specific_objectives) > 0))
                    <div class="glass-card rounded-2xl p-6 shadow-sm bg-white/80 backdrop-blur-md border border-slate-200/60 space-y-5">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                            <x-icon name="light-bulb" class="h-5 w-5 text-green-500" />
                            <span>Solution et Objectifs du Projet</span>
                        </h3>
                        
                        <div class="space-y-5">
                            @if($subject->hypothesis)
                                <div class="space-y-1">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">A. Hypothèse globale formulée</h4>
                                    <p class="text-sm text-slate-655 leading-relaxed whitespace-pre-line">{{ $subject->hypothesis }}</p>
                                </div>
                            @endif
                            @if($subject->general_objective)
                                <div class="space-y-1">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">B. Objectif Principal</h4>
                                    <p class="text-sm text-slate-655 leading-relaxed whitespace-pre-line">{{ $subject->general_objective }}</p>
                                </div>
                            @endif
                            @if($subject->specific_objectives && count($subject->specific_objectives) > 0)
                                <div class="space-y-2">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">C. Objectifs Spécifiques et Étapes</h4>
                                    <ol class="space-y-2 pl-1">
                                        @foreach($subject->specific_objectives as $i => $obj)
                                            <li class="text-sm text-slate-655 flex items-start gap-2.5 leading-relaxed">
                                                <span class="font-black text-green-500 shrink-0">{{ $i + 1 }}.</span>
                                                <span>{{ $obj }}</span>
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic text-center py-6">Aucun objectif défini pour ce sujet.</p>
                @endif
            </div>

            {{-- TAB 4: CADRE & FICHIERS --}}
            <div x-show="currentTab === 'science'" x-cloak class="space-y-6">
                
                {{-- State of Art and Framework --}}
                @if(($subject->state_of_art && count($subject->state_of_art) > 0) || $subject->demarcations || $subject->methodologies)
                    <div class="glass-card rounded-2xl p-6 shadow-sm bg-white/80 backdrop-blur-md border border-slate-200/60 space-y-5">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                            <x-icon name="book-open" class="h-5 w-5 text-blue-500" />
                            <span>Cadre Scientifique et Méthodologies</span>
                        </h3>
                        
                        <div class="space-y-5">
                            @if($subject->state_of_art && count($subject->state_of_art) > 0)
                                <div class="space-y-2">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">A. État de l'art & Revue Littéraire</h4>
                                    <div class="overflow-x-auto rounded-xl border border-slate-100 shadow-inner bg-slate-50/20">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                                                    <th class="px-4 py-2.5">Auteur</th>
                                                    <th class="px-4 py-2.5">Institution</th>
                                                    <th class="px-4 py-2.5">Apport au travail</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                                @foreach($subject->state_of_art as $ref)
                                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                                        <td class="px-4 py-2.5 font-bold text-slate-700 whitespace-nowrap">{{ $ref['author'] ?? '—' }}</td>
                                                        <td class="px-4 py-2.5 text-slate-500 whitespace-nowrap">{{ $ref['institution'] ?? '—' }}</td>
                                                        <td class="px-4 py-2.5 leading-relaxed">{{ $ref['contribution'] ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if($subject->demarcations)
                                <div class="space-y-1">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">B. Démarcations</h4>
                                    <p class="text-sm text-slate-655 leading-relaxed whitespace-pre-line">{{ $subject->demarcations }}</p>
                                </div>
                            @endif
                            @if($subject->methodologies)
                                <div class="space-y-1">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">C. Méthodologies de recherche appliquées</h4>
                                    <p class="text-sm text-slate-655 leading-relaxed whitespace-pre-line">{{ $subject->methodologies }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Deliverable PDFs --}}
                @if($subject->thesisFiles && $subject->thesisFiles->count() > 0)
                    <div class="glass-card rounded-2xl p-6 shadow-sm bg-white/80 backdrop-blur-md border border-slate-200/60 space-y-4">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                            <x-icon name="folder-open" class="h-5 w-5 text-primary" />
                            <span>Documents et versions TFC déposés</span>
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($subject->thesisFiles as $tfile)
                                <div class="bg-slate-50 border border-slate-150 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs font-bold text-slate-800 truncate" title="{{ $tfile->original_name }}">{{ $tfile->original_name }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider {{ $tfile->version_type === 'jury' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                                                {{ $tfile->version_type === 'jury' ? 'Version Jury' : 'Version Finale' }}
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-400">Date dépôt : {{ $tfile->created_at->format('d/m/Y à H:i') }}</p>
                                        
                                        @if($tfile->aiReport && Auth::user()->hasRole('Enseignant') && $subject->teacher_id === Auth::id())
                                            <div class="pt-1.5 flex flex-wrap gap-x-3 gap-y-1 text-[10px] font-semibold">
                                                <span class="{{ $tfile->aiReport->ai_score < 20 ? 'text-green-600' : ($tfile->aiReport->ai_score < 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                    Score IA : {{ $tfile->aiReport->ai_score }}%
                                                </span>
                                                <span class="text-slate-300">•</span>
                                                <span class="{{ $tfile->aiReport->similarity_score < 20 ? 'text-green-600' : ($tfile->aiReport->similarity_score < 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                    Similarité : {{ $tfile->aiReport->similarity_score }}%
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('thesis.download', $tfile) }}" class="bg-white hover:bg-primary hover:text-white text-slate-700 text-xs font-bold py-2 px-4 border border-slate-250 shadow-inner rounded-xl transition-all flex items-center justify-center gap-1.5 shrink-0 self-end sm:self-auto">
                                        <x-icon name="arrow-down-tray" class="h-4 w-4" />
                                        <span>Télécharger</span>
                                    </a>
                                </div>
                                
                                {{-- Feedbacks List --}}
                                @if($tfile->feedbacks->count() > 0)
                                    <div class="mt-2 pl-4 border-l-2 border-slate-200 space-y-2">
                                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Retours & Remarques</h4>
                                        @foreach($tfile->feedbacks as $feedback)
                                            <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="text-[10px] font-extrabold text-slate-700">{{ $feedback->author->name }}</span>
                                                    <span class="text-[9px] text-slate-400">{{ $feedback->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <p class="text-xs text-slate-600">{{ $feedback->content_remarque }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Add Feedback Form (Encadrants et CP) --}}
                                @if(Auth::user()->hasAnyRole(['Enseignant', 'Chef de département']))
                                    <form action="{{ route('feedbacks.store', $tfile) }}" method="POST" class="mt-2 pl-4 border-l-2 border-blue-100">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" name="content_remarque" required minlength="3" placeholder="Ajouter une remarque..." class="flex-1 text-xs rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <button type="submit" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-bold transition-colors">
                                                Envoyer
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                
            </div>

        </div>

        {{-- BACK LINKS ROW --}}
        <div class="flex justify-between items-center gap-4 pt-4 border-t border-slate-200/50">
            <a href="{{ route('subjects.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 transition-colors flex items-center gap-1">
                <span>← Retour aux sujets de TFC</span>
            </a>
            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-primary hover:text-primary-light transition-colors flex items-center gap-1">
                <span>Mon Espace de Travail →</span>
            </a>
        </div>

    </div>
</x-app-layout>
