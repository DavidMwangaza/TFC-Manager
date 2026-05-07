<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center gap-2">
            <x-icon name="cog-6-tooth" class="w-6 h-6" /> Tableau de Bord — Administration
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration']]" />
            <div class="flex gap-6">
                {{-- Sidebar --}}
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                {{-- Contenu principal --}}
                <div class="flex-1 space-y-6">

                    {{-- Année académique en cours --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-4 text-white flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Année académique en cours</p>
                            <p class="text-2xl font-bold">{{ $currentYear?->name ?? 'Non définie' }}</p>
                        </div>
                        <a href="{{ route('admin.academic-years.index') }}" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded text-sm transition">
                            Gérer →
                        </a>
                    </div>

                    {{-- Statistiques globales --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.users.index') }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition group">
                            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</div>
                            <div class="text-sm text-gray-500 group-hover:text-blue-600 transition">Utilisateurs</div>
                        </a>
                        <a href="{{ route('admin.departments.index') }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition group">
                            <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_departments'] }}</div>
                            <div class="text-sm text-gray-500 group-hover:text-indigo-600 transition">Filières</div>
                        </a>
                        <div class="bg-white shadow-sm rounded-lg p-5">
                            <div class="text-3xl font-bold text-purple-600">{{ $stats['total_subjects'] }}</div>
                            <div class="text-sm text-gray-500">Sujets total</div>
                        </div>
                        <div class="bg-white shadow-sm rounded-lg p-5">
                            <div class="text-3xl font-bold text-green-600">{{ $stats['validated_subjects'] }}</div>
                            <div class="text-sm text-gray-500">Validés</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white shadow-sm rounded-lg p-5">
                            <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending_subjects'] }}</div>
                            <div class="text-sm text-gray-500">En attente</div>
                        </div>
                        <div class="bg-white shadow-sm rounded-lg p-5">
                            <div class="text-3xl font-bold text-red-600">{{ $stats['rejected_subjects'] }}</div>
                            <div class="text-sm text-gray-500">Rejetés</div>
                        </div>
                        <div class="bg-white shadow-sm rounded-lg p-5">
                            <div class="text-3xl font-bold text-teal-600">{{ $stats['total_faculties'] }}</div>
                            <div class="text-sm text-gray-500">Facultés</div>
                        </div>
                        <a href="{{ route('admin.users.index', ['status' => 'blocked']) }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition group">
                            <div class="text-3xl font-bold {{ $stats['blocked_users'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $stats['blocked_users'] }}</div>
                            <div class="text-sm text-gray-500 group-hover:text-red-600 transition">Utilisateurs bloqués</div>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Graphique : Sujets par statut --}}
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-1.5"><x-icon name="chart-pie" class="w-4 h-4 text-blue-500" /> Répartition des sujets par statut</h3>
                            <div class="flex items-center justify-center" style="height: 250px;">
                                <canvas id="chartStatus"></canvas>
                            </div>
                        </div>

                        {{-- Graphique : Sujets par filière --}}
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-1.5"><x-icon name="chart-bar" class="w-4 h-4 text-indigo-500" /> Sujets par filière</h3>
                            <div style="height: 250px;">
                                <canvas id="chartDept"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Derniers utilisateurs inscrits --}}
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-1.5"><x-icon name="users" class="w-4 h-4" /> Derniers utilisateurs</h3>
                                <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 hover:underline">Voir tous →</a>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse($recentUsers as $u)
                                    <div class="px-4 py-3 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $u->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $u->email }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @foreach($u->roles as $role)
                                                @php
                                                    $colors = [
                                                        'Admin' => 'bg-red-100 text-red-800',
                                                        'Chef de département' => 'bg-blue-100 text-blue-800',
                                                        'Enseignant' => 'bg-purple-100 text-purple-800',
                                                        'Etudiant' => 'bg-green-100 text-green-800',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$role->name] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <p class="px-4 py-6 text-center text-gray-500 text-sm">Aucun utilisateur.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Dernières activités --}}
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-1.5"><x-icon name="clock" class="w-4 h-4" /> Activité récente</h3>
                                <a href="{{ route('admin.logs.index') }}" class="text-xs text-blue-600 hover:underline">Voir tout →</a>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse($recentLogs as $log)
                                    <div class="px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm text-gray-700 truncate max-w-xs">{{ $log->description }}</p>
                                            @php
                                                $actionColors = [
                                                    'created' => 'bg-green-100 text-green-800',
                                                    'updated' => 'bg-blue-100 text-blue-800',
                                                    'deleted' => 'bg-red-100 text-red-800',
                                                    'blocked' => 'bg-red-100 text-red-800',
                                                    'unblocked' => 'bg-green-100 text-green-800',
                                                    'password_reset' => 'bg-yellow-100 text-yellow-800',
                                                    'role_changed' => 'bg-purple-100 text-purple-800',
                                                    'year_closed' => 'bg-orange-100 text-orange-800',
                                                ];
                                                $aColor = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $aColor }}">
                                                {{ $log->action }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $log->user?->name ?? 'Système' }} — {{ $log->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                @empty
                                    <p class="px-4 py-6 text-center text-gray-500 text-sm">Aucune activité enregistrée.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Accès rapides --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-1.5"><x-icon name="rocket-launch" class="w-4 h-4 text-blue-500" /> Accès rapides</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                                <x-icon name="user" class="w-5 h-5" /> Créer un utilisateur
                            </a>
                            <a href="{{ route('admin.departments.create') }}" class="flex items-center gap-2 px-4 py-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition text-sm font-medium">
                                <x-icon name="building-library" class="w-5 h-5" /> Ajouter une filière
                            </a>
                            <a href="{{ route('admin.academic-years.create') }}" class="flex items-center gap-2 px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition text-sm font-medium">
                                <x-icon name="calendar" class="w-5 h-5" /> Nouvelle année
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition text-sm font-medium">
                                <x-icon name="cog-6-tooth" class="w-5 h-5" /> Paramètres système
                            </a>
                        </div>
                    </div>

                    {{-- Note sur les limites du rôle Admin --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-amber-800 mb-2 flex items-center gap-1.5"><x-icon name="information-circle" class="w-5 h-5" /> Rappel — Périmètre du rôle Admin</h4>
                        <ul class="text-xs text-amber-700 space-y-1">
                            <li>• <strong>Vous gérez :</strong> les accès, la structure académique, les paramètres et la maintenance.</li>
                            <li>• <strong>Vous ne validez pas</strong> les sujets (c'est le rôle du Chef de département).</li>
                            <li>• <strong>Vous ne notez pas</strong> les travaux (c'est le rôle du Jury/Directeur).</li>
                            <li>• <strong>Vous ne modifiez pas</strong> le contenu des TFC (titre, fichier) d'un étudiant.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Graphique Doughnut — Statut des sujets
            new Chart(document.getElementById('chartStatus'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartSubjectsByStatus['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartSubjectsByStatus['data']) !!},
                        backgroundColor: {!! json_encode($chartSubjectsByStatus['colors']) !!},
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } },
                    }
                }
            });

            // Graphique Barres — Sujets par filière
            const deptLabels = {!! json_encode($chartSubjectsByDept['labels']) !!};
            const deptData = {!! json_encode($chartSubjectsByDept['data']) !!};
            const deptColors = deptLabels.map((_, i) => {
                const hues = ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e', '#3b82f6', '#06b6d4', '#14b8a6', '#10b981'];
                return hues[i % hues.length];
            });

            new Chart(document.getElementById('chartDept'), {
                type: 'bar',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        label: 'Nombre de sujets',
                        data: deptData,
                        backgroundColor: deptColors,
                        borderRadius: 6,
                        barThickness: 40,
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
                            ticks: { stepSize: 1, precision: 0 },
                            grid: { color: '#f3f4f6' },
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
                                font: { size: 11 },
                            },
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
