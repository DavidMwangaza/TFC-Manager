<x-app-layout>
    <x-slot name="header">
        Tableau de Bord — Chef de Département / Filière
    </x-slot>

    <div class="space-y-6 animate-fade-in-up">
        
        {{-- FILIÈRE HERO BANNER --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-900 rounded-2xl p-4 lg:p-6 text-white shadow-xl shadow-slate-950/20">
            <!-- Decorative glows -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-accent tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                    <span>Département de rattachement</span>
                </div>
                <h2 class="font-serif text-2xl lg:text-3xl font-extrabold tracking-tight leading-relaxed">
                    {{ Auth::user()->department?->name ?? 'Filière Non Assignée' }}
                </h2>
                <p class="text-sm text-slate-300 font-medium opacity-90 flex items-center gap-1.5">
                    <x-icon name="academic-cap" class="h-5 w-5 text-accent" />
                    <span>Faculté : <strong>{{ Auth::user()->department?->faculty?->name ?? '—' }}</strong></span>
                </p>
            </div>
        </div>

        {{-- QUICK STATS SECTION --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <x-stat-card 
                title="Sujets En attente" 
                value="{{ $pendingSubjects->count() }}" 
                icon="clock" 
                color="border-amber-500 {{ $pendingSubjects->count() > 0 ? 'animate-urgent-pulse' : '' }}" 
                delay="0" 
            />
            <x-stat-card 
                title="Sujets Validés" 
                value="{{ $allSubjects->where('status', 'validated')->count() }}" 
                icon="check-badge" 
                color="border-success" 
                delay="100" 
            />
            <x-stat-card 
                title="Sujets Rejetés" 
                value="{{ $allSubjects->where('status', 'rejected')->count() }}" 
                icon="x-circle" 
                color="border-danger" 
                delay="200" 
            />
            <x-stat-card 
                title="Corps Enseignant" 
                value="{{ $teachers->count() }}" 
                icon="users" 
                color="border-primary" 
                delay="300" 
            />
        </div>

        {{-- PENDING SUBJECTS SECTION --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="inbox-arrow-down" class="h-5 w-5 text-amber-500" />
                    <span>Sujets de TFC en Attente de Validation</span>
                </h3>
                <span class="bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200">
                    {{ $pendingSubjects->count() }} nouveau(x) sujet(s)
                </span>
            </div>

            <div class="p-4 bg-slate-50/30">
                @if($pendingSubjects->count() === 0)
                    <x-empty-state 
                        title="Aucun sujet en attente" 
                        description="Tous les sujets de TFC soumis par les étudiants de votre filière ont été traités."
                        icon="check-circle"
                    />
                @else
                    <div class="space-y-6">
                        @foreach($pendingSubjects as $subject)
                            <div x-data="{ activeTab: null, valModal: false, rejModal: false }" class="bg-white rounded-2xl shadow-sm border border-slate-150 overflow-hidden hover:shadow-md transition-all duration-300">
                                
                                {{-- Subject Header --}}
                                <div class="bg-slate-50/70 px-4 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-serif font-extrabold text-slate-800 leading-relaxed tracking-tight text-sm">{{ $subject->title }}</h4>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wider">
                                                {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400 font-medium">
                                            Proposé par <strong>{{ $subject->student->name }}</strong> (Matricule : {{ $subject->student->matricule ?? '—' }}) · Soumis le {{ $subject->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="p-4 space-y-4">
                                    {{-- Submissions details accordions --}}
                                    <div class="border border-slate-100 rounded-xl bg-slate-50/40 divide-y divide-slate-100">
                                        
                                        {{-- Tab: Problem --}}
                                        <div>
                                            <button @click="activeTab = activeTab === 'problem' ? null : 'problem'" class="w-full px-4 py-2 text-left text-xs font-bold text-slate-700 hover:bg-slate-100/50 flex justify-between items-center transition-colors">
                                                <span class="flex items-center gap-2">
                                                    <x-icon name="magnifying-glass" class="h-4 w-4 text-blue-500" />
                                                    <span>Problématique & Question de Recherche</span>
                                                </span>
                                                <x-icon name="chevron-down" class="h-3.5 w-3.5 transform transition-transform duration-200 text-slate-400" ::class="activeTab === 'problem' ? 'rotate-180 text-primary' : ''" />
                                            </button>
                                            <div x-show="activeTab === 'problem'" x-collapse class="px-4 pb-4 pt-1 text-xs text-slate-650 space-y-3">
                                                @if($subject->context_relevance)
                                                    <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm"><span class="text-[9px] font-bold text-blue-600 uppercase tracking-wider block mb-1">Contexte et Pertinence</span> {{ $subject->context_relevance }}</div>
                                                @endif
                                                @if($subject->challenges)
                                                    <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm"><span class="text-[9px] font-bold text-blue-600 uppercase tracking-wider block mb-1">Défis & Lacunes</span> {{ $subject->challenges }}</div>
                                                @endif
                                                @if($subject->research_question)
                                                    <div class="bg-blue-50/50 p-3.5 rounded-lg border border-blue-100 shadow-inner"><span class="text-[9px] font-bold text-blue-800 uppercase tracking-wider block mb-1">Question de Recherche Principale</span> <strong class="text-blue-900 text-sm font-bold leading-normal block">{{ $subject->research_question }}</strong></div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Tab: Objectives --}}
                                        <div>
                                            <button @click="activeTab = activeTab === 'obj' ? null : 'obj'" class="w-full px-4 py-2 text-left text-xs font-bold text-slate-700 hover:bg-slate-100/50 flex justify-between items-center transition-colors">
                                                <span class="flex items-center gap-2">
                                                    <x-icon name="light-bulb" class="h-4 w-4 text-green-500" />
                                                    <span>Hypothèses & Objectifs Visés</span>
                                                </span>
                                                <x-icon name="chevron-down" class="h-3.5 w-3.5 transform transition-transform duration-200 text-slate-400" ::class="activeTab === 'obj' ? 'rotate-180 text-primary' : ''" />
                                            </button>
                                            <div x-show="activeTab === 'obj'" x-collapse class="px-4 pb-4 pt-1 text-xs text-slate-650 space-y-3">
                                                @if($subject->hypothesis)
                                                    <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm"><span class="text-[9px] font-bold text-green-600 uppercase tracking-wider block mb-1">Hypothèse Globale</span> {{ $subject->hypothesis }}</div>
                                                @endif
                                                @if($subject->general_objective)
                                                    <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm"><span class="text-[9px] font-bold text-green-600 uppercase tracking-wider block mb-1">Objectif Général</span> {{ $subject->general_objective }}</div>
                                                @endif
                                                @if($subject->specific_objectives && count($subject->specific_objectives) > 0)
                                                    <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                        <span class="text-[9px] font-bold text-green-600 uppercase tracking-wider block mb-1">Objectifs Spécifiques</span>
                                                        <ul class="list-disc list-inside mt-2 space-y-1 pl-1">
                                                            @foreach($subject->specific_objectives as $obj)
                                                                <li>{{ $obj }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Tab: Science --}}
                                        <div>
                                            <button @click="activeTab = activeTab === 'science' ? null : 'science'" class="w-full px-4 py-2 text-left text-xs font-bold text-slate-700 hover:bg-slate-100/50 flex justify-between items-center transition-colors">
                                                <span class="flex items-center gap-2">
                                                    <x-icon name="book-open" class="h-4 w-4 text-blue-500" />
                                                    <span>Références & Cadre Scientifique</span>
                                                </span>
                                                <x-icon name="chevron-down" class="h-3.5 w-3.5 transform transition-transform duration-200 text-slate-400" ::class="activeTab === 'science' ? 'rotate-180 text-primary' : ''" />
                                            </button>
                                            <div x-show="activeTab === 'science'" x-collapse class="px-4 pb-4 pt-1 text-xs text-slate-650 space-y-3">
                                                @if($subject->state_of_art && count($subject->state_of_art) > 0)
                                                    <div class="space-y-1">
                                                        <span class="text-[9px] font-bold text-blue-600 uppercase tracking-wider block mb-1">État de l'art</span>
                                                        <div class="overflow-x-auto rounded-xl border border-slate-100 shadow-sm bg-white">
                                                            <table class="w-full text-left text-xs border-collapse">
                                                                <thead>
                                                                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                                                                        <th class="px-3 py-2">Auteur</th>
                                                                        <th class="px-3 py-2">Institution</th>
                                                                        <th class="px-3 py-2">Apport</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-slate-100">
                                                                    @foreach($subject->state_of_art as $ref)
                                                                        <tr class="text-slate-600 hover:bg-slate-50/50 transition-colors">
                                                                            <td class="px-3 py-2 font-semibold text-slate-700 whitespace-nowrap">{{ $ref['author'] ?? '—' }}</td>
                                                                            <td class="px-3 py-2 text-slate-500 whitespace-nowrap">{{ $ref['institution'] ?? '—' }}</td>
                                                                            <td class="px-3 py-2 leading-relaxed">{{ $ref['contribution'] ?? '—' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @if($subject->demarcations)
                                                        <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm"><span class="text-[9px] font-bold text-blue-600 uppercase tracking-wider block mb-1">Démarcations</span> {{ $subject->demarcations }}</div>
                                                    @endif
                                                    @if($subject->methodologies)
                                                        <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-sm"><span class="text-[9px] font-bold text-blue-600 uppercase tracking-wider block mb-1">Méthodologies</span> {{ $subject->methodologies }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    
                                    {{-- Actions Row --}}
                                    <div class="flex items-center gap-3 pt-3">
                                        <button @click="valModal = true" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 px-4 rounded-xl hover-lift shadow-sm hover:shadow transition-all flex items-center gap-1.5">
                                            <x-icon name="check-circle" class="h-4 w-4" />
                                            <span>Valider & Assigner</span>
                                        </button>
                                        
                                        <button @click="rejModal = true" class="bg-white hover:bg-red-50 text-red-600 border border-red-200 hover:border-red-300 text-xs font-semibold py-2 px-4 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                                            <x-icon name="x-circle" class="h-4 w-4" />
                                            <span>Rejeter Sujet</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- VAL MODAL --}}
                                <div x-show="valModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                                    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                                        <div @click="valModal = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
                                        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full">
                                            <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex justify-between items-center">
                                                <h3 class="text-sm font-bold text-green-900">Validation et Assignation Direction</h3>
                                                <button @click="valModal = false" class="text-green-500 hover:text-green-700"><x-icon name="x-mark" class="h-5 w-5"/></button>
                                            </div>
                                            <div class="px-6 py-6">
                                                <form action="{{ route('subjects.validate', $subject) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    <div class="space-y-1.5">
                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Directeur Principal d'Encadrement <span class="text-red-500">*</span></label>
                                                        <select name="teacher_id" required class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-sm font-medium bg-white">
                                                            <option value="">— Sélectionner un Enseignant —</option>
                                                            @foreach($teachers as $teacher)
                                                                <option value="{{ $teacher->id }}">
                                                                    {{ $teacher->name }} ({{ $teacher->supervisedSubjects->count() }} TFC encadré(s))
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    

                                                    <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-xs leading-normal font-semibold flex gap-2 border border-blue-150">
                                                        <x-icon name="information-circle" class="h-5 w-5 shrink-0" />
                                                        <span>L'étudiant et les encadrants recevront instantanément des notifications de démarrage.</span>
                                                    </div>
                                                    
                                                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                                                        <button type="button" @click="valModal = false" class="px-4 py-2 text-xs font-bold text-slate-650 hover:bg-slate-50 border border-slate-200 rounded-xl transition-colors">Annuler</button>
                                                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-green-600 hover:bg-green-700 shadow-sm hover:shadow rounded-xl transition-colors">Confirmer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- REJ MODAL --}}
                                <div x-show="rejModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                                    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                                        <div @click="rejModal = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
                                        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full">
                                            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                                                <h3 class="text-sm font-bold text-red-900 font-bold">Rejeter la Proposition</h3>
                                                <button @click="rejModal = false" class="text-red-500 hover:text-red-700"><x-icon name="x-mark" class="h-5 w-5"/></button>
                                            </div>
                                            <div class="px-6 py-6">
                                                <form action="{{ route('subjects.reject', $subject) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    <div class="space-y-1.5">
                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Motif académique du rejet <span class="text-red-500">*</span></label>
                                                        <p class="text-[10px] text-slate-400">Détaillez rigoureusement le motif de révision de la problématique ou du cadre.</p>
                                                        <textarea name="rejection_reason" required minlength="10" rows="4"
                                                                  class="w-full text-xs rounded-lg border-slate-200 shadow-inner focus:ring-red-500 focus:border-red-500"
                                                                  placeholder="Décrivez précisément les lacunes identifiées..."></textarea>
                                                    </div>
                                                    
                                                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                                                        <button type="button" @click="rejModal = false" class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl">Annuler</button>
                                                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm rounded-xl">Rejeter</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- FILIÈRE SUBJECTS LIST DATA-TABLE --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100 bg-white">
            <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="clipboard-document-list" class="h-5 w-5 text-primary" />
                    <span>Tous les Sujets du Département</span>
                </h3>
            </div>

            @if($allSubjects->count() === 0)
                <div class="p-4">
                    <p class="text-xs text-slate-400 italic text-center py-6">Aucun sujet n'a encore été initié dans ce département.</p>
                </div>
            @else
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                <th class="px-4 py-2.5">Étudiant / Matricule</th>
                                <th class="px-4 py-2.5">Titre du sujet / Type</th>
                                <th class="px-4 py-2.5">Encadrement</th>
                                <th class="px-4 py-2.5 text-center">État Visuel</th>
                                <th class="px-4 py-2.5 text-center">Documents</th>
                                <th class="px-4 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-650 bg-white">
                            @foreach($allSubjects as $subj)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="font-extrabold text-slate-800 text-sm leading-tight">{{ $subj->student->name }}</div>
                                        <div class="text-slate-400 mt-0.5 font-medium">Mat. : <strong>{{ $subj->student->matricule ?? '—' }}</strong></div>
                                    </td>
                                    <td class="px-4 py-2 max-w-xs md:max-w-md truncate" title="{{ $subj->title }}">
                                        <div class="font-serif font-bold text-slate-800 truncate text-xs tracking-tight leading-relaxed">{{ $subj->title }}</div>
                                        <span class="inline-flex items-center px-1.5 py-0.5 mt-1 rounded text-[8px] font-bold uppercase tracking-wider {{ $subj->subject_type === 'tfc' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                            {{ $subj->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-slate-700 font-semibold">
                                        {{ $subj->teacher?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <x-status-badge :status="$subj->status" />
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        @php
                                            $hasJury = $subj->thesisFiles->where('version_type', 'jury')->count() > 0;
                                            $hasFinal = $subj->thesisFiles->where('version_type', 'final')->count() > 0;
                                        @endphp
                                        <div class="flex items-center justify-center gap-1">
                                            @if($hasJury) <span class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded text-[9px] font-bold border border-blue-100">Jury</span> @endif
                                            @if($hasFinal) <span class="bg-green-50 text-green-700 px-1.5 py-0.5 rounded text-[9px] font-bold border border-green-100">Final</span> @endif
                                            @if(!$hasJury && !$hasFinal) <span class="text-slate-300 text-[10px]">—</span> @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-right whitespace-nowrap">
                                        @if($subj->defense_validated)
                                            <div x-data="{ scheModal: false }" class="inline-block">
                                                <button @click="scheModal = true" class="bg-slate-100 border border-slate-200 hover:bg-primary hover:text-white text-slate-700 text-[10px] font-bold py-1.5 px-3 rounded-lg shadow-sm transition-all inline-flex items-center gap-1">
                                                    <x-icon name="calendar" class="h-3.5 w-3.5" />
                                                    <span>Planifier soutenance</span>
                                                </button>
                                                
                                                {{-- PLAN MODAL --}}
                                                <div x-show="scheModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                                                    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                                                        <div @click="scheModal = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
                                                        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full">
                                                            <div class="bg-primary/5 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                                                                <h3 class="text-sm font-bold text-slate-800">Planifier la soutenance académique</h3>
                                                                <button @click="scheModal = false" class="text-slate-400 hover:text-slate-650"><x-icon name="x-mark" class="h-5 w-5"/></button>
                                                            </div>
                                                            <div class="px-6 py-6 text-left">
                                                                <form action="{{ route('subjects.schedule-defense', $subj) }}" method="POST" class="space-y-4">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <div class="space-y-1.5">
                                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Date & Heure <span class="text-red-500">*</span></label>
                                                                        <input type="datetime-local" name="defense_date" required class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-sm font-medium bg-white" value="{{ $subj->defense_date ? $subj->defense_date->format('Y-m-d\TH:i') : '' }}">
                                                                    </div>
                                                                    
                                                                    <div class="space-y-1.5">
                                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Salle d'Examen <span class="text-red-500">*</span></label>
                                                                        <div class="relative">
                                                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                                                                <x-icon name="map-pin" class="h-4 w-4" />
                                                                            </span>
                                                                            <input type="text" name="defense_room" required class="pl-9 w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-sm font-medium bg-white" value="{{ $subj->defenseSchedule?->room ?? $subj->defense_room }}" placeholder="Ex. Salle 3D, Bloc 2">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="space-y-1.5">
                                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Président du Jury <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="jury_president" required class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-sm font-medium bg-white" value="{{ $subj->defenseSchedule?->jury_members['president'] ?? '' }}" placeholder="Nom complet du président">
                                                                    </div>
                                                                    
                                                                    <div class="space-y-1.5">
                                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Secrétaire du Jury <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="jury_secretary" required class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-sm font-medium bg-white" value="{{ $subj->defenseSchedule?->jury_members['secretary'] ?? '' }}" placeholder="Nom complet du secrétaire">
                                                                    </div>
                                                                    
                                                                    <div class="space-y-1.5">
                                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Autres Membres du Jury</label>
                                                                        <input type="text" name="jury_members" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-sm font-medium bg-white" value="{{ isset($subj->defenseSchedule?->jury_members['members']) ? implode(', ', $subj->defenseSchedule->jury_members['members']) : '' }}" placeholder="Séparés par des virgules">
                                                                        <p class="text-[10px] text-slate-400 mt-1">Séparez les noms par des virgules</p>
                                                                    </div>
                                                                    
                                                                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                                                                        <button type="button" @click="scheModal = false" class="px-4 py-2 text-xs font-bold text-slate-650 border border-slate-200 rounded-xl">Annuler</button>
                                                                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-primary-light shadow-sm rounded-xl">Planifier</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-slate-400 font-bold bg-slate-50 px-2 py-1 rounded">En cours de rédaction</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- TEACHER WORKLOAD SPLIT GRID --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100 bg-white">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="users" class="h-5 w-5 text-primary" />
                    <span>Répartition des charges d'encadrement par enseignant</span>
                </h3>
            </div>
            
            <div class="p-4 bg-slate-50/30">
                @if($teachers->count() === 0)
                    <p class="text-xs text-slate-450 italic text-center">Aucun enseignant rattaché à ce département.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($teachers as $teacher)
                            @php
                                $count = $teacher->supervisedSubjects->count();
                                
                                // Explicit color classes mapping to avoid tailwind purge dynamic issues
                                if ($count === 0) {
                                    $border = 'border-slate-100';
                                    $hoverBorder = 'hover:border-slate-300';
                                    $circleBg = 'bg-slate-50';
                                    $circleText = 'text-slate-600';
                                    $barBg = 'bg-slate-300';
                                    $badge = 'text-slate-400';
                                } elseif ($count <= 3) {
                                    $border = 'border-green-100';
                                    $hoverBorder = 'hover:border-green-300';
                                    $circleBg = 'bg-green-50';
                                    $circleText = 'text-green-700';
                                    $barBg = 'bg-green-500';
                                    $badge = 'text-green-500';
                                } elseif ($count <= 6) {
                                    $border = 'border-amber-100';
                                    $hoverBorder = 'hover:border-amber-300';
                                    $circleBg = 'bg-amber-50';
                                    $circleText = 'text-amber-700';
                                    $barBg = 'bg-amber-500';
                                    $badge = 'text-amber-600';
                                } else {
                                    $border = 'border-red-100';
                                    $hoverBorder = 'hover:border-red-300';
                                    $circleBg = 'bg-red-50';
                                    $circleText = 'text-red-700';
                                    $barBg = 'bg-red-500';
                                    $badge = 'text-red-600';
                                }
                                
                                $percentage = min(100, ($count / 8) * 100);
                            @endphp
                            
                            <div class="bg-white border {{ $border }} {{ $hoverBorder }} rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300 group">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="space-y-1">
                                        <p class="font-extrabold text-slate-800 text-sm leading-snug group-hover:text-primary transition-colors">{{ $teacher->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-semibold">{{ $teacher->email }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl {{ $circleBg }} {{ $circleText }} border border-slate-100 flex items-center justify-center font-black text-base shrink-0 shadow-inner">
                                        {{ $count }}
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-4 overflow-hidden">
                                    <div class="{{ $barBg }} h-1.5 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="text-right text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-wider">
                                    Charge relative (Max 8)
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- INSTRUCTIONS RULES REMINDER --}}
        <div class="bg-slate-900 text-slate-300 rounded-2xl p-4 border border-slate-800 shadow-md shadow-slate-950/10 space-y-3">
            <h4 class="text-xs font-bold text-accent uppercase tracking-wider flex items-center gap-1.5">
                <x-icon name="information-circle" class="h-4 w-4 text-accent" />
                <span>Guide de Régulation de la Filière Académique</span>
            </h4>
            <ul class="text-xs text-slate-400 space-y-2 pl-4 list-disc marker:text-accent leading-relaxed">
                <li>Le <strong>Chef de Département</strong> est l'autorité d'approbation et d'assignation initiale de la direction d'études.</li>
                <li>Garantissez une répartition équitable des charges d'encadrement en surveillant la jauge relative de chaque enseignant.</li>
                <li>Les validations et les rejets déclenchent des notifications immédiates par courriel et système pour les étudiants.</li>
            </ul>
        </div>

    </div>
</x-app-layout>
