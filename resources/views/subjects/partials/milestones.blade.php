<div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="{}">
    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><x-icon name="calendar" class="w-5 h-5 text-blue-500" /> Suivi par jalons</h2>

    @if(Auth::user()->hasRole('Chef de département') || (Auth::user()->hasRole('Enseignant') && $subject->teacher_id === Auth::id()))
        <div class="mb-6 bg-slate-50 border rounded-lg p-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">Nouveau jalon</h3>
            <form action="{{ route('milestones.store', $subject) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Titre du jalon</label>
                    <input name="title" required value="{{ old('title') }}" placeholder="ex: Introduction" class="w-full border rounded px-3 py-2 text-sm @error('title') border-red-500 @enderror" />
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Date limite (Étudiant)</label>
                    <input name="due_date" required type="datetime-local" value="{{ old('due_date') }}" class="w-full border rounded px-3 py-2 text-sm @error('due_date') border-red-500 @enderror" />
                    @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Délai de correction (SLA Prof)</label>
                    <input name="correction_deadline" type="datetime-local" value="{{ old('correction_deadline') }}" class="w-full border rounded px-3 py-2 text-sm @error('correction_deadline') border-red-500 @enderror" />
                    @error('correction_deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 shadow-sm transition">
                        <x-icon name="plus" class="w-4 h-4" /> Créer le jalon
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="flex items-center justify-between mb-3">
        <div>
            <form method="GET" class="flex items-center gap-2">
                <label class="text-sm text-slate-600">Trier :</label>
                <select name="sort" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                    <option value="due_asc" {{ request('sort') === 'due_asc' ? 'selected' : '' }}>Échéance ↑</option>
                    <option value="due_desc" {{ request('sort') === 'due_desc' ? 'selected' : '' }}>Échéance ↓</option>
                    <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Statut</option>
                </select>
            </form>
        </div>
        <div class="text-sm text-slate-500">Total : {{ $milestones->total() }}</div>
    </div>

    @if($milestones->count() > 0)
        <div class="space-y-4">
            @foreach($milestones as $index => $milestone)
                @php
                    $isOverdue = $milestone->due_date && $milestone->status === 'pending' && $milestone->due_date->isPast();
                    $bgClass = 'bg-white';
                    $borderClass = 'border-slate-200';
                    
                    if ($milestone->status === 'validated') {
                        $bgClass = 'bg-green-50/30';
                        $borderClass = 'border-green-200';
                    } elseif ($milestone->status === 'rejected') {
                        $bgClass = 'bg-red-50/30';
                        $borderClass = 'border-red-200';
                    } elseif ($milestone->status === 'submitted') {
                        $bgClass = 'bg-blue-50/30';
                        $borderClass = 'border-blue-200';
                    } elseif ($isOverdue) {
                        $bgClass = 'bg-orange-50/30';
                        $borderClass = 'border-orange-300';
                    }
                @endphp
                
                <div class="relative border {{ $borderClass }} {{ $bgClass }} rounded-xl shadow-sm overflow-hidden transition hover:shadow-md">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 sm:p-5">
                        
                        {{-- Numéro / Icône --}}
                        <div class="shrink-0 flex items-center justify-center w-12 h-12 rounded-full border-2 
                            {{ $milestone->status === 'validated' ? 'border-green-500 bg-green-100 text-green-600' : '' }}
                            {{ $milestone->status === 'submitted' ? 'border-blue-500 bg-blue-100 text-blue-600' : '' }}
                            {{ $milestone->status === 'rejected' ? 'border-red-500 bg-red-100 text-red-600' : '' }}
                            {{ $milestone->status === 'pending' && !$isOverdue ? 'border-slate-300 bg-slate-50 text-slate-500' : '' }}
                            {{ $isOverdue ? 'border-orange-500 bg-orange-100 text-orange-600' : '' }}">
                            @if($milestone->status === 'validated')
                                <x-icon name="check-circle" class="w-6 h-6" />
                            @elseif($milestone->status === 'submitted')
                                <x-icon name="arrow-path" class="w-6 h-6 animate-spin-slow" />
                            @else
                                <span class="text-lg font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>

                        {{-- Contenu principal --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center flex-wrap gap-2 mb-1">
                                <h4 class="text-base font-bold text-slate-900">{{ $milestone->title }}</h4>
                                @if($milestone->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">En attente</span>
                                    @if($isOverdue)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 animate-pulse">
                                            <x-icon name="exclamation-triangle" class="w-3.5 h-3.5" /> En retard
                                        </span>
                                    @endif
                                @elseif($milestone->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">En correction</span>
                                @elseif($milestone->status === 'validated')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Validé</span>
                                @elseif($milestone->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">À refaire</span>
                                @endif
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-2 text-sm text-slate-600">
                                <div class="flex items-center gap-1.5" title="Date limite pour l'étudiant">
                                    <x-icon name="calendar" class="w-4 h-4 text-slate-400" />
                                    <span>Échéance: <strong class="{{ $isOverdue ? 'text-orange-600' : 'text-slate-900' }}">{{ $milestone->due_date?->translatedFormat('d M Y, H:i') ?? 'Non définie' }}</strong></span>
                                </div>
                                
                                @if($milestone->submission_date)
                                    <div class="flex items-center gap-1.5" title="Date de dépôt du travail">
                                        <x-icon name="document-arrow-up" class="w-4 h-4 text-slate-400" />
                                        <span>Soumis le: <strong class="text-slate-900">{{ $milestone->submission_date->translatedFormat('d M Y, H:i') }}</strong></span>
                                    </div>
                                @endif
                                
                                @if($milestone->correction_deadline)
                                    <div class="flex items-center gap-1.5" title="Délai maximum pour la correction du professeur">
                                        <x-icon name="clock" class="w-4 h-4 text-slate-400" />
                                        <span>SLA Prof: <strong class="text-slate-900">{{ $milestone->correction_deadline->translatedFormat('d M Y, H:i') }}</strong></span>
                                    </div>
                                @endif

                                {{-- Score IA visible seulement par les encadrants et admin --}}
                                @php
                                    $aiReport = $milestone->thesisFile ? $milestone->thesisFile->aiReport : null;
                                @endphp
                                @if($aiReport && Auth::user()->hasRole('Enseignant') && $subject->teacher_id === Auth::id())
                                    <div class="flex items-center gap-1.5" title="Analyse de contenu IA">
                                        <x-icon name="cpu-chip" class="w-4 h-4 text-slate-400" />
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ 
                                            $aiReport->ai_score < 25 ? 'bg-green-100 text-green-700' : 
                                            ($aiReport->ai_score < 60 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') 
                                        }}">
                                            IA: {{ $aiReport->ai_score }}%
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions (Boutons) --}}
                        <div class="shrink-0 flex items-center flex-wrap gap-2 w-full sm:w-auto mt-4 sm:mt-0 pt-4 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            @if(Auth::user()->hasRole('Etudiant') && $subject->student_id === Auth::id())
                                @if(in_array($milestone->status, ['pending', 'rejected']))
                                    <button type="button" @click="$dispatch('open-modal', 'milestone-upload-{{ $milestone->id }}')" 
                                            class="flex-1 sm:flex-none inline-flex justify-center items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                                        <x-icon name="arrow-up-tray" class="w-4 h-4" /> Soumettre
                                    </button>
                                    <x-modal name="milestone-upload-{{ $milestone->id }}" focusable>
                                        <div class="p-6">
                                            <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                                                <x-icon name="document-arrow-up" class="w-6 h-6 text-blue-600"/>
                                                Dépôt du travail &mdash; {{ $milestone->title }}
                                            </h3>
                                            <p class="text-sm text-slate-500 mb-4">Veuillez uploader votre document PDF.</p>

                                            {{-- Onglet PDF --}}
                                            <form method="POST" action="{{ route('milestones.upload', $milestone) }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-5 bg-slate-50 p-4 rounded-lg border border-slate-200">
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Fichier PDF (max 20 Mo)</label>
                                                    <input type="file" name="pdf" accept="application/pdf" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="$dispatch('close-modal', 'milestone-upload-{{ $milestone->id }}')" class="px-4 py-2 bg-white text-slate-700 font-medium border border-slate-300 rounded-lg hover:bg-slate-50 transition">Annuler</button>
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">Confirmer le dépôt</button>
                                                </div>
                                            </form>
                                        </div>
                                    </x-modal>
                                @endif
                            @endif

                            @if(Auth::user()->hasRole('Enseignant') && $subject->teacher_id === Auth::id())
                                @if($milestone->status === 'submitted')
                                    <button type="button" @click="$dispatch('open-modal', 'milestone-validate-{{ $milestone->id }}')" 
                                            class="flex-1 sm:flex-none inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                                        <x-icon name="check-circle" class="w-4 h-4" /> Valider
                                    </button>
                                    <x-modal name="milestone-validate-{{ $milestone->id }}" focusable>
                                        <form method="POST" action="{{ route('milestones.validate', $milestone) }}" class="p-6">
                                            @csrf
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><x-icon name="check-circle" class="w-6 h-6 text-green-600"/> Validation du jalon</h3>
                                            <p class="text-sm text-slate-600 mb-4">Vous vous apprêtez à valider le jalon : <strong>{{ $milestone->title }}</strong></p>
                                            <div class="mb-5">
                                                <label class="block text-sm font-medium text-slate-700 mb-1">Feedback pour l'étudiant (optionnel)</label>
                                                <textarea name="comments" rows="3" placeholder="Bravo pour cette partie..." class="w-full border-slate-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                                            </div>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="$dispatch('close-modal', 'milestone-validate-{{ $milestone->id }}')" class="px-4 py-2 bg-white text-slate-700 font-medium border border-slate-300 rounded-lg hover:bg-slate-50 transition">Annuler</button>
                                                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                                                    Valider définitivement
                                                </button>
                                            </div>
                                        </form>
                                    </x-modal>

                                    <button type="button" @click="$dispatch('open-modal', 'milestone-reject-{{ $milestone->id }}')" 
                                            class="flex-1 sm:flex-none inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                                        <x-icon name="x-circle" class="w-4 h-4" /> Rejeter
                                    </button>
                                    <x-modal name="milestone-reject-{{ $milestone->id }}" focusable>
                                        <form method="POST" action="{{ route('milestones.reject', $milestone) }}" class="p-6">
                                            @csrf
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600"/> Demande de corrections</h3>
                                            <p class="text-sm text-slate-600 mb-4">Vous refusez le travail actuel pour le jalon : <strong>{{ $milestone->title }}</strong></p>
                                            <div class="mb-5 bg-red-50 p-4 rounded-lg border border-red-100">
                                                <label class="block text-sm font-medium text-red-800 mb-1">Motif du rejet (obligatoire)</label>
                                                <textarea name="comments" required rows="3" placeholder="Veuillez revoir la section X, il manque Y..." class="w-full border-red-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-red-300"></textarea>
                                                <p class="text-xs text-red-600 mt-1">L'étudiant recevra ce message pour corriger son travail.</p>
                                            </div>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="$dispatch('close-modal', 'milestone-reject-{{ $milestone->id }}')" class="px-4 py-2 bg-white text-slate-700 font-medium border border-slate-300 rounded-lg hover:bg-slate-50 transition">Annuler</button>
                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">Confirmer le rejet</button>
                                            </div>
                                        </form>
                                    </x-modal>
                                @endif
                            @endif

                            @if($milestone->thesisFile)
                                <a href="{{ route('thesis.download', $milestone->thesisFile) }}" 
                                   class="flex-1 sm:flex-none inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg border border-slate-200 transition" title="Télécharger le document">
                                    <x-icon name="document-text" class="w-4 h-4 text-slate-500" /> Lire le PDF
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Section Feedback / Commentaires --}}
                    @if($milestone->comments)
                        <div class="px-5 py-3 border-t {{ $milestone->status === 'rejected' ? 'bg-red-50 border-red-100' : 'bg-slate-50 border-slate-100' }}">
                            <div class="flex gap-3">
                                <div class="shrink-0 mt-0.5">
                                    <x-icon name="information-circle" class="w-5 h-5 {{ $milestone->status === 'rejected' ? 'text-red-400' : 'text-slate-400' }}" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider {{ $milestone->status === 'rejected' ? 'text-red-800' : 'text-slate-500' }} mb-1">
                                        Feedback du Directeur
                                    </p>
                                    <p class="text-sm {{ $milestone->status === 'rejected' ? 'text-red-700 font-medium' : 'text-slate-700' }}">
                                        {{ $milestone->comments }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $milestones->links() }}</div>
    @else
        <div class="flex flex-col items-center justify-center py-10 px-4 text-center bg-slate-50 rounded-xl border border-dashed border-slate-300">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 mb-4">
                <x-icon name="bookmark" class="w-8 h-8 text-slate-300" />
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-1">Aucun jalon défini</h3>
            <p class="text-sm text-slate-500 max-w-sm">Le suivi de ce TFC n'a pas encore été découpé en étapes. Les jalons apparaîtront ici une fois créés.</p>
        </div>
    @endif
</div>
