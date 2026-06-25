<x-app-layout>
    <x-slot name="header">
        Tableau de Bord — Administration Système
    </x-slot>

    <div class="space-y-6 animate-fade-in-up">
        
        {{-- SYSTEM ACADEMIC YEAR HIGHLIGHT --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-900 rounded-2xl p-4 lg:p-6 text-white shadow-xl shadow-slate-950/20">
            <!-- Decorative background elements -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-accent uppercase tracking-wider block">Configuration Globale Actuelle</span>
                    <h2 class="font-serif text-2xl lg:text-3xl font-extrabold tracking-tight leading-relaxed">
                        Année Académique : {{ $currentYear?->name ?? 'Non définie' }}
                    </h2>
                </div>
                
                <a href="{{ route('admin.academic-years.index') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white text-xs font-bold py-2.5 px-5 rounded-xl border border-white/10 shadow-sm hover-lift transition-all flex items-center justify-center gap-1.5 self-start sm:self-auto">
                    <x-icon name="calendar" class="h-4 w-4 text-accent" />
                    <span>Gérer les Cycles Académiques</span>
                </a>
            </div>
        </div>

        {{-- GLOBAL STATISTICS GRIDS --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Métriques Système & Données</h3>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <x-stat-card 
                    title="Utilisateurs Globaux" 
                    value="{{ $stats['total_users'] }}" 
                    icon="users" 
                    color="border-primary" 
                    delay="0" 
                />
                <x-stat-card 
                    title="Départements / Filières" 
                    value="{{ $stats['total_departments'] }}" 
                    icon="academic-cap" 
                    color="border-blue-500" 
                    delay="50" 
                />
                <x-stat-card 
                    title="Total des Sujets" 
                    value="{{ $stats['total_subjects'] }}" 
                    icon="clipboard-document-list" 
                    color="border-blue-500" 
                    delay="100" 
                />
                <x-stat-card 
                    title="Sujets Validés" 
                    value="{{ $stats['validated_subjects'] }}" 
                    icon="check-badge" 
                    color="border-success" 
                    delay="150" 
                />
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <x-stat-card 
                    title="Sujets en Instruction" 
                    value="{{ $stats['pending_subjects'] }}" 
                    icon="clock" 
                    color="border-amber-500" 
                    delay="200" 
                />
                <x-stat-card 
                    title="Sujets Rejetés" 
                    value="{{ $stats['rejected_subjects'] }}" 
                    icon="x-circle" 
                    color="border-danger" 
                    delay="250" 
                />
                <x-stat-card 
                    title="Total Facultés" 
                    value="{{ $stats['total_faculties'] }}" 
                    icon="building-library" 
                    color="border-teal-500" 
                    delay="300" 
                />
                <x-stat-card 
                    title="Comptes Bloqués" 
                    value="{{ $stats['blocked_users'] }}" 
                    icon="shield-exclamation" 
                    color="{{ $stats['blocked_users'] > 0 ? 'border-red-500 animate-urgent-pulse' : 'border-slate-200' }}" 
                    delay="350" 
                />
            </div>
        </div>

        {{-- VISUAL ANALYTICS CHARTS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="glass-card rounded-2xl p-4 shadow-sm bg-white">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <x-icon name="chart-pie" class="h-5 w-5 text-primary" />
                    <span>Progression & Statuts des Sujets</span>
                </h3>
                <div class="relative" style="height: 250px;">
                    <canvas id="chartStatus"></canvas>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-4 shadow-sm bg-white">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <x-icon name="chart-bar" class="h-5 w-5 text-blue-500" />
                    <span>Répartition des Sujets par Filière</span>
                </h3>
                <div class="relative" style="height: 250px;">
                    <canvas id="chartDept"></canvas>
                </div>
            </div>
        </div>

        {{-- AUDIT TRAILS AND RECENT SIGNUPS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            {{-- Latest Inscriptions --}}
            <div class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-sm bg-white">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <x-icon name="users" class="h-5 w-5 text-blue-500" />
                        <span>Derniers Comptes Utilisateurs Créés</span>
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-primary hover:text-primary-light transition">Voir tout →</a>
                </div>
                
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse($recentUsers as $u)
                        <div class="px-4 py-2 hover:bg-slate-50/40 transition-colors flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $u->name }}</p>
                                <p class="text-[10px] text-slate-400 truncate font-semibold mt-0.5">{{ $u->email }}</p>
                            </div>
                            
                            <div class="flex items-center gap-1.5 shrink-0">
                                @foreach($u->roles as $role)
                                    @php
                                        switch ($role->name) {
                                            case 'Admin':
                                                $rStyle = 'bg-red-50 text-red-700 border-red-100';
                                                break;
                                            case 'Chef de département':
                                                $rStyle = 'bg-blue-50 text-blue-700 border-blue-100';
                                                break;
                                            case 'Enseignant':
                                                $rStyle = 'bg-blue-50 text-blue-700 border-blue-100';
                                                break;
                                            case 'Etudiant':
                                                $rStyle = 'bg-green-50 text-green-700 border-green-100';
                                                break;
                                            default:
                                                $rStyle = 'bg-slate-50 text-slate-600 border-slate-100';
                                                break;
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded border text-[8px] font-black uppercase tracking-wider {{ $rStyle }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-center text-slate-400 text-xs">Aucun utilisateur récent.</p>
                    @endforelse
                </div>
            </div>

            {{-- Audit Logs --}}
            <div class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-sm bg-white">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <x-icon name="clock" class="h-5 w-5 text-orange-500" />
                        <span>Journal d'Activité Récente</span>
                    </h3>
                    <a href="{{ route('admin.logs.index') }}" class="text-xs font-bold text-primary hover:text-primary-light transition">Voir tout →</a>
                </div>
                
                <div class="divide-y divide-slate-100 flex-1">
                    @forelse($recentLogs as $log)
                        <div class="px-4 py-2.5 hover:bg-slate-50/40 transition-colors">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <p class="text-xs font-semibold text-slate-700 leading-normal">{{ $log->description }}</p>
                                @php
                                    switch ($log->action) {
                                        case 'created':
                                            $actStyle = 'bg-green-50 text-green-700';
                                            break;
                                        case 'deleted':
                                        case 'blocked':
                                            $actStyle = 'bg-red-50 text-red-700';
                                            break;
                                        case 'updated':
                                        case 'unblocked':
                                            $actStyle = 'bg-blue-50 text-blue-700';
                                            break;
                                        default:
                                            $actStyle = 'bg-slate-50 text-slate-500';
                                            break;
                                    }
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider shrink-0 {{ $actStyle }}">
                                    {{ $log->action }}
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                                <x-icon name="user" class="h-3 w-3 text-slate-300" />
                                <span>Opérateur : {{ $log->user?->name ?? 'Système' }}</span>
                                <span class="text-slate-300">•</span>
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-center text-slate-400 text-xs">Aucun log récent d'activité.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- QUICK LINKS / ACTIONS --}}
        <div class="glass-card rounded-2xl p-4 shadow-sm bg-white">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <x-icon name="rocket-launch" class="h-5 w-5 text-green-500" />
                <span>Actions Administratives Rapides</span>
            </h3>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.users.create') }}" class="group flex items-center gap-3.5 px-4 py-3 bg-slate-50/50 hover:bg-slate-50 border border-slate-150 rounded-xl hover:border-primary-light hover:shadow-md transition-all text-xs font-bold text-slate-700 hover:text-primary">
                    <div class="bg-primary/10 text-primary p-2.5 rounded-xl group-hover:scale-110 transition-transform shrink-0">
                        <x-icon name="user" class="h-5 w-5" />
                    </div>
                    <span>Nouveau Profil</span>
                </a>
                <a href="{{ route('admin.departments.create') }}" class="group flex items-center gap-3.5 px-4 py-3 bg-slate-50/50 hover:bg-slate-50 border border-slate-150 rounded-xl hover:border-blue-400 hover:shadow-md transition-all text-xs font-bold text-slate-700 hover:text-blue-600">
                    <div class="bg-blue-50 text-blue-600 p-2.5 rounded-xl group-hover:scale-110 transition-transform shrink-0">
                        <x-icon name="building-library" class="h-5 w-5" />
                    </div>
                    <span>Nouvelle Filière</span>
                </a>
                <a href="{{ route('admin.academic-years.create') }}" class="group flex items-center gap-3.5 px-4 py-3 bg-slate-50/50 hover:bg-slate-50 border border-slate-150 rounded-xl hover:border-green-400 hover:shadow-md transition-all text-xs font-bold text-slate-700 hover:text-green-600">
                    <div class="bg-green-50 text-green-600 p-2.5 rounded-xl group-hover:scale-110 transition-transform shrink-0">
                        <x-icon name="calendar" class="h-5 w-5" />
                    </div>
                    <span>Nouveau Cycle</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="group flex items-center gap-3.5 px-4 py-3 bg-slate-50/50 hover:bg-slate-50 border border-slate-150 rounded-xl hover:border-blue-400 hover:shadow-md transition-all text-xs font-bold text-slate-700 hover:text-blue-600">
                    <div class="bg-blue-50 text-blue-600 p-2.5 rounded-xl group-hover:scale-110 transition-transform shrink-0">
                        <x-icon name="cog-6-tooth" class="h-5 w-5" />
                    </div>
                    <span>Paramètres Globaux</span>
                </a>
            </div>
        </div>

        {{-- RULES REMINDER --}}
        <div class="bg-slate-900 text-slate-300 rounded-2xl p-4 border border-slate-800 shadow-md shadow-slate-950/10 space-y-3">
            <h4 class="text-xs font-bold text-accent uppercase tracking-wider flex items-center gap-1.5">
                <x-icon name="information-circle" class="h-4 w-4 text-accent" />
                <span>Régulation du Périmètre Administrateur</span>
            </h4>
            <ul class="text-xs text-slate-400 space-y-2 pl-4 list-disc marker:text-accent leading-relaxed">
                <li>Votre champ d'action technique comprend la sécurité du système, l'octroi d'accès utilisateur et la gestion structurelle de la base académique.</li>
                <li>Le contrôle d'intégrité de la recherche, les notes des travaux et l'attribution des directeurs d'études relèvent exclusivement du corps académique (Filières, Enseignants, Doyens).</li>
            </ul>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Premium layout settings
            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.color = '#64748b';

            // Doughnut Status Chart
            new Chart(document.getElementById('chartStatus'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartSubjectsByStatus['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartSubjectsByStatus['data']) !!},
                        backgroundColor: {!! json_encode($chartSubjectsByStatus['colors']) !!},
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { padding: 15, usePointStyle: true, pointStyle: 'circle', font: { size: 11, weight: '600' } } 
                        },
                    }
                }
            });

            // Department Bar Chart
            const deptLabels = {!! json_encode($chartSubjectsByDept['labels']) !!};
            const deptData = {!! json_encode($chartSubjectsByDept['data']) !!};
            const deptColors = deptLabels.map((_, i) => {
                const colors = ['#1e3a8a', '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#14b8a6', '#10b981'];
                return colors[i % colors.length];
            });

            new Chart(document.getElementById('chartDept'), {
                type: 'bar',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        data: deptData,
                        backgroundColor: deptColors,
                        borderRadius: 8,
                        barThickness: 'flex',
                        maxBarThickness: 38,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 10 } },
                            grid: { color: '#f1f5f9', drawBorder: false },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            border: { display: false },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
                                font: { size: 11, weight: '600' },
                            },
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
