@props([
    'endpoint' => '/api/v1/subjects',
    'emptyMessage' => 'Aucun TFC n\'a été trouvé.',
    'emptyTitle' => 'Aucun résultat'
])

<div x-data="tfcList('{{ $endpoint }}')" x-init="fetchData()" class="relative min-h-[300px]">

    {{-- ÉTAT 1 : CHARGEMENT (Skeleton Screen) --}}
    <div x-show="loading" class="space-y-4 animate-pulse">
        @for($i = 0; $i < 4; $i++)
            <div class="bg-white rounded-xl p-6 border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 space-y-3 w-full">
                    <div class="h-5 bg-slate-200 rounded w-3/4"></div>
                    <div class="h-4 bg-slate-100 rounded w-1/2"></div>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="h-8 bg-slate-100 rounded-full w-24"></div>
                    <div class="h-8 bg-slate-100 rounded-full w-24"></div>
                </div>
            </div>
        @endfor
    </div>

    {{-- ÉTAT 2 : ERREUR (Problème DB ou réseau) --}}
    <div x-show="error && !loading" style="display: none;" class="bg-red-50/80 backdrop-blur-sm border border-red-200 p-8 rounded-2xl text-center max-w-2xl mx-auto shadow-inner">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-500 mb-4 shadow-sm border border-red-200">
            <x-icon name="exclamation-triangle" class="w-8 h-8" />
        </div>
        <h3 class="text-xl font-bold text-red-800 mb-2">Erreur de connexion</h3>
        <p class="text-red-600 mb-6">Impossible de charger la liste des TFC. La base de données ne répond pas ou une erreur est survenue.</p>
        <button @click="fetchData()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition-all hover:-translate-y-0.5">
            <x-icon name="arrow-path" class="w-5 h-5" />
            Réessayer
        </button>
    </div>

    {{-- ÉTAT 3 : VIDE (Empty State avec message sympa) --}}
    <div x-show="!loading && !error && tfcs.length === 0" style="display: none;">
        <x-empty-state 
            :title="$emptyTitle"
            :description="$emptyMessage"
            icon="document-text"
        />
    </div>

    {{-- ÉTAT 4 : SUCCÈS (Liste chargée) --}}
    <div x-show="!loading && !error && tfcs.length > 0" style="display: none;" class="space-y-4">
        <template x-for="tfc in tfcs" :key="tfc.id">
            <div class="bg-white rounded-xl p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition-all hover:border-slate-300 group flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <a :href="'/subjects/' + tfc.id" class="text-lg font-semibold text-slate-800 group-hover:text-blue-700 transition-colors inline-block mb-1" x-text="tfc.title"></a>
                    <div class="text-sm text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-2">
                        <span class="inline-flex items-center gap-1">
                            <x-icon name="user" class="w-4 h-4 text-slate-400" />
                            <span x-text="tfc.student?.name || 'Inconnu'"></span>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-icon name="academic-cap" class="w-4 h-4 text-slate-400" />
                            <span x-text="tfc.teacher?.name || 'Non assigné'"></span>
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <template x-if="tfc.status === 'pending'">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200/50 shadow-sm">
                            <x-icon name="clock" class="w-4 h-4" /> En attente
                        </span>
                    </template>
                    <template x-if="tfc.status === 'validated'">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200/50 shadow-sm">
                            <x-icon name="check-circle" class="w-4 h-4" /> Validé
                        </span>
                    </template>
                    <template x-if="tfc.status === 'rejected'">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200/50 shadow-sm">
                            <x-icon name="x-circle" class="w-4 h-4" /> Rejeté
                        </span>
                    </template>
                </div>
            </div>
        </template>
        
        {{-- Pagination info (simplifiée) --}}
        <div class="mt-4 text-center text-sm text-slate-500" x-show="pagination.total > 0">
            <span x-text="pagination.total"></span> résultat(s) trouvé(s).
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('tfcList', (endpoint) => ({
            tfcs: [],
            pagination: {},
            loading: true,
            error: false,
            
            async fetchData() {
                this.loading = true;
                this.error = false;
                
                try {
                    const response = await fetch(endpoint, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    
                    const data = await response.json();
                    this.tfcs = data.data; // Pagination Laravel
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        total: data.total
                    };
                } catch (err) {
                    console.error('Erreur lors du chargement des TFC:', err);
                    this.error = true;
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endpush
