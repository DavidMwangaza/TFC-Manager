<x-app-layout>
    <x-slot name="header">
        Tableau de Bord — Enseignant & Directeur
    </x-slot>

    <div class="space-y-6 animate-fade-in-up">
        
        {{-- QUICK STATS SECTION --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <x-stat-card 
                title="Étudiants Encadrés" 
                value="{{ $supervisedSubjects->count() }}" 
                icon="users" 
                color="border-primary" 
                delay="0" 
            />
            <x-stat-card 
                title="Versions Jury" 
                value="{{ $supervisedSubjects->filter(fn($s) => $s->thesisFiles->where('version_type', 'jury')->count() > 0)->count() }}" 
                icon="document-check" 
                color="border-success" 
                delay="100" 
            />
            <x-stat-card 
                title="Versions Finales" 
                value="{{ $supervisedSubjects->filter(fn($s) => $s->thesisFiles->where('version_type', 'final')->count() > 0)->count() }}" 
                icon="document-text" 
                color="border-blue-500" 
                delay="200" 
            />
            @php
                $feuVertCount = $supervisedSubjects->filter(fn($s) => !($s->defense_validated ?? false) && $s->thesisFiles->where('version_type', 'jury')->count() > 0)->count();
            @endphp
            <x-stat-card 
                title="Feu Vert Requis" 
                value="{{ $feuVertCount }}" 
                icon="fire" 
                color="{{ $feuVertCount > 0 ? 'border-accent animate-urgent-pulse' : 'border-slate-200' }}" 
                delay="300" 
            />
        </div>

        {{-- JALONS ACTION REQUISE --}}
        @if($pendingMilestones->count() > 0)
            <div class="glass-card rounded-2xl border-l-4 border-amber-500 p-4 shadow-md shadow-slate-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-amber-50 rounded-xl text-amber-600 shrink-0">
                        <x-icon name="exclamation-circle" class="h-6 w-6 animate-pulse" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Corrections en attente</h3>
                        <p class="text-xs text-slate-500 font-medium">Vous avez {{ $pendingMilestones->count() }} livrable(s) soumis à corriger.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach($pendingMilestones as $milestone)
                        @php
                            $isOverdue = $milestone->correction_deadline && $milestone->correction_deadline->isPast();
                        @endphp
                        <div class="group relative rounded-xl border {{ $isOverdue ? 'border-red-200 bg-red-50/20' : 'border-slate-200 hover:border-slate-300' }} p-4 transition-all duration-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1.5 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-sm text-slate-800 truncate">{{ $milestone->title }}</h4>
                                    @if($isOverdue)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-red-100 text-red-700 animate-pulse">
                                            SLA Dépassé !
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold">
                                    <span class="text-primary">{{ $milestone->subject->student->name ?? '—' }}</span>
                                    <span class="text-slate-300 font-normal">|</span>
                                    <span class="text-slate-500 font-medium truncate max-w-xs md:max-w-md">{{ $milestone->subject->title }}</span>
                                </div>

                                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400">
                                    <span class="flex items-center gap-1">
                                        <x-icon name="arrow-up-tray" class="h-3.5 w-3.5" />
                                        Soumis le {{ $milestone->submission_date?->format('d/m/Y') ?? '—' }}
                                    </span>
                                    @if($milestone->correction_deadline)
                                        <span class="flex items-center gap-1 {{ $isOverdue ? 'text-red-600 font-bold' : '' }}">
                                            <x-icon name="clock" class="h-3.5 w-3.5" />
                                            SLA Correction : {{ $milestone->correction_deadline->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="shrink-0 flex items-center gap-2 flex-wrap justify-end">
                                <a href="{{ route('subjects.show', $milestone->subject) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary hover:bg-primary-light text-white text-xs font-bold rounded-lg shadow-sm hover-lift transition">
                                    <x-icon name="eye" class="h-3.5 w-3.5" />
                                    <span>Traiter le jalon</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- STUDENTS LIST SECTION --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="users" class="h-5 w-5 text-primary" />
                    <span>Étudiants sous ma Direction</span>
                </h3>
                <span class="bg-primary/15 text-primary text-xs font-bold px-3 py-1 rounded-full border border-primary/20">
                    {{ $supervisedSubjects->count() }} Étudiant(s)
                </span>
            </div>

            <div class="p-4 bg-slate-50/30 space-y-4">
                @if($supervisedSubjects->count() === 0)
                    <x-empty-state 
                        title="Aucun étudiant assigné" 
                        description="Le Chef de Département ne vous a pas encore assigné d'étudiants pour l'encadrement de TFC."
                        icon="users"
                    />
                @else
                    @foreach($supervisedSubjects as $subject)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-all duration-300">
                            
                            {{-- Student Header Row --}}
                            <div class="bg-gradient-to-r from-slate-50 via-white to-slate-50 px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-11 h-11 bg-primary/10 text-primary border border-primary/20 rounded-xl flex items-center justify-center font-black text-sm shrink-0 shadow-inner">
                                        {{ strtoupper(substr($subject->student->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-slate-800 truncate text-sm leading-snug">{{ $subject->student->name }}</h4>
                                        <p class="text-xs text-slate-400 font-medium truncate mt-0.5">
                                            Matricule : <strong>{{ $subject->student->matricule ?? '—' }}</strong> · {{ $subject->student->email }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-3 shrink-0">
                                    {{-- Principal Director Badge --}}
                                    @if($subject->teacher_id === Auth::id())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">Directeur Principal</span>
                                    @endif

                                    {{-- Defense Authorized status --}}
                                    @if($subject->defense_validated ?? false)
                                        <x-status-badge status="validated" class="px-3" />
                                        @if($subject->defense_date)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-700 bg-green-50 px-2.5 py-1 rounded-lg border border-green-200">
                                                <x-icon name="calendar" class="h-3.5 w-3.5" />
                                                <span>{{ \Carbon\Carbon::parse($subject->defense_date)->format('d/m/Y') }}</span>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="p-4 space-y-4">
                                {{-- Subject Details Accordion --}}
                                <div>
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <p class="font-serif text-base font-extrabold text-primary tracking-tight leading-relaxed">{{ $subject->title }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-slate-100 text-slate-500 uppercase tracking-wider border border-slate-200">
                                            {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                        </span>
                                    </div>

                                    <div x-data="{ showDetails: false }" class="mt-2.5">
                                        <button @click="showDetails = !showDetails" class="inline-flex items-center gap-1.5 text-xs text-primary hover:text-primary-light font-bold transition-colors">
                                            <x-icon name="chevron-down" class="h-3.5 w-3.5 transform transition-transform duration-200" ::class="showDetails ? 'rotate-180' : ''" />
                                            <span x-text="showDetails ? 'Masquer la fiche de sujet' : 'Visualiser la fiche de sujet'"></span>
                                        </button>
                                        
                                        <div x-show="showDetails" x-collapse class="mt-3 border border-slate-100 rounded-xl bg-slate-50/50 p-4 space-y-3.5 text-xs leading-relaxed text-slate-600 shadow-inner">
                                            @if($subject->research_question)
                                                <div class="space-y-0.5"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Question de Recherche</span><p class="font-extrabold text-slate-800 text-sm leading-snug">{{ $subject->research_question }}</p></div>
                                            @endif
                                            @if($subject->context_relevance)
                                                <div class="space-y-0.5"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Contexte</span><p>{{ $subject->context_relevance }}</p></div>
                                            @endif
                                            @if($subject->hypothesis)
                                                <div class="space-y-0.5"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Hypothèse</span><p>{{ $subject->hypothesis }}</p></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Student files & Deliverables --}}
                                @if($subject->thesisFiles->count() > 0)
                                    <div class="border-t border-slate-100 pt-4 space-y-3">
                                        <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                            <x-icon name="paper-clip" class="h-3.5 w-3.5" />
                                            <span>Versions du manuscrit déposées</span>
                                        </h5>
                                        
                                        <div class="grid grid-cols-1 gap-3">
                                            @foreach($subject->thesisFiles as $file)
                                                <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                                    <div class="space-y-1.5 min-w-0">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="text-xs font-bold text-slate-700 truncate" title="{{ $file->original_name }}">{{ $file->original_name }}</span>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider {{ $file->version_type === 'jury' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-green-50 text-green-700 border border-green-100' }}">
                                                                {{ $file->version_type === 'jury' ? 'Jury' : 'Finale' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400">Déposé le {{ $file->created_at->format('d/m/Y à H:i') }}</p>
                                                        
                                                        @if($file->aiReport)
                                                            <div class="flex flex-wrap gap-4 pt-1">
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="text-[10px] font-bold text-slate-500">Intégrité Générative :</span>
                                                                    <span class="text-xs font-black {{ $file->aiReport->ai_score < 20 ? 'text-green-600' : ($file->aiReport->ai_score < 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                                        {{ $file->aiReport->ai_score }}% IA
                                                                    </span>
                                                                </div>
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="text-[10px] font-bold text-slate-500">Intégrité Rédactionnelle (Simil.) :</span>
                                                                    <span class="text-xs font-black {{ $file->aiReport->similarity_score < 20 ? 'text-green-600' : ($file->aiReport->similarity_score < 50 ? 'text-amber-600' : 'text-red-600') }}">
                                                                        {{ $file->aiReport->similarity_score }}%
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="pt-1">
                                                                <form method="POST" action="{{ route('thesis.request-ai-analysis', $file) }}">
                                                                    @csrf
                                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded text-[10px] font-bold shadow-sm transition-colors">
                                                                        <x-icon name="cpu-chip" class="h-3.5 w-3.5 text-slate-400" />
                                                                        Demander l'analyse IA
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <a href="{{ route('thesis.download', $file) }}" class="bg-white hover:bg-primary hover:text-white text-slate-600 text-xs font-bold py-1.5 px-3.5 rounded-lg border border-slate-200 shadow-sm transition-colors flex items-center justify-center gap-1">
                                                        <x-icon name="arrow-down-tray" class="h-3.5 w-3.5" />
                                                        <span>PDF</span>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- "Feu Vert" / Defense Authorization controls --}}
                                @if($subject->teacher_id === Auth::id())
                                    @php
                                        $hasJuryVersion = $subject->thesisFiles->where('version_type', 'jury')->count() > 0;
                                        $hasFinalVersion = $subject->thesisFiles->where('version_type', 'final')->count() > 0;
                                        $isFeuVert = $subject->defense_validated ?? false;
                                    @endphp
                                    
                                    @if($hasJuryVersion && !$isFeuVert)
                                        {{-- Prompt to grant defense authorization --}}
                                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60 rounded-xl p-4 mt-3">
                                            <div class="flex items-start gap-3">
                                                <x-icon name="exclamation-triangle" class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                                                <div class="space-y-3 flex-1">
                                                    <div>
                                                        <p class="text-xs font-extrabold text-amber-900 leading-tight">Autorisation de défense requise ("Feu Vert")</p>
                                                        <p class="text-[11px] text-amber-700 leading-normal mt-0.5">
                                                            Le manuscrit de pré-soutenance (Jury) est déposé. Si la rédaction est jugée prête et intègre, accordez l'autorisation d'examen devant le jury.
                                                        </p>
                                                    </div>
                                                    <form action="{{ route('subjects.authorize-defense', $subject) }}" method="POST"
                                                          onsubmit="return confirm('Accorder le Feu Vert de soutenance pour {{ $subject->student->name }} ?')">
                                                        @csrf
                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 px-4 rounded-lg shadow-sm hover-lift transition-colors flex items-center gap-1.5">
                                                            <x-icon name="rocket-launch" class="h-3.5 w-3.5" />
                                                            <span>Accorder l'Autorisation (Feu Vert)</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($isFeuVert)
                                        {{-- Feu vert status and revocation tool --}}
                                        <div class="bg-gradient-to-r from-green-50 to-teal-50 border border-green-200 rounded-xl p-4 mt-3">
                                            <div class="flex items-start justify-between gap-4 flex-wrap">
                                                <div class="flex items-start gap-2.5">
                                                    <x-icon name="check-circle" class="h-5 w-5 text-green-600 shrink-0 mt-0.5" />
                                                    <div>
                                                        <p class="text-xs font-extrabold text-green-900 leading-tight">Autorisation de soutenance validée</p>
                                                        <p class="text-[11px] text-green-700 mt-0.5">Le feu vert a été octroyé. L'étudiant peut déposer son manuscrit final pour archivage permanent.</p>
                                                    </div>
                                                </div>
                                                
                                                @if(!$hasFinalVersion)
                                                    <div x-data="{ openRevoke: false }" class="w-full pt-2">
                                                        <button @click="openRevoke = !openRevoke" class="text-xs text-red-600 hover:text-red-800 font-bold transition-colors">
                                                            Retirer l'autorisation de soutenance
                                                        </button>
                                                        
                                                        <form x-show="openRevoke" x-cloak action="{{ route('subjects.revoke-defense', $subject) }}" method="POST" class="mt-3 space-y-2 border-t border-green-100 pt-3">
                                                            @csrf
                                                            @method('DELETE')
                                                            <label for="reason_{{ $subject->id }}" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Motif du retrait de l'autorisation</label>
                                                            <textarea id="reason_{{ $subject->id }}" name="defense_revocation_reason" required minlength="10" rows="2"
                                                                      class="w-full text-xs rounded-lg border-slate-200 shadow-inner focus:ring-red-500 focus:border-red-500"
                                                                      placeholder="Expliquez pourquoi le Feu Vert est révoqué..."></textarea>
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg shadow-sm">
                                                                Confirmer le retrait
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif

                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- GLOBAL MILESTONES STATS --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="flag" class="h-5 w-5 text-primary" />
                    <span>Statistiques Générales des Jalons d'Encadrement</span>
                </h3>
            </div>
            
            <div class="p-4">
                @if($milestoneStats['total'] > 0)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="glass-card rounded-xl p-4 text-center border-l-4 border-slate-400 bg-slate-50/30">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total jalons</span>
                            <span class="text-2xl font-black text-slate-800 block mt-1">{{ $milestoneStats['total'] }}</span>
                        </div>
                        <div class="glass-card rounded-xl p-4 text-center border-l-4 border-amber-400 bg-slate-50/30">
                            <span class="text-[9px] font-bold text-amber-500 uppercase tracking-wider block">En attente</span>
                            <span class="text-2xl font-black text-amber-700 block mt-1">{{ $milestoneStats['pending'] }}</span>
                        </div>
                        <div class="glass-card rounded-xl p-4 text-center border-l-4 border-blue-400 bg-slate-50/30">
                            <span class="text-[9px] font-bold text-blue-500 uppercase tracking-wider block">Soumis</span>
                            <span class="text-2xl font-black text-blue-700 block mt-1">{{ $milestoneStats['submitted'] }}</span>
                        </div>
                        <div class="glass-card rounded-xl p-4 text-center border-l-4 border-green-400 bg-slate-50/30">
                            <span class="text-[9px] font-bold text-green-500 uppercase tracking-wider block">Validés</span>
                            <span class="text-2xl font-black text-green-700 block mt-1">{{ $milestoneStats['validated'] }}</span>
                        </div>
                        <div class="glass-card rounded-xl p-4 text-center border-l-4 border-red-400 bg-slate-50/30">
                            <span class="text-[9px] font-bold text-red-500 uppercase tracking-wider block">Rejetés</span>
                            <span class="text-2xl font-black text-red-700 block mt-1">{{ $milestoneStats['rejected'] }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic text-center py-4">Aucun jalon n'a été configuré sur les sujets d'encadrement.</p>
                @endif
            </div>
        </div>

        {{-- INSTRUCTIONS BOX --}}
        <div class="bg-slate-900 text-slate-300 rounded-2xl p-4 border border-slate-800 shadow-md shadow-slate-950/10 space-y-3">
            <h4 class="text-xs font-bold text-accent uppercase tracking-wider flex items-center gap-1.5">
                <x-icon name="information-circle" class="h-4 w-4 text-accent" />
                <span>Consignes de l'Encadrement Académique</span>
            </h4>
            <ul class="text-xs text-slate-400 space-y-2 pl-4 list-disc marker:text-accent leading-relaxed">
                <li>Le <strong>Feu Vert</strong> de direction principale est un verrou de conformité nécessaire avant la soutenance de l'étudiant.</li>
                <li>L'analyse de similarité et générative s'actualise automatiquement à chaque nouveau dépôt PDF.</li>
                <li>Respectez le SLA de correction (délais contractuels) pour maintenir un rythme d'avancement optimal.</li>
            </ul>
        </div>

    </div>
</x-app-layout>
