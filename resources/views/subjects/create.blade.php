<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 text-primary rounded-xl">
                <x-icon name="document-text" class="w-6 h-6" />
            </div>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Fiche de Proposition de Sujet</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[['label' => 'Sujets', 'url' => route('subjects.index')], ['label' => 'Nouveau sujet']]" />

            {{-- Messages flash --}}
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Wizard multi-étapes --}}
            <div x-data="{
                step: 1,
                totalSteps: 5,
                submitting: false,
                errors: {},
                specificObjectives: {{ json_encode(old('specific_objectives', [''])) }},
                stateOfArt: {{ json_encode(old('state_of_art', [['author' => '', 'institution' => '', 'contribution' => '']])) }},

                addObjective() {
                    this.specificObjectives.push('');
                },
                removeObjective(index) {
                    if (this.specificObjectives.length > 1) this.specificObjectives.splice(index, 1);
                },
                addReference() {
                    this.stateOfArt.push({ author: '', institution: '', contribution: '' });
                },
                removeReference(index) {
                    if (this.stateOfArt.length > 1) this.stateOfArt.splice(index, 1);
                },
                validateStep() {
                    this.errors = {};
                    if (this.step === 2) {
                        const title = document.getElementById('title')?.value?.trim();
                        if (!title) this.errors.title = 'Le titre est obligatoire.';
                    }
                    if (this.step === 3) {
                        const ctx = document.getElementById('context_relevance')?.value?.trim();
                        const ch = document.getElementById('challenges')?.value?.trim();
                        const rq = document.getElementById('research_question')?.value?.trim();
                        if (!ctx || ctx.length < 30) this.errors.context_relevance = 'Minimum 30 caractères requis.';
                        if (!ch || ch.length < 30) this.errors.challenges = 'Minimum 30 caractères requis.';
                        if (!rq) this.errors.research_question = 'La question de recherche est obligatoire.';
                    }
                    if (this.step === 4) {
                        const hyp = document.getElementById('hypothesis')?.value?.trim();
                        const go = document.getElementById('general_objective')?.value?.trim();
                        if (!hyp || hyp.length < 20) this.errors.hypothesis = 'Minimum 20 caractères requis.';
                        if (!go || go.length < 20) this.errors.general_objective = 'Minimum 20 caractères requis.';
                        const filled = this.specificObjectives.filter(o => o.trim() !== '');
                        if (filled.length === 0) this.errors.specific_objectives = 'Au moins un objectif spécifique est requis.';
                    }
                    return Object.keys(this.errors).length === 0;
                },
                nextStep() {
                    if (this.step === 1 || this.validateStep()) {
                        if (this.step < this.totalSteps) this.step++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                prevStep() { if (this.step > 1) this.step--; window.scrollTo({ top: 0, behavior: 'smooth' }); }
            }" class="space-y-6">

                {{-- Indicateur de progression --}}
                <div class="bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-4">
                        <template x-for="s in totalSteps" :key="s">
                            <div class="flex items-center" :class="s < totalSteps ? 'flex-1' : ''">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl shadow-sm text-sm font-bold transition-all duration-300"
                                    :class="step >= s ? 'bg-primary text-white shadow-primary/20' : 'bg-slate-100 border border-slate-200/60 text-slate-400'">
                                    <span x-text="s"></span>
                                </div>
                                <div x-show="s < totalSteps" class="flex-1 h-1 mx-2 rounded transition-all duration-300"
                                    :class="step > s ? 'bg-primary' : 'bg-slate-100'"></div>
                            </div>
                        </template>
                    </div>
                    <div class="text-center text-sm text-slate-600">
                        <span x-show="step === 1">Étape 1 — Informations Générales</span>
                        <span x-show="step === 2">Étape 2 — Titre du Projet</span>
                        <span x-show="step === 3">Étape 3 — Construction du Problème</span>
                        <span x-show="step === 4">Étape 4 — Solution et Objectifs</span>
                        <span x-show="step === 5">Étape 5 — Cadre Scientifique et Méthodologique</span>
                    </div>
                </div>

                {{-- Formulaire --}}
                <form method="POST" action="{{ route('subjects.store') }}" id="subjectForm" @submit="submitting = true">
                    @csrf

                    {{-- 
                         ÉTAPE 1 : INFORMATIONS GÉNÉRALES (lecture seule)
                          --}}
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl p-6 lg:p-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Informations Générales</h3>
                        <p class="text-sm text-slate-500 mb-6">Ces informations sont extraites de votre profil connecté.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-500">Nom et Prénom</label>
                                <div class="mt-1 p-3 bg-slate-50 border border-slate-200 rounded-md text-slate-800 font-medium">
                                    {{ Auth::user()->name }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-500">Matricule</label>
                                <div class="mt-1 p-3 bg-slate-50 border border-slate-200 rounded-md text-slate-800 font-medium">
                                    {{ Auth::user()->matricule ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-500">Filière</label>
                                <div class="mt-1 p-3 bg-slate-50 border border-slate-200 rounded-md text-slate-800 font-medium">
                                    {{ Auth::user()->department->name ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-500">Adresse e-mail</label>
                                <div class="mt-1 p-3 bg-slate-50 border border-slate-200 rounded-md text-slate-800 font-medium">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-700"><strong>Note :</strong> Vos informations sont incorrectes ? Contactez l'administration pour mettre à jour votre profil.</p>
                        </div>
                    </div>

                    {{-- 
                         ÉTAPE 2 : TITRE DU PROJET
                          --}}
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl p-6 lg:p-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Titre du Projet</h3>
                        <p class="text-sm text-slate-500 mb-6">Choisissez un titre clair et descriptif pour votre travail.</p>

                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-slate-700">Titre du sujet <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" required maxlength="255"
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Ex: Conception et réalisation d'une application web de gestion..."
                                    value="{{ old('title') }}">
                                @error('title') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                <p x-show="errors.title" x-text="errors.title" class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit"></p>
                                <p class="mt-1 text-xs text-slate-500">Maximum 255 caractères — Soyez précis et descriptif.</p>
                            </div>

                            <div>
                                <label for="subject_type" class="block text-sm font-medium text-slate-700">Type de sujet</label>
                                <select name="subject_type" id="subject_type"
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="">— Sélectionnez —</option>
                                    <option value="tfc" {{ old('subject_type') === 'tfc' ? 'selected' : '' }}>TFC (Travail de Fin de Cycle)</option>
                                    <option value="memoire" {{ old('subject_type') === 'memoire' ? 'selected' : '' }}>Mémoire</option>
                                </select>
                                @error('subject_type') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 
                         ÉTAPE 3 : CONSTRUCTION DU PROBLÈME
                          --}}
                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl p-6 lg:p-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Construction du Problème</h3>
                        <p class="text-sm text-slate-500 mb-6">Structurez votre problématique en 3 parties distinctes.</p>

                        <div class="space-y-6">
                            {{-- A: Contexte et Pertinence --}}
                            <div>
                                <label for="context_relevance" class="block text-sm font-medium text-slate-700">
                                    A. Contexte et Pertinence <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500 mb-1">Expliquez pourquoi ce sujet est important maintenant. Quel est le contexte ?</p>
                                <textarea name="context_relevance" id="context_relevance" rows="4" required
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Dans le contexte actuel de... il est pertinent de s'intéresser à...">{{ old('context_relevance') }}</textarea>
                                @error('context_relevance') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                <p x-show="errors.context_relevance" x-text="errors.context_relevance" class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit"></p>
                            </div>

                            {{-- B: Défis et Lacunes --}}
                            <div>
                                <label for="challenges" class="block text-sm font-medium text-slate-700">
                                    B. Défis et Lacunes Actuelles <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500 mb-1">Qu'est-ce qui ne fonctionne pas actuellement ? Quels sont les problèmes identifiés ?</p>
                                <textarea name="challenges" id="challenges" rows="4" required
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Actuellement, les problèmes suivants sont observés : ...">{{ old('challenges') }}</textarea>
                                @error('challenges') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                <p x-show="errors.challenges" x-text="errors.challenges" class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit"></p>
                            </div>

                            {{-- C: Question de Recherche --}}
                            <div>
                                <label for="research_question" class="block text-sm font-medium text-slate-700">
                                    C. Question de Recherche <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500 mb-1">Posez votre question de recherche clairement.</p>
                                <input type="text" name="research_question" id="research_question" required
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Ex: Comment concevoir un système permettant de... ?"
                                    value="{{ old('research_question') }}">
                                @error('research_question') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                <p x-show="errors.research_question" x-text="errors.research_question" class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit"></p>
                            </div>
                        </div>
                    </div>

                    {{-- 
                         ÉTAPE 4 : SOLUTION ET OBJECTIFS
                          --}}
                    <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl p-6 lg:p-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Solution et Objectifs</h3>
                        <p class="text-sm text-slate-500 mb-6">Décrivez votre solution envisagée et définissez vos objectifs.</p>

                        <div class="space-y-6">
                            {{-- Hypothèse --}}
                            <div>
                                <label for="hypothesis" class="block text-sm font-medium text-slate-700">
                                    Hypothèse <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500 mb-1">Quelle est votre solution provisoire au problème posé ?</p>
                                <textarea name="hypothesis" id="hypothesis" rows="3" required
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Nous supposons que la mise en place de... permettrait de...">{{ old('hypothesis') }}</textarea>
                                @error('hypothesis') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                <p x-show="errors.hypothesis" x-text="errors.hypothesis" class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit"></p>
                            </div>

                            {{-- Objectif Général --}}
                            <div>
                                <label for="general_objective" class="block text-sm font-medium text-slate-700">
                                    Objectif Général <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500 mb-1">Le but final de votre projet en une phrase.</p>
                                <textarea name="general_objective" id="general_objective" rows="2" required
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="L'objectif principal de ce travail est de...">{{ old('general_objective') }}</textarea>
                                @error('general_objective') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                <p x-show="errors.general_objective" x-text="errors.general_objective" class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit"></p>
                            </div>

                            {{-- Objectifs Spécifiques (liste dynamique) --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    Objectifs Spécifiques <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500 mb-2">Listez vos objectifs spécifiques. Cliquez sur « + » pour en ajouter.</p>

                                <div class="space-y-2">
                                    <template x-for="(obj, index) in specificObjectives" :key="index">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-slate-400 w-6 text-right" x-text="(index+1) + '.'"></span>
                                            <input type="text" :name="'specific_objectives[' + index + ']'"
                                                x-model="specificObjectives[index]"
                                                class="flex-1 rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                                placeholder="Objectif spécifique...">
                                            <button type="button" @click="removeObjective(index)" x-show="specificObjectives.length > 1"
                                                class="text-red-400 hover:text-red-600 p-1" title="Supprimer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="addObjective()"
                                    class="mt-2 inline-flex items-center px-4 py-2 text-sm font-bold text-primary bg-primary/10 rounded-xl hover:bg-primary/20 transition">
                                    + Ajouter un objectif
                                </button>
                                @error('specific_objectives') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                @error('specific_objectives.*') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                                <p x-show="errors.specific_objectives" x-text="errors.specific_objectives" class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit"></p>
                            </div>
                        </div>
                    </div>

                    {{-- 
                         ÉTAPE 5 : CADRE SCIENTIFIQUE ET MÉTHODOLOGIQUE
                          --}}
                    <div x-show="step === 5" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl p-6 lg:p-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Cadre Scientifique et Méthodologique</h3>
                        <p class="text-sm text-slate-500 mb-6">Présentez votre état de l'art, vos démarcations et vos méthodes.</p>

                        <div class="space-y-6">
                            {{-- État de l'art (tableau dynamique) --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    État de l'art — Travaux antérieurs
                                </label>
                                <p class="text-xs text-slate-500 mb-2">Remplissez le tableau avec les travaux existants liés à votre sujet.</p>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-slate-50">
                                                <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase">#</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase">Auteur</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase">Institution</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase">Apport / Sujet</th>
                                                <th class="px-3 py-2"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(ref, index) in stateOfArt" :key="index">
                                                <tr class="border-b">
                                                    <td class="px-3 py-2 text-slate-400" x-text="index + 1"></td>
                                                    <td class="px-3 py-2">
                                                        <input type="text" :name="'state_of_art[' + index + '][author]'"
                                                            x-model="stateOfArt[index].author"
                                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                                            placeholder="Nom de l'auteur">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="text" :name="'state_of_art[' + index + '][institution]'"
                                                            x-model="stateOfArt[index].institution"
                                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                                            placeholder="Institution">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="text" :name="'state_of_art[' + index + '][contribution]'"
                                                            x-model="stateOfArt[index].contribution"
                                                                class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                                            placeholder="Apport principal">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button" @click="removeReference(index)" x-show="stateOfArt.length > 1"
                                                            class="text-red-400 hover:text-red-600 p-1" title="Supprimer">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" @click="addReference()"
                                    class="mt-2 inline-flex items-center px-4 py-2 text-sm font-bold text-primary bg-primary/10 rounded-xl hover:bg-primary/20 transition">
                                    + Ajouter une référence
                                </button>
                            </div>

                            {{-- Démarcations --}}
                            <div>
                                <label for="demarcations" class="block text-sm font-medium text-slate-700">
                                    Démarcations
                                </label>
                                <p class="text-xs text-slate-500 mb-1">En quoi votre travail se distingue-t-il des travaux existants ?</p>
                                <textarea name="demarcations" id="demarcations" rows="3"
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Contrairement aux travaux cités, notre approche se distingue par...">{{ old('demarcations') }}</textarea>
                                @error('demarcations') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                            </div>

                            {{-- Méthodologies --}}
                            <div>
                                <label for="methodologies" class="block text-sm font-medium text-slate-700">
                                    Méthodologies
                                </label>
                                <p class="text-xs text-slate-500 mb-1">Décrivez vos méthodes de collecte et d'analyse (ex: Interview, UML, UP, Merise...).</p>
                                <textarea name="methodologies" id="methodologies" rows="3"
                                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Pour la collecte : technique documentaire, interview...&#10;Pour l'analyse : UML, méthode UP...">{{ old('methodologies') }}</textarea>
                                @error('methodologies') <p class="text-red-500 text-[11px] font-semibold mt-1.5 bg-red-50/50 px-2.5 py-1 rounded-lg border border-red-100/50 w-fit">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 
                         BOUTONS DE NAVIGATION
                          --}}
                    <div class="bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            {{-- Retour --}}
                            <div>
                                <button type="button" x-show="step > 1" @click="prevStep()"
                                    class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200/60 rounded-xl font-semibold text-sm text-slate-700 transition">
                                    ← Précédent
                                </button>
                                <a x-show="step === 1" href="{{ route('subjects.index') }}" class="text-sm text-slate-600 hover:text-slate-900 hover:underline">
                                    ← Retour à la liste
                                </a>
                            </div>

                            {{-- Suivant / Soumettre --}}
                            <div>
                                <button type="button" x-show="step < totalSteps" @click="nextStep()"
                                    class="inline-flex items-center px-6 py-2.5 bg-primary border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:bg-primary-light transition-all">
                                    Suivant →
                                </button>
                                <button type="submit" x-show="step === totalSteps" x-cloak :disabled="submitting"
                                    class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg shadow-blue-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!submitting" class="flex items-center gap-2">
                                        <span>Soumettre la proposition</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </span>
                                    <span x-show="submitting" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Envoi en cours...
                                    </span>
                                </button>
                            </div>
                        </div>

                        {{-- Avertissement final --}}
                        <div x-show="step === totalSteps" x-cloak class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <p class="text-xs text-amber-700">
                                En soumettant cette fiche, votre proposition de sujet sera envoyée au Chef de Filière pour examen. <strong>Vous ne pourrez plus la modifier</strong> après soumission, sauf en cas de rejet.
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
