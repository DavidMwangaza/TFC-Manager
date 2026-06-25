<x-app-layout>
    <x-slot name="header">
        Tableau de Bord — Doyen de la Faculté
    </x-slot>

    <div class="space-y-6 animate-fade-in-up">
        
        {{-- FACULTY HERO BANNER --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-900 rounded-2xl p-4 lg:p-6 text-white shadow-xl shadow-slate-950/20">
            <!-- Decorative background elements -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-accent tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                    <span>Supervision Facultaire</span>
                </div>
                <h2 class="font-serif text-2xl lg:text-3xl font-extrabold tracking-tight leading-relaxed">
                    {{ Auth::user()->faculty?->name ?? 'Faculté Non Assignée' }}
                </h2>
                @if(Auth::user()->faculty?->description)
                    <p class="text-sm text-slate-300 max-w-2xl leading-relaxed opacity-90">
                        {{ Auth::user()->faculty?->description }}
                    </p>
                @endif
            </div>
        </div>

        {{-- GLOBAL KPIS SECTION --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-5">
            <x-stat-card 
                title="Total Étudiants" 
                value="{{ $stats['total_students'] }}" 
                icon="users" 
                color="border-primary" 
                delay="0" 
            />
            <x-stat-card 
                title="Encadrants actifs" 
                value="{{ $stats['total_teachers'] }}" 
                icon="briefcase" 
                color="border-blue-500" 
                delay="50" 
            />
            <x-stat-card 
                title="Sujets Proposés" 
                value="{{ $stats['total_subjects'] }}" 
                icon="clipboard-document-list" 
                color="border-slate-400" 
                delay="100" 
            />
            <x-stat-card 
                title="Sujets Validés" 
                value="{{ $stats['validated_subjects'] }}" 
                icon="check-badge" 
                color="border-success" 
                delay="150" 
            />
            <x-stat-card 
                title="Sujets en attente" 
                value="{{ $stats['pending_subjects'] }}" 
                icon="clock" 
                color="border-amber-500" 
                delay="200" 
            />
        </div>

        {{-- SLA DELAY ALERTS AND TEACHER WORKLOAD --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Delay Alerts (SLA) --}}
            <div class="glass-card rounded-2xl overflow-hidden flex flex-col h-full border-t-4 border-red-500 shadow-md shadow-slate-100 bg-white">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <x-icon name="exclamation-triangle" class="h-5 w-5 text-red-500 {{ $delayedSubjects->isNotEmpty() ? 'animate-pulse' : '' }}" /> 
                        <span>Suivi des Jalons (SLA Dépassé)</span>
                    </h3>
                    <span class="bg-red-50 text-red-700 text-[10px] font-black px-2.5 py-0.5 rounded-full border border-red-100">
                        {{ $delayedSubjects->count() }} Alerte(s)
                    </span>
                </div>
                
                <div class="flex-1 bg-slate-50/30">
                    @if($delayedSubjects->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center h-full">
                            <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mb-3 border border-green-100 shadow-sm">
                                <x-icon name="check-circle" class="h-7 w-7 text-green-500" />
                            </div>
                            <p class="text-slate-800 font-bold text-sm">Parfaite ponctualité !</p>
                            <p class="text-slate-400 text-xs mt-0.5">Aucun dépassement de délai détecté sur les jalons.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-slate-100 bg-white">
                            @foreach($delayedSubjects as $subject)
                                <li class="px-4 py-2.5 hover:bg-slate-50/40 transition-colors flex items-center justify-between gap-4">
                                    <div class="min-w-0 space-y-1">
                                        <p class="font-extrabold text-slate-800 text-sm leading-snug">{{ $subject->student->name }}</p>
                                        <div class="flex flex-wrap gap-x-2 gap-y-0.5 text-[10px] font-semibold text-slate-400">
                                            <span class="text-blue-600">{{ $subject->department->name }}</span>
                                            <span>·</span>
                                            <span>Encadreur : {{ $subject->teacher?->name ?? 'Non Assigné' }}</span>
                                        </div>
                                    </div>
                                    
                                    <a href="{{ route('subjects.show', $subject) }}" class="bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold py-1.5 px-3 rounded-lg border border-redNone-150 transition-colors flex items-center gap-1 shrink-0 shadow-sm">
                                        <span>Détail</span>
                                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Teachers Workload (Top 10) --}}
            <div class="glass-card rounded-2xl overflow-hidden flex flex-col h-full border-t-4 border-blue-500 shadow-md shadow-slate-100 bg-white">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <x-icon name="users" class="h-5 w-5 text-blue-500" />
                        <span>Charge Relative d'Encadrement (Top 10)</span>
                    </h3>
                </div>
                
                <div class="flex-1 bg-slate-50/30">
                    @if($teachersWorkload->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center h-full">
                            <p class="text-slate-400 text-xs italic">Aucune donnée d'encadrement disponible.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-slate-100 bg-white">
                            @foreach($teachersWorkload as $teacher)
                                <li class="px-4 py-2.5 hover:bg-slate-50/40 transition-colors">
                                    <div class="flex justify-between items-center gap-4 mb-2">
                                        <div class="min-w-0">
                                            <p class="font-extrabold text-slate-800 text-xs truncate leading-snug">{{ $teacher->name }}</p>
                                            <p class="text-[10px] text-slate-400 font-semibold truncate">{{ $teacher->department->name }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black shrink-0 {{ $teacher->supervised_subjects_count > 5 ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                            {{ $teacher->supervised_subjects_count }} Sujets
                                        </span>
                                    </div>
                                    
                                    {{-- Workload bar indicator (max 8) --}}
                                    @php
                                        $maxLoad = 8;
                                        $percentage = min(100, ($teacher->supervised_subjects_count / $maxLoad) * 100);
                                        $barColor = $percentage > 80 ? 'bg-red-500' : ($percentage > 50 ? 'bg-amber-500' : 'bg-primary');
                                    @endphp
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $barColor }} h-1.5 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>

        {{-- SUMMARY BY DEPARTMENT TABLE --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100 bg-white">
            <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="chart-bar" class="h-5 w-5 text-primary" />
                    <span>Répartition des Sujets par Filière</span>
                </h3>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="px-4 py-2.5">Département / Filière</th>
                            <th class="px-4 py-2.5 text-center">Total Soumis</th>
                            <th class="px-4 py-2.5 text-center text-green-600">Sujets Validés</th>
                            <th class="px-4 py-2.5 text-center text-amber-500">Sujets En Instruction</th>
                            <th class="px-4 py-2.5 text-center text-red-600">Sujets Rejetés</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-650 bg-white font-semibold">
                        @foreach($departmentsData as $dept)
                            @php
                                $totalDept = $dept->validated_count + $dept->pending_count + $dept->rejected_count;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-2 text-sm font-extrabold text-slate-800">{{ $dept->name }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="bg-slate-100 border border-slate-200 text-slate-700 px-2 py-0.5 rounded font-black">{{ $totalDept }}</span>
                                </td>
                                <td class="px-4 py-2 text-center text-green-600 font-black">
                                    {{ $dept->validated_count > 0 ? $dept->validated_count : '—' }}
                                </td>
                                <td class="px-4 py-2 text-center text-amber-500 font-black">
                                    {{ $dept->pending_count > 0 ? $dept->pending_count : '—' }}
                                </td>
                                <td class="px-4 py-2 text-center text-red-600 font-black">
                                    {{ $dept->rejected_count > 0 ? $dept->rejected_count : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- DEAN ROLE GUIDELINE REMINDER --}}
        <div class="bg-slate-900 text-slate-300 rounded-2xl p-4 border border-slate-800 shadow-md shadow-slate-950/10 space-y-3">
            <h4 class="text-xs font-bold text-accent uppercase tracking-wider flex items-center gap-1.5">
                <x-icon name="information-circle" class="h-4 w-4 text-accent" />
                <span>Régulation Facultaire et Droits de Supervision</span>
            </h4>
            <ul class="text-xs text-slate-400 space-y-2 pl-4 list-disc marker:text-accent leading-relaxed">
                <li>Le <strong>Doyen de la Faculté</strong> possède un droit d'audit permanent sur la ponctualité de la recherche dans l'ensemble des départements rattachés.</li>
                <li>Les indicateurs de charge guident la direction lors de l'attribution des directeurs pour préserver la qualité de l'encadrement académique.</li>
            </ul>
        </div>

    </div>
</x-app-layout>
