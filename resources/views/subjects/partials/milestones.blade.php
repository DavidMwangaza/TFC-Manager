<div class="bg-white shadow-sm sm:rounded-lg p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><x-icon name="calendar" class="w-5 h-5 text-indigo-500" /> Suivi par jalons</h2>

    @hasanyrole('Enseignant|Chef de département')
        <form action="{{ route('milestones.store', $subject) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            @csrf
            <input name="title" required placeholder="Titre du jalon (ex: Introduction)" class="border rounded px-3 py-2" />
            <input name="due_date" required type="datetime-local" class="border rounded px-3 py-2" />
            <input name="correction_deadline" type="datetime-local" class="border rounded px-3 py-2" />
            <div class="md:col-span-3 text-right">
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Créer le jalon</button>
            </div>
        </form>
    @endhasanyrole

    <div class="flex items-center justify-between mb-3">
        <div>
            <form method="GET" class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Trier :</label>
                <select name="sort" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                    <option value="due_asc" {{ request('sort') === 'due_asc' ? 'selected' : '' }}>Échéance ↑</option>
                    <option value="due_desc" {{ request('sort') === 'due_desc' ? 'selected' : '' }}>Échéance ↓</option>
                    <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Statut</option>
                </select>
            </form>
        </div>
        <div class="text-sm text-gray-500">Total : {{ $milestones->total() }}</div>
    </div>

    @if($milestones->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Soumis</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Correction (SLA)</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($milestones as $milestone)
                        <tr>
                            <td class="px-4 py-2">{{ $milestone->title }}</td>
                            <td class="px-4 py-2">{{ $milestone->due_date?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $milestone->submission_date?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $milestone->correction_deadline?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-2">
                                @if($milestone->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">En attente</span>
                                @elseif($milestone->status === 'submitted')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">Soumis</span>
                                @elseif($milestone->status === 'validated')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Validé</span>
                                @elseif($milestone->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">À refaire</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(Auth::user()->hasRole('Etudiant') && $subject->student_id === Auth::id())
                                        @if(in_array($milestone->status, ['pending', 'rejected']))
                                            <button type="button" @click="$dispatch('open-modal', 'milestone-upload-{{ $milestone->id }}')" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Soumettre</button>
                                            <x-modal name="milestone-upload-{{ $milestone->id }}" focusable>
                                                <form method="POST" action="{{ route('milestones.upload', $milestone) }}" enctype="multipart/form-data" class="p-6">
                                                    @csrf
                                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Soumettre un fichier pour : {{ $milestone->title }}</h3>
                                                    <div class="mb-4">
                                                        <label class="block text-sm text-gray-700 mb-1">Fichier PDF</label>
                                                        <input type="file" name="pdf" accept="application/pdf" required class="block w-full text-sm" />
                                                    </div>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="$dispatch('close-modal', 'milestone-upload-{{ $milestone->id }}')" class="px-4 py-2 bg-white border rounded">Annuler</button>
                                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Soumettre</button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @endif
                                    @endif

                                    @if(Auth::user()->hasRole('Enseignant') && $subject->teacher_id === Auth::id())
                                        @if($milestone->status === 'submitted')
                                            <button type="button" @click="$dispatch('open-modal', 'milestone-validate-{{ $milestone->id }}')" class="px-3 py-1 bg-green-600 text-white rounded text-sm">Valider</button>
                                            <x-modal name="milestone-validate-{{ $milestone->id }}" focusable>
                                                <form method="POST" action="{{ route('milestones.validate', $milestone) }}" class="p-6">
                                                    @csrf
                                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Valider : {{ $milestone->title }}</h3>
                                                    <div class="mb-4">
                                                        <label class="block text-sm text-gray-700 mb-1">Commentaire (optionnel)</label>
                                                        <input name="comments" class="w-full border rounded px-2 py-1" />
                                                    </div>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="$dispatch('close-modal', 'milestone-validate-{{ $milestone->id }}')" class="px-4 py-2 bg-white border rounded">Annuler</button>
                                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Valider</button>
                                                    </div>
                                                </form>
                                            </x-modal>

                                            <button type="button" @click="$dispatch('open-modal', 'milestone-reject-{{ $milestone->id }}')" class="px-3 py-1 bg-red-600 text-white rounded text-sm">Rejeter</button>
                                            <x-modal name="milestone-reject-{{ $milestone->id }}" focusable>
                                                <form method="POST" action="{{ route('milestones.reject', $milestone) }}" class="p-6">
                                                    @csrf
                                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rejeter : {{ $milestone->title }}</h3>
                                                    <div class="mb-4">
                                                        <label class="block text-sm text-gray-700 mb-1">Motif (requis)</label>
                                                        <input name="comments" required class="w-full border rounded px-2 py-1" />
                                                    </div>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="$dispatch('close-modal', 'milestone-reject-{{ $milestone->id }}')" class="px-4 py-2 bg-white border rounded">Annuler</button>
                                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Rejeter</button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @endif
                                    @endif

                                    @if($milestone->thesisFile)
                                        <a href="{{ route('thesis.download', $milestone->thesisFile) }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm">Télécharger</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if($milestone->comments)
                            <tr class="bg-gray-50">
                                <td colspan="6" class="px-4 py-2 text-sm text-gray-600">Commentaire : {{ $milestone->comments }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $milestones->links() }}</div>
    @else
        <p class="text-sm text-gray-500">Aucun jalon défini pour ce sujet.</p>
    @endif
</div>
