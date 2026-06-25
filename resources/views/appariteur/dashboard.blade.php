<x-app-layout>
    <x-slot name="header">
        Bureau Appariteur — Validation Administrative & Financière
    </x-slot>

    <div class="space-y-8 animate-fade-in-up" x-data="{ 
        showValidateModal: false, 
        showRejectModal: false, 
        currentSubject: null,
        currentStudentName: '',
        currentSubjectTitle: ''
    }">
        
        {{-- APPARITEUR STAT CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <x-stat-card 
                title="Dossiers en Attente" 
                value="{{ $pendingFinancial->count() }}" 
                icon="clock" 
                color="border-amber-500 {{ $pendingFinancial->count() > 0 ? 'animate-urgent-pulse' : '' }}" 
                delay="0" 
            />
            <x-stat-card 
                title="Validés Récemment" 
                value="{{ $validatedFinancial->count() }}" 
                icon="check-badge" 
                color="border-success" 
                delay="100" 
            />
            <x-stat-card 
                title="Rejetés Récemment" 
                value="{{ $rejectedFinancial->count() }}" 
                icon="x-circle" 
                color="border-danger" 
                delay="200" 
            />
        </div>

        {{-- WORKSPACE PANEL --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Pending Dossiers Grid --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card rounded-2xl overflow-hidden shadow-md shadow-slate-100 bg-white">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <x-icon name="clock" class="h-5 w-5 text-amber-500" />
                            <span>Dossiers Académiques en Attente</span>
                        </h3>
                        <span class="bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200">
                            {{ $pendingFinancial->count() }} dossier(s)
                        </span>
                    </div>

                    <div class="bg-slate-50/30">
                        @if($pendingFinancial->isEmpty())
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mb-3 border border-green-100 shadow-sm">
                                    <x-icon name="check-circle" class="h-7 w-7 text-green-500" />
                                </div>
                                <p class="text-slate-800 font-bold text-sm">Tout est en ordre !</p>
                                <p class="text-slate-400 text-xs mt-0.5">Aucun dossier en attente de vérification administrative.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto custom-scrollbar">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="p-4">Étudiant</th>
                                            <th class="p-4">Filière / Département</th>
                                            <th class="p-4">Sujet de Recherche</th>
                                            <th class="p-4 text-right">Décisions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-650 bg-white font-medium">
                                        @foreach($pendingFinancial as $subject)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="p-4 whitespace-nowrap">
                                                    <div class="font-extrabold text-slate-800 text-sm leading-tight">{{ $subject->student->name }}</div>
                                                    <div class="text-slate-400 mt-0.5">Matricule : <strong>{{ $subject->student->matricule ?? '—' }}</strong></div>
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-slate-600">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                                                        {{ $subject->department->name }}
                                                    </span>
                                                </td>
                                                <td class="p-4 max-w-xs truncate text-slate-700 font-semibold" title="{{ $subject->title }}">
                                                    <div class="truncate text-xs">{{ $subject->title }}</div>
                                                </td>
                                                <td class="p-4 whitespace-nowrap text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button type="button"
                                                            @click="currentSubject = {{ $subject->id }}; currentStudentName = '{{ addslashes($subject->student->name) }}'; currentSubjectTitle = '{{ addslashes(Str::limit($subject->title, 60)) }}'; showValidateModal = true"
                                                            class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg shadow-sm hover-lift transition-colors flex items-center gap-1">
                                                            <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                                            <span>Valider</span>
                                                        </button>
                                                        <button type="button"
                                                            @click="currentSubject = {{ $subject->id }}; currentStudentName = '{{ addslashes($subject->student->name) }}'; currentSubjectTitle = '{{ addslashes(Str::limit($subject->title, 60)) }}'; showRejectModal = true"
                                                            class="bg-white hover:bg-red-50 text-red-600 border border-red-200 hover:border-red-300 text-xs font-semibold py-1.5 px-3 rounded-lg shadow-sm transition-colors flex items-center gap-1">
                                                            <x-icon name="x-circle" class="h-3.5 w-3.5" />
                                                            <span>Rejeter</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Recent History Column --}}
            <div class="space-y-6">
                
                {{-- Validated Recently --}}
                <div class="glass-card rounded-2xl overflow-hidden flex flex-col h-fit shadow-md shadow-slate-100 bg-white">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <x-icon name="check-badge" class="h-5 w-5 text-green-500" />
                            <span>Validés Récemment</span>
                        </h3>
                    </div>
                    
                    <div class="bg-slate-50/30">
                        @if($validatedFinancial->isEmpty())
                            <p class="text-xs text-slate-400 italic text-center py-6">Aucun dossier validé récemment.</p>
                        @else
                            <ul class="divide-y divide-slate-100 bg-white text-xs">
                                @foreach($validatedFinancial as $subjVal)
                                    <li class="p-4 hover:bg-slate-50/30 transition-colors flex justify-between items-center gap-4">
                                        <div class="min-w-0">
                                            <p class="font-extrabold text-slate-800 truncate leading-snug">{{ $subjVal->student->name }}</p>
                                            <div class="flex items-center gap-2 text-[10px] text-slate-400 font-semibold mt-0.5">
                                                <span>{{ $subjVal->department->name }}</span>
                                                <span>·</span>
                                                <span class="flex items-center gap-0.5"><x-icon name="clock" class="h-3 w-3" /> {{ $subjVal->financial_validated_at?->format('d/m/Y') ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <x-status-badge status="validated" class="py-0 px-2 text-[9px] font-black rounded-lg" />
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- Rejected Recently --}}
                <div class="glass-card rounded-2xl overflow-hidden flex flex-col h-fit shadow-md shadow-slate-100 bg-white">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <x-icon name="x-circle" class="h-5 w-5 text-red-500" />
                            <span>Rejetés Récemment</span>
                        </h3>
                    </div>
                    
                    <div class="bg-slate-50/30">
                        @if($rejectedFinancial->isEmpty())
                            <p class="text-xs text-slate-400 italic text-center py-6">Aucun dossier rejeté récemment.</p>
                        @else
                            <ul class="divide-y divide-slate-100 bg-white text-xs">
                                @foreach($rejectedFinancial as $subjRej)
                                    <li class="p-4 hover:bg-slate-50/30 transition-colors space-y-2">
                                        <div class="flex justify-between items-center gap-4">
                                            <div class="min-w-0">
                                                <p class="font-extrabold text-slate-800 truncate leading-snug">{{ $subjRej->student->name }}</p>
                                                <div class="flex items-center gap-2 text-[10px] text-slate-400 font-semibold mt-0.5">
                                                    <span>{{ $subjRej->department->name }}</span>
                                                    <span>·</span>
                                                    <span class="flex items-center gap-0.5"><x-icon name="clock" class="h-3 w-3" /> {{ $subjRej->financial_validated_at?->format('d/m/Y') ?? '—' }}</span>
                                                </div>
                                            </div>
                                            <x-status-badge status="rejected" class="py-0 px-2 text-[9px] font-black rounded-lg" />
                                        </div>
                                        @if($subjRej->financial_notes)
                                            <div class="text-[10px] p-2 rounded-lg bg-red-50/60 border border-red-100 text-red-700 leading-normal font-semibold">
                                                Motif : {{ $subjRej->financial_notes }}
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        {{-- VALIDATE MODAL --}}
        <div x-show="showValidateModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div @click="showValidateModal = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
                <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full">
                    
                    <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-green-900 flex items-center gap-2">
                            <x-icon name="check-circle" class="h-5 w-5 text-green-600" />
                            <span>Valider la Situation Administrative</span>
                        </h3>
                        <button @click="showValidateModal = false" class="text-green-500 hover:text-green-700"><x-icon name="x-mark" class="h-5 w-5"/></button>
                    </div>
                    
                    <div class="px-6 py-6 text-xs text-slate-650 space-y-4">
                        <p class="text-sm text-slate-700">
                            Confirmer que l'étudiant <strong class="text-slate-900" x-text="currentStudentName"></strong> est en ordre financièrement et administrativement ?
                        </p>
                        
                        <div class="p-3 bg-slate-50 border border-slate-150 rounded-xl leading-normal font-semibold text-slate-500" x-text="currentSubjectTitle"></div>

                        <form :action="'/appariteur/subjects/' + currentSubject + '/validate-financial'" method="POST" class="space-y-4 pt-2">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-500 uppercase tracking-wider">Notes et Référence Reçu (Facultatif)</label>
                                <textarea name="financial_notes" rows="3"
                                    class="w-full text-xs rounded-lg border-slate-200 shadow-inner focus:ring-green-500 focus:border-green-500"
                                    placeholder="Ex. Reçu de scolarité n°892312, date de paiement..."></textarea>
                            </div>
                            
                            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                                <button type="button" @click="showValidateModal = false" class="px-4 py-2 text-xs font-bold text-slate-650 border border-slate-200 rounded-xl">Annuler</button>
                                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-green-600 hover:bg-green-700 shadow-sm rounded-xl">Confirmer la Validation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- REJECT MODAL --}}
        <div x-show="showRejectModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div @click="showRejectModal = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
                <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full">
                    
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-red-900 flex items-center gap-2">
                            <x-icon name="x-circle" class="h-5 w-5 text-red-600" />
                            <span>Rejeter la Proposition pour Situation Financière</span>
                        </h3>
                        <button @click="showRejectModal = false" class="text-red-500 hover:text-red-700"><x-icon name="x-mark" class="h-5 w-5"/></button>
                    </div>
                    
                    <div class="px-6 py-6 text-xs text-slate-650 space-y-4">
                        <p class="text-sm text-slate-700">
                            Refuser la validation administrative pour l'étudiant <strong class="text-slate-900" x-text="currentStudentName"></strong> ?
                        </p>
                        
                        <div class="p-3 bg-slate-50 border border-slate-150 rounded-xl leading-normal font-semibold text-slate-500" x-text="currentSubjectTitle"></div>

                        <form :action="'/appariteur/subjects/' + currentSubject + '/reject-financial'" method="POST" class="space-y-4 pt-2">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="block font-bold text-slate-500 uppercase tracking-wider">Motif du refus académique <span class="text-red-500">*</span></label>
                                <textarea name="financial_notes" rows="3" required
                                    class="w-full text-xs rounded-lg border-slate-200 shadow-inner focus:ring-red-500 focus:border-red-500"
                                    placeholder="Expliquez rigoureusement les motifs d'insolvabilité ou de dossier invalide..."></textarea>
                            </div>
                            
                            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                                <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-xs font-bold text-slate-650 border border-slate-200 rounded-xl">Annuler</button>
                                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm rounded-xl">Confirmer le Refus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
