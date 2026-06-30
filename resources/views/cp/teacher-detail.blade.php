<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cp.dashboard') }}" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div class="p-2 bg-primary/10 text-primary rounded-xl">
                <x-icon name="user" class="w-5 h-5" />
            </div>
            Profil Directeur — {{ $teacher->name }}
        </div>
    </x-slot>

    <div class="space-y-6 animate-fade-in-up">

        {{-- BREADCRUMB --}}
        <x-breadcrumb :items="[
            ['label' => 'Tableau de Bord CP', 'url' => route('cp.dashboard')],
            ['label' => 'Directeur : ' . $teacher->name]
        ]" />

        {{-- TEACHER HERO BANNER --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-900 rounded-2xl p-5 lg:p-7 text-white shadow-xl shadow-slate-950/20">
            <div class="absolute -right-16 -top-16 w-72 h-72 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-72 h-72 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative flex flex-col md:flex-row items-start md:items-center gap-6">
                {{-- Avatar Initials --}}
                <div class="shrink-0 w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-2xl font-black text-accent backdrop-blur-md shadow-inner select-none">
                    {{ strtoupper(substr($teacher->name, 0, 1)) }}{{ strtoupper(substr(strstr($teacher->name, ' ') ?: ' ', 1, 1)) }}
                </div>

                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-accent tracking-wide">
                        <x-icon name="academic-cap" class="h-3.5 w-3.5" />
                        <span>Directeur de Mémoire / TFC</span>
                    </div>
                    <h2 class="font-serif text-2xl lg:text-3xl font-extrabold tracking-tight">{{ $teacher->name }}</h2>
                    <p class="text-sm text-slate-300 flex items-center gap-2">
                        <x-icon name="envelope" class="h-4 w-4 text-slate-400" />
                        {{ $teacher->email }}
                    </p>
                </div>

                {{-- Stats rapides --}}
                @php
                    $totalCount     = $teacher->supervisedSubjects->count();
                    $validatedCount = $teacher->supervisedSubjects->where('status', 'validated')->count();
                    $pendingCount   = $teacher->supervisedSubjects->where('status', 'pending')->count();
                @endphp
                <div class="shrink-0 flex gap-3 flex-wrap">
                    <div class="text-center bg-white/10 backdrop-blur-md rounded-xl px-4 py-3 border border-white/10">
                        <div class="text-2xl font-black text-white">{{ $totalCount }}</div>
                        <div class="text-[10px] text-slate-300 font-semibold uppercase tracking-wider mt-0.5">Total</div>
                    </div>
                    <div class="text-center bg-white/10 backdrop-blur-md rounded-xl px-4 py-3 border border-white/10">
                        <div class="text-2xl font-black text-green-400">{{ $validatedCount }}</div>
                        <div class="text-[10px] text-slate-300 font-semibold uppercase tracking-wider mt-0.5">Validés</div>
                    </div>
                    <div class="text-center bg-white/10 backdrop-blur-md rounded-xl px-4 py-3 border border-white/10">
                        <div class="text-2xl font-black text-amber-400">{{ $pendingCount }}</div>
                        <div class="text-[10px] text-slate-300 font-semibold uppercase tracking-wider mt-0.5">En cours</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- LISTE DES ETUDIANTS DIRIGES --}}
        <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100 bg-white">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="users" class="h-5 w-5 text-primary" />
                    <span>Étudiants encadrés ({{ $totalCount }})</span>
                </h3>
                @if($totalCount > 0)
                    <span class="text-xs text-slate-400 font-semibold bg-slate-100 px-2.5 py-1 rounded-full hidden sm:block">
                        Cliquez sur un étudiant pour voir l'évolution de son sujet
                    </span>
                @endif
            </div>

            @if($teacher->supervisedSubjects->isEmpty())
                <div class="p-8">
                    <x-empty-state
                        title="Aucun étudiant encadré"
                        description="Cet enseignant n'a pas encore été assigné à un sujet de TFC ou de Mémoire dans votre département."
                        icon="user-group"
                    />
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($teacher->supervisedSubjects as $subject)
                        @php
                            $milestones      = $subject->milestones;
                            $totalMilestones = $milestones->count();
                            $validatedMils   = $milestones->where('status', 'validated')->count();
                            $submittedMils   = $milestones->where('status', 'submitted')->count();
                            $pendingMils     = $milestones->where('status', 'pending')->count();
                            $percent         = $totalMilestones > 0 ? round(($validatedMils / $totalMilestones) * 100) : 0;
                            $nextMilestone   = $milestones->where('status', '!=', 'validated')->sortBy('due_date')->first();
                            $hasJury         = $subject->thesisFiles->where('version_type', 'jury')->count() > 0;
                            $hasFinal        = $subject->thesisFiles->where('version_type', 'final')->count() > 0;
                        @endphp

                        <a href="{{ route('subjects.show', $subject) }}"
                           class="group flex flex-col md:flex-row items-start md:items-center gap-4 px-5 py-4 hover:bg-primary/5 transition-all duration-200 cursor-pointer">

                            {{-- Étudiant Info --}}
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-extrabold text-sm border border-primary/10 select-none">
                                    {{ strtoupper(substr($subject->student->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0 space-y-0.5">
                                    <p class="font-extrabold text-slate-800 text-sm group-hover:text-primary transition-colors leading-tight truncate">
                                        {{ $subject->student->name ?? '—' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 font-semibold">
                                        Mat. {{ $subject->student->matricule ?? '—' }}
                                        @if($subject->student->email)
                                            · {{ $subject->student->email }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Sujet info --}}
                            <div class="flex-[2] min-w-0 space-y-1.5">
                                <p class="font-serif font-bold text-slate-700 text-xs leading-relaxed tracking-tight line-clamp-2 group-hover:text-primary transition-colors">
                                    {{ $subject->title }}
                                </p>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <x-status-badge :status="$subject->status" />
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $subject->subject_type === 'tfc' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        {{ $subject->subject_type === 'tfc' ? 'TFC' : 'Mémoire' }}
                                    </span>
                                    @if($hasJury)
                                        <span class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded text-[9px] font-bold border border-blue-100">PDF Jury</span>
                                    @endif
                                    @if($hasFinal)
                                        <span class="bg-green-50 text-green-700 px-1.5 py-0.5 rounded text-[9px] font-bold border border-green-100">PDF Final</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Milestone Progress --}}
                            <div class="shrink-0 flex items-center gap-4">
                                @if($totalMilestones > 0)
                                    <div class="space-y-1.5 min-w-[140px]">
                                        <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                            <span>Jalons</span>
                                            <span class="{{ $percent === 100 ? 'text-green-600' : 'text-slate-600' }}">{{ $percent }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-700
                                                {{ $percent === 100 ? 'bg-green-500' : ($percent >= 50 ? 'bg-primary' : 'bg-amber-400') }}"
                                                 style="width: {{ $percent }}%">
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-semibold flex-wrap">
                                            @if($validatedMils > 0)
                                                <span class="text-green-600">&#10003; {{ $validatedMils }} valid&#233;{{ $validatedMils > 1 ? 's' : '' }}</span>
                                            @endif
                                            @if($submittedMils > 0)
                                                <span class="text-blue-600">&#8593; {{ $submittedMils }} soumis</span>
                                            @endif
                                            @if($pendingMils > 0)
                                                <span class="text-slate-400">&#9900; {{ $pendingMils }} en attente</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="shrink-0 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <x-progress-ring :percent="$percent" size="52" colorClass="{{ $percent === 100 ? 'text-green-500' : 'text-primary' }}" />
                                    </div>
                                @else
                                    <div class="min-w-[140px] text-center text-[10px] text-slate-300 font-bold py-3 bg-slate-50 rounded-lg border border-slate-100">
                                        Aucun jalon planifi&#233;
                                    </div>
                                @endif
                            </div>

                            {{-- Prochain jalon & fleche --}}
                            <div class="shrink-0 flex items-center gap-3">
                                @if($nextMilestone)
                                    @php $isLate = $nextMilestone->due_date && now()->gt($nextMilestone->due_date); @endphp
                                    <div class="text-right space-y-0.5 hidden xl:block">
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Prochain jalon</p>
                                        <p class="text-xs font-bold text-slate-700 truncate max-w-[120px]" title="{{ $nextMilestone->title }}">
                                            {{ \Illuminate\Support\Str::limit($nextMilestone->title, 20) }}
                                        </p>
                                        @if($nextMilestone->due_date)
                                            <p class="text-[10px] font-semibold {{ $isLate ? 'text-red-500 animate-pulse' : 'text-slate-400' }}">
                                                {{ $isLate ? '&#9888; En retard · ' : '' }}{{ $nextMilestone->due_date->format('d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                @elseif($totalMilestones > 0 && $percent === 100)
                                    <div class="hidden xl:block text-right">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg border border-green-100">
                                            <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                            Tout valid&#233;
                                        </span>
                                    </div>
                                @endif

                                <div class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-primary group-hover:text-white text-slate-400 flex items-center justify-center transition-all shadow-sm shrink-0">
                                    <x-icon name="chevron-right" class="h-4 w-4" />
                                </div>
                            </div>

                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- JALONS EN RETARD DE CET ENSEIGNANT --}}
        @php
            $overdueMilestones = $teacher->supervisedSubjects->flatMap(function($s) {
                return $s->milestones->filter(function($m) {
                    return $m->due_date && now()->gt($m->due_date) && $m->status !== 'validated';
                })->map(function($m) use ($s) {
                    return ['milestone' => $m, 'subject' => $s];
                });
            })->sortBy(fn($item) => $item['milestone']->due_date);
        @endphp

        @if($overdueMilestones->count() > 0)
            <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100 bg-white border-l-4 border-red-500">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-red-50/40 flex items-center gap-2">
                    <x-icon name="exclamation-triangle" class="h-5 w-5 text-red-500" />
                    <h3 class="text-sm font-bold text-red-800">
                        Jalons en Retard ({{ $overdueMilestones->count() }})
                    </h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($overdueMilestones as $item)
                        @php $m = $item['milestone']; $s = $item['subject']; @endphp
                        <a href="{{ route('subjects.show', $s) }}"
                           class="flex items-center gap-4 px-5 py-3 hover:bg-red-50/30 transition-colors group">
                            <div class="p-2 bg-red-100 text-red-600 rounded-lg shrink-0">
                                <x-icon name="clock" class="h-4 w-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate group-hover:text-red-700 transition-colors">
                                    {{ $m->title }}
                                </p>
                                <p class="text-xs text-slate-400 font-medium">
                                    &#201;tudiant : <strong>{{ $s->student->name ?? '&#8212;' }}</strong>
                                    &middot; &#201;ch&#233;ance : <strong class="text-red-500">{{ $m->due_date->format('d/m/Y') }}</strong>
                                    &middot; Statut : <span class="capitalize">{{ $m->status }}</span>
                                </p>
                            </div>
                            <x-icon name="chevron-right" class="h-4 w-4 text-slate-300 group-hover:text-red-500 transition-colors" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- BACK BUTTON --}}
        <div class="flex justify-start pb-4">
            <a href="{{ route('cp.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Retour au tableau de bord
            </a>
        </div>

    </div>
</x-app-layout>
