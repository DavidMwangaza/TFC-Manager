<x-app-layout>
    <x-slot name="header">
        Mon Espace Étudiant
    </x-slot>

    <div class="space-y-6 animate-fade-in-up">
        
        {{-- HERO SECTION --}}
        @if($subject)
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-900 rounded-2xl p-4 lg:p-6 text-white shadow-xl shadow-slate-950/20">
                <!-- Abstract decorative background glows -->
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-accent tracking-wide">
                            <x-icon name="sparkles" class="h-4 w-4" />
                            <span>Année Académique {{ date('Y') }}-{{ date('Y') + 1 }}</span>
                        </div>
                        <h2 class="font-serif text-2xl lg:text-3xl font-extrabold tracking-tight leading-relaxed">
                            Bonjour, {{ Auth::user()->name }} 
                        </h2>
                        <p class="text-sm text-slate-300 max-w-xl leading-relaxed">
                            Votre parcours de Travail de Fin de Cycle est actif. Suivez vos jalons, collaborez avec votre encadreur et soumettez vos révisions ici.
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <span class="text-xs text-slate-400">Statut du sujet :</span>
                            <x-status-badge :status="$subject->status" />
                            @if($subject->teacher)
                                <span class="inline-flex items-center gap-1 text-xs text-slate-300 bg-white/5 px-2.5 py-1 rounded-lg border border-white/10">
                                    <x-icon name="user" class="h-3.5 w-3.5 text-accent" />
                                    <span>Encadreur : <strong>{{ $subject->teacher->name }}</strong></span>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if($milestoneProgress)
                        <div class="shrink-0 flex items-center justify-center p-4 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 shadow-inner">
                            <x-progress-ring :percent="$milestoneProgress['percent']" size="110" colorClass="text-accent" />
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Welcome Banner without subject --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-900 rounded-2xl p-4 lg:p-6 text-white shadow-xl shadow-slate-950/20">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative space-y-3">
                    <h2 class="font-serif text-2xl lg:text-3xl font-extrabold tracking-tight leading-relaxed">
                        Bienvenue, {{ Auth::user()->name }} 
                    </h2>
                    <p class="text-sm text-slate-300 max-w-xl leading-relaxed">
                        Prêt à commencer vos recherches ? Proposez dès aujourd'hui votre sujet de Travail de Fin de Cycle (TFC) pour initier votre parcours académique.
                    </p>
                </div>
            </div>
        @endif

        {{-- MAIN LAYOUT --}}
        @if(!$subject || ($subject && $subject->status === 'rejected'))
            {{-- State: No subject or Rejected --}}
            <div class="max-w-4xl mx-auto">
                @if($subject && $subject->status === 'rejected')
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex gap-4 animate-fade-in-up">
                        <div class="p-3 bg-red-100 text-red-600 rounded-xl shrink-0 h-fit shadow-sm">
                            <x-icon name="exclamation-circle" class="h-6 w-6" />
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-base font-bold text-red-900">Proposition de sujet rejetée</h4>
                            <p class="text-sm text-red-700"><strong>Motif du rejet :</strong> {{ $subject->rejection_reason }}</p>
                            <p class="text-xs text-red-600">Prenez note de ces remarques et soumettez à nouveau votre fiche ci-dessous.</p>
                        </div>
                    </div>
                @endif

                <x-empty-state 
                    title="Proposer un sujet de TFC" 
                    description="Remplissez la fiche de proposition structurée en 5 étapes pour faire examiner votre sujet par le Chef de Filière."
                    icon="document-text"
                    :actionUrl="route('subjects.create')"
                    actionText="Remplir la fiche de proposition"
                />
                
                <div class="mt-8 bg-amber-50 border border-amber-200/60 rounded-2xl p-5 flex gap-3.5 max-w-xl mx-auto">
                    <x-icon name="information-circle" class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                    <div class="text-xs text-amber-800 leading-relaxed">
                        <strong>Remarque importante :</strong> Une fois soumise, votre fiche sera verrouillée pour analyse par la commission de filière. Aucun changement ne pourra être effectué sauf en cas de demande de révision ou rejet.
                    </div>
                </div>
            </div>
        @else
            {{-- State: Subject exists & Approved/Pending --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT/CENTER: Subject details & Upload --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Subject Proposal Details Accordion --}}
                    <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100">
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <x-icon name="clipboard-document-list" class="h-5 w-5 text-primary" />
                                <span>Fiche de Proposition Actuelle</span>
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20">
                                {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                            </span>
                        </div>
                        
                        <div class="p-4">
                            <h4 class="font-serif text-lg font-extrabold text-slate-800 tracking-tight mb-4 leading-relaxed">
                                {{ $subject->title }}
                            </h4>
                            
                            {{-- Content sections accordion --}}
                            <div x-data="{ activeTab: null }" class="space-y-3">
                                
                                {{-- 1. Problématique --}}
                                <div class="border border-slate-100 rounded-xl overflow-hidden bg-slate-50/30">
                                    <button @click="activeTab = activeTab === 'problem' ? null : 'problem'" 
                                            class="w-full px-5 py-4 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50 flex justify-between items-center transition-colors">
                                        <span class="flex items-center gap-2.5">
                                            <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg"><x-icon name="magnifying-glass" class="h-4 w-4" /></span>
                                            <span>Construction du Problème</span>
                                        </span>
                                        <x-icon name="chevron-down" class="h-4 w-4 transform transition-transform duration-300 text-slate-400" ::class="activeTab === 'problem' ? 'rotate-180 text-primary' : ''" />
                                    </button>
                                    <div x-show="activeTab === 'problem'" x-collapse class="px-5 pb-5 space-y-4 border-t border-slate-100/50 pt-4">
                                        @if($subject->context_relevance)
                                            <div class="space-y-1 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Contexte & Pertinence</span>
                                                <p class="text-sm text-slate-600 leading-relaxed">{{ $subject->context_relevance }}</p>
                                            </div>
                                        @endif
                                        @if($subject->challenges)
                                            <div class="space-y-1 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Défis & Lacunes</span>
                                                <p class="text-sm text-slate-600 leading-relaxed">{{ $subject->challenges }}</p>
                                            </div>
                                        @endif
                                        @if($subject->research_question)
                                            <div class="space-y-1 bg-blue-50/50 p-4 rounded-lg border border-blue-100 shadow-inner">
                                                <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider">Question Principale de Recherche</span>
                                                <p class="text-base text-slate-800 font-bold leading-relaxed">{{ $subject->research_question }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- 2. Objectifs --}}
                                <div class="border border-slate-100 rounded-xl overflow-hidden bg-slate-50/30">
                                    <button @click="activeTab = activeTab === 'objectives' ? null : 'objectives'" 
                                            class="w-full px-5 py-4 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50 flex justify-between items-center transition-colors">
                                        <span class="flex items-center gap-2.5">
                                            <span class="p-1.5 bg-green-50 text-green-600 rounded-lg"><x-icon name="light-bulb" class="h-4 w-4" /></span>
                                            <span>Hypothèses & Objectifs</span>
                                        </span>
                                        <x-icon name="chevron-down" class="h-4 w-4 transform transition-transform duration-300 text-slate-400" ::class="activeTab === 'objectives' ? 'rotate-180 text-primary' : ''" />
                                    </button>
                                    <div x-show="activeTab === 'objectives'" x-collapse class="px-5 pb-5 space-y-4 border-t border-slate-100/50 pt-4">
                                        @if($subject->hypothesis)
                                            <div class="space-y-1 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Hypothèse Globale</span>
                                                <p class="text-sm text-slate-600 leading-relaxed">{{ $subject->hypothesis }}</p>
                                            </div>
                                        @endif
                                        @if($subject->general_objective)
                                            <div class="space-y-1 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Objectif Général</span>
                                                <p class="text-sm text-slate-600 leading-relaxed">{{ $subject->general_objective }}</p>
                                            </div>
                                        @endif
                                        @if($subject->specific_objectives && count($subject->specific_objectives) > 0)
                                            <div class="space-y-2 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Objectifs Spécifiques</span>
                                                <ul class="space-y-1.5">
                                                    @foreach($subject->specific_objectives as $obj)
                                                        <li class="text-sm text-slate-600 flex items-start gap-2">
                                                            <span class="text-green-500 font-bold mt-0.5 shrink-0"></span>
                                                            <span>{{ $obj }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- 3. Cadre scientifique --}}
                                <div class="border border-slate-100 rounded-xl overflow-hidden bg-slate-50/30">
                                    <button @click="activeTab = activeTab === 'science' ? null : 'science'" 
                                            class="w-full px-5 py-4 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50 flex justify-between items-center transition-colors">
                                        <span class="flex items-center gap-2.5">
                                            <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg"><x-icon name="book-open" class="h-4 w-4" /></span>
                                            <span>Cadre Scientifique & Références</span>
                                        </span>
                                        <x-icon name="chevron-down" class="h-4 w-4 transform transition-transform duration-300 text-slate-400" ::class="activeTab === 'science' ? 'rotate-180 text-primary' : ''" />
                                    </button>
                                    <div x-show="activeTab === 'science'" x-collapse class="px-5 pb-5 space-y-4 border-t border-slate-100/50 pt-4">
                                        @if($subject->state_of_art && count($subject->state_of_art) > 0)
                                            <div class="space-y-2">
                                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">État de l'art / Références</span>
                                                <div class="overflow-x-auto rounded-xl border border-slate-100 shadow-sm bg-white">
                                                    <table class="w-full text-left text-xs border-collapse">
                                                        <thead>
                                                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                                                                <th class="px-4 py-2.5">Auteur</th>
                                                                <th class="px-4 py-2.5">Institution</th>
                                                                <th class="px-4 py-2.5">Apport principal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                                            @foreach($subject->state_of_art as $ref)
                                                                <tr class="hover:bg-slate-50/50 transition-colors">
                                                                    <td class="px-4 py-2.5 font-semibold text-slate-700 whitespace-nowrap">{{ $ref['author'] ?? '—' }}</td>
                                                                    <td class="px-4 py-2.5 whitespace-nowrap text-slate-500">{{ $ref['institution'] ?? '—' }}</td>
                                                                    <td class="px-4 py-2.5 leading-relaxed">{{ $ref['contribution'] ?? '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        @if($subject->demarcations)
                                            <div class="space-y-1 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Démarcations</span>
                                                <p class="text-sm text-slate-600 leading-relaxed">{{ $subject->demarcations }}</p>
                                            </div>
                                        @endif
                                        @if($subject->methodologies)
                                            <div class="space-y-1 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Méthodologies envisagées</span>
                                                <p class="text-sm text-slate-600 leading-relaxed">{{ $subject->methodologies }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Defense Schedule Highlight --}}
                    @if($subject->defenseSchedule)
                        @php $ds = $subject->defenseSchedule; @endphp
                        <div class="bg-gradient-to-r from-green-950 via-green-900 to-green-950 text-white rounded-2xl p-4 shadow-lg border border-green-800 relative overflow-hidden">
                            <div class="absolute -right-8 -top-8 w-24 h-24 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-white/10 rounded-xl shrink-0 shadow-inner">
                                    <x-icon name="calendar-days" class="h-6 w-6 text-accent" />
                                </div>
                                <div class="space-y-2 flex-1 min-w-0">
                                    <h4 class="text-base font-bold text-white tracking-wide">Soutenance Planifiée !</h4>
                                    <p class="text-sm text-green-200">
                                        Le <strong>{{ $ds->defense_date->format('d/m/Y à H:i') }}</strong>
                                    </p>
                                    <p class="text-xs text-green-300 flex items-center gap-1">
                                        <x-icon name="map-pin" class="h-3.5 w-3.5" />
                                        Salle : <strong class="text-white ml-1">{{ $ds->room }}</strong>
                                    </p>

                                    {{-- Jury members --}}
                                    @if(isset($ds->jury_members['president']))
                                        <div class="mt-3 pt-3 border-t border-green-800 space-y-1.5 text-xs">
                                            <p class="text-green-400 font-bold uppercase tracking-wider text-[10px]">Composition du Jury</p>
                                            <p class="text-green-200 flex items-center gap-1.5">
                                                <span class="font-bold text-white">Président :</span> {{ $ds->jury_members['president'] }}
                                            </p>
                                            <p class="text-green-200 flex items-center gap-1.5">
                                                <span class="font-bold text-white">Secrétaire :</span> {{ $ds->jury_members['secretary'] ?? '—' }}
                                            </p>
                                            @if(!empty($ds->jury_members['members']))
                                                <p class="text-green-200">
                                                    <span class="font-bold text-white">Membres :</span> {{ implode(', ', $ds->jury_members['members']) }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($subject->defense_date)
                        {{-- Fallback pour les anciens enregistrements --}}
                        <div class="bg-gradient-to-r from-green-950 via-green-900 to-green-950 text-white rounded-2xl p-4 shadow-lg flex items-start gap-4 border border-green-800">
                            <div class="p-3 bg-white/10 rounded-xl shrink-0 shadow-inner">
                                <x-icon name="calendar-days" class="h-6 w-6 text-accent" />
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-base font-bold text-white">Soutenance Planifiée</h4>
                                <p class="text-sm text-green-200">Le <strong>{{ \Carbon\Carbon::parse($subject->defense_date)->format('d/m/Y à H:i') }}</strong></p>
                                <p class="text-xs text-green-300">Lieu : <strong class="text-white">{{ $subject->defense_room ?? 'À confirmer' }}</strong></p>
                            </div>
                        </div>
                    @endif

                    {{-- PDF Upload Deliverables Box --}}
                    @if($subject->isValidated())
                        <div class="glass-card rounded-2xl p-4 shadow-md shadow-slate-100">
                            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <x-icon name="document-arrow-up" class="h-5 w-5 text-primary" />
                                <span>Déposer une version complète du TFC</span>
                            </h3>

                            @php
                                $hasJury = $subject->thesisFiles->where('version_type', 'jury')->count() > 0;
                                $hasFinal = $subject->thesisFiles->where('version_type', 'final')->count() > 0;
                                $canUploadFinal = $subject->defense_validated ?? false;
                            @endphp

                            <form action="{{ route('thesis.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="pdf" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Fichier de manuscrit (PDF)</label>
                                        <input type="file" name="pdf" id="pdf" accept=".pdf" required
                                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 file:transition-colors file:cursor-pointer border border-slate-200 rounded-xl p-1 bg-white focus:outline-none">
                                        @error('pdf') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label for="version_type" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Type de version</label>
                                        <select name="version_type" id="version_type" required
                                            class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-sm font-medium bg-white">
                                            <option value="jury" {{ !$canUploadFinal ? 'selected' : '' }}>Version Jury (Pré-soutenance)</option>
                                            @if($canUploadFinal)
                                                <option value="final">Version Finale (Archivage)</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between gap-4 flex-wrap pt-2">
                                    @if(!$canUploadFinal)
                                        <span class="text-xs text-amber-600 flex items-center gap-1.5 font-medium">
                                            <x-icon name="lock-closed" class="h-4 w-4 shrink-0 text-amber-500" />
                                            <span>La version finale requiert la validation de votre soutenance.</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-green-600 flex items-center gap-1.5 font-medium">
                                            <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-green-500" />
                                            <span>Vous pouvez soumettre votre version finale archivée.</span>
                                        </span>
                                    @endif
                                    
                                    <button type="submit" class="bg-primary hover:bg-primary-light text-white text-sm font-semibold py-2 px-5 rounded-xl hover-lift shadow-md shadow-primary/10 transition-colors flex items-center gap-2">
                                        <x-icon name="arrow-up-tray" class="h-4 w-4" />
                                        <span>Déposer le PDF</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Uploaded Files List --}}
                    @if($subject->thesisFiles->count() > 0)
                        <div class="space-y-4">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                <x-icon name="folder-open" class="h-5 w-5 text-primary" />
                                <span>Mes Documents Déposés</span>
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($subject->thesisFiles->sortByDesc('created_at') as $file)
                                    <div class="glass-card hover-lift rounded-xl p-5 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm bg-white/50">
                                        <div class="space-y-2 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm font-bold text-slate-800 truncate block max-w-xs md:max-w-md" title="{{ $file->original_name }}">
                                                    {{ $file->original_name }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $file->version_type === 'jury' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-green-50 text-green-700 border border-green-100' }}">
                                                    {{ $file->version_type === 'jury' ? 'Version Jury' : 'Version Finale' }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center gap-4 text-xs text-slate-400 font-medium">
                                                <span class="flex items-center gap-1"><x-icon name="calendar" class="h-3.5 w-3.5" /> Déposé le {{ $file->created_at->format('d/m/Y à H:i') }}</span>
                                            </div>


                                        </div>
                                        
                                        <a href="{{ route('thesis.download', $file) }}" class="bg-slate-100 hover:bg-primary hover:text-white text-slate-700 text-xs font-bold py-2 px-4 rounded-xl shadow-inner border border-slate-200/60 transition-all flex items-center gap-1.5 justify-center shrink-0">
                                            <x-icon name="arrow-down-tray" class="h-4 w-4" />
                                            <span>Télécharger PDF</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- RIGHT SIDEBAR: Upcoming actions & Timeline --}}
                <div class="space-y-8">
                    
                    {{-- 1. Next Steps --}}
                    @php
                        $nextMilestone = $subject->milestones->where('status', '!=', 'validated')->sortBy('due_date')->first();
                    @endphp
                    
                    <div class="glass-card rounded-2xl p-4 border-t-4 border-accent shadow-md shadow-slate-100 relative overflow-hidden bg-white">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                            <x-icon name="clock" class="h-4 w-4 text-accent" />
                            <span>Prochaine Action</span>
                        </h3>
                        
                        @if($nextMilestone)
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-800 text-base leading-snug">{{ $nextMilestone->title }}</h4>
                                    <p class="text-xs text-slate-400 flex items-center gap-1 mt-1 font-medium">
                                        <x-icon name="calendar" class="h-3.5 w-3.5 text-slate-300" />
                                        <span>Échéance : <strong>{{ $nextMilestone->due_date?->format('d/m/Y') ?? 'Aujourd\'hui' }}</strong></span>
                                    </p>
                                </div>
                                
                                {{-- Simple visual countdown highlight --}}
                                @if($nextMilestone->due_date)
                                    @php
                                        $diffDays = now()->diffInDays($nextMilestone->due_date, false);
                                    @endphp
                                    @if($diffDays < 0)
                                        <div class="px-3.5 py-2.5 bg-red-50 text-red-800 rounded-xl border border-red-200 text-xs font-semibold animate-urgent-pulse flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                            <span>En retard de {{ abs($diffDays) }} jours !</span>
                                        </div>
                                    @elseif($diffDays <= 7)
                                        <div class="px-3.5 py-2.5 bg-amber-50 text-amber-800 rounded-xl border border-amber-200 text-xs font-semibold animate-urgent-pulse flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0 animate-ping"></span>
                                            <span>Reste {{ $diffDays }} jours pour finaliser.</span>
                                        </div>
                                    @else
                                        <div class="px-3.5 py-2.5 bg-slate-50 text-slate-600 rounded-xl border border-slate-200/60 text-xs font-medium flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-primary-light shrink-0"></span>
                                            <span>Il vous reste {{ $diffDays }} jours.</span>
                                        </div>
                                    @endif
                                @endif
                                
                                @if(in_array($nextMilestone->status, ['pending', 'rejected']))
                                    <a href="{{ route('subjects.show', $subject) }}" class="w-full bg-primary hover:bg-primary-light text-white text-xs font-bold py-2.5 px-4 rounded-xl hover-lift shadow-md shadow-primary/10 transition-colors flex items-center justify-center gap-1.5">
                                        <x-icon name="arrow-up-tray" class="h-4 w-4" />
                                        <span>Soumettre mes travaux</span>
                                    </a>
                                @elseif($nextMilestone->status === 'submitted')
                                    <div class="w-full bg-slate-100 text-slate-500 rounded-xl text-center py-2 text-xs font-bold border border-slate-200 shadow-inner flex items-center justify-center gap-1.5">
                                        <x-icon name="arrow-path" class="h-4 w-4 animate-spin text-slate-400" />
                                        <span>En correction chez l'encadreur</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="py-4 text-center text-slate-400 space-y-2">
                                <x-icon name="check-circle" class="h-8 w-8 text-green-500 mx-auto" />
                                <p class="text-xs font-bold text-slate-700">Tous les jalons sont validés !</p>
                                <p class="text-[10px] leading-relaxed">Félicitations, vous avez complété l'ensemble des livrables de votre recherche.</p>
                            </div>
                        @endif
                    </div>

                    {{-- 2. Vertical Timeline of Milestones --}}
                    <div class="glass-card rounded-2xl p-4 border border-slate-100 shadow-md shadow-slate-100 bg-white">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <x-icon name="flag" class="h-5 w-5 text-primary" />
                            <span>Chronologie des Jalons</span>
                        </h3>
                        
                        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-1.5 before:bottom-1.5 before:w-0.5 before:bg-slate-200">
                            @foreach($subject->milestones->sortBy('due_date') as $index => $milestone)
                                @php
                                    $isCurrent = $nextMilestone && $nextMilestone->id === $milestone->id;
                                    $isValidated = $milestone->status === 'validated';
                                    $isPending = $milestone->status === 'pending';
                                    $isSubmitted = $milestone->status === 'submitted';
                                    $isRejected = $milestone->status === 'rejected';
                                    
                                    if ($isValidated) {
                                        $dotBg = 'bg-green-500 ring-green-100 text-white';
                                        $dotIcon = 'check';
                                    } elseif ($isSubmitted) {
                                        $dotBg = 'bg-blue-500 ring-blue-100 text-white';
                                        $dotIcon = 'arrow-path';
                                    } elseif ($isRejected) {
                                        $dotBg = 'bg-red-500 ring-red-100 text-white';
                                        $dotIcon = 'exclamation-triangle';
                                    } else {
                                        $dotBg = 'bg-slate-200 ring-slate-100 text-slate-500';
                                        $dotIcon = null;
                                    }
                                @endphp
                                
                                <div class="relative group">
                                    <!-- Timeline Node Circle -->
                                    <span class="absolute -left-[29px] top-0.5 flex h-5 w-5 items-center justify-center rounded-full ring-4 {{ $dotBg }} transition-all duration-300">
                                        @if($dotIcon)
                                            <x-icon :name="$dotIcon" class="h-3 w-3 shrink-0" />
                                        @else
                                            <span class="text-[9px] font-extrabold">{{ $index + 1 }}</span>
                                        @endif
                                    </span>
                                    
                                    <div class="space-y-1 pl-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs font-bold text-slate-800 group-hover:text-primary transition-colors block leading-tight">
                                                {{ $milestone->title }}
                                            </span>
                                            
                                            <span class="shrink-0">
                                                <x-status-badge :status="$milestone->status" class="py-0 px-2 text-[9px] font-black rounded-lg" />
                                            </span>
                                        </div>
                                        
                                        <p class="text-[10px] text-slate-400 font-medium">
                                            Échéance : {{ $milestone->due_date?->format('d/m/Y') ?? '—' }}
                                        </p>
                                        
                                        @if($milestone->comments && in_array($milestone->status, ['validated', 'rejected']))
                                            <div class="mt-2 text-[10px] leading-relaxed p-2.5 rounded-lg border border-slate-100 bg-slate-50 text-slate-500 shadow-inner">
                                                <strong>Remarque de l'encadreur :</strong> {{ $milestone->comments }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- 3. Info Rules reminder --}}
                    <div class="bg-slate-900 text-slate-300 rounded-2xl p-4 border border-slate-800 shadow-md shadow-slate-950/10 space-y-3">
                        <h4 class="text-xs font-bold text-accent uppercase tracking-wider flex items-center gap-1.5">
                            <x-icon name="information-circle" class="h-4 w-4 text-accent" />
                            <span>Guide & Règlements</span>
                        </h4>
                        <ul class="text-xs text-slate-400 space-y-2.5 pl-4 list-disc marker:text-accent">
                            <li>Les attributions de direction sont orchestrées par la direction de votre filière d'études.</li>
                            <li>Toute version soumise passe obligatoirement par un diagnostic de plagiat et d'intégrité IA.</li>
                            <li>La validation de la soutenance libère les verrous de téléchargement pour la version d'archivage finale.</li>
                        </ul>
                    </div>

                </div>

            </div>
        @endif

    </div>
</x-app-layout>
