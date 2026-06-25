<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 text-primary rounded-xl">
                <x-icon name="user" class="w-6 h-6" />
            </div>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Mon Profil') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-breadcrumb :items="[['label' => 'Mon Profil']]" />

            {{-- Carte de profil unifiée --}}
            <div class="glass-card bg-white/80 backdrop-blur-md shadow-sm border border-slate-200/60 rounded-2xl overflow-hidden">
                {{-- En-tête avec gradient --}}
                <div class="bg-gradient-to-br from-slate-900 via-primary-dark to-slate-950 p-6 sm:p-8 text-white relative">
                    <!-- Glowing accent background -->
                    <div class="absolute -right-16 -top-16 w-64 h-64 bg-accent/5 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="flex items-center space-x-6 relative">
                        <div class="flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover border-2 border-white/20 shadow-inner">
                            @else
                                <div class="h-20 w-20 rounded-full bg-white/20 border border-white/20 shadow-inner flex items-center justify-center">
                                    <span class="text-3xl font-black text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-white">
                            <h3 class="text-2xl font-extrabold tracking-tight">{{ $user->name }}</h3>
                            <p class="text-slate-300 text-sm font-semibold">{{ $user->email }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider shadow-sm border border-white/10
                                        @if($role === 'Admin') bg-red-500/20 text-red-200
                                        @elseif($role === 'Chef de département') bg-amber-500/20 text-amber-200
                                        @elseif($role === 'Enseignant') bg-blue-500/20 text-blue-200
                                        @else bg-green-500/20 text-green-200
                                        @endif">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Détails du profil --}}
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-semibold text-slate-900">{{ __('Informations personnelles') }}</h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ __('Lecture seule') }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mb-6">{{ __('Ces informations sont gérées par l\'administrateur. Contactez-le pour toute modification.') }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/50 p-6 rounded-xl border border-slate-100">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Nom complet') }}</p>
                            <p class="text-sm font-extrabold text-slate-800">{{ $user->name }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Adresse e-mail') }}</p>
                            <p class="text-sm font-extrabold text-slate-800">{{ $user->email }}</p>
                        </div>

                        @if($user->matricule)
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Matricule') }}</p>
                            <p class="text-sm font-extrabold text-slate-800">{{ $user->matricule }}</p>
                        </div>
                        @endif

                        @if($user->department)
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Filière') }}</p>
                            <p class="text-sm font-extrabold text-slate-800">{{ $user->department->name }}</p>
                        </div>
                        @endif

                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Rôle(s)') }}</p>
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                        @if($role === 'Admin') bg-red-100 text-red-800
                                        @elseif($role === 'Chef de département') bg-amber-100 text-amber-800
                                        @elseif($role === 'Enseignant') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Membre depuis') }}</p>
                            <p class="text-sm font-extrabold text-slate-800">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>

                    {{-- Formulaire Avatar & Bio --}}
                    <div class="mt-8 pt-6 border-t border-slate-200/60">
                        <h4 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Personnalisation du profil') }}</h4>
                        
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Photo de profil (Avatar)') }}</label>
                                <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('avatar') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('Biographie professionnelle') }}</label>
                                <textarea name="biographie" rows="4" class="w-full text-sm rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring-primary text-slate-700" placeholder="Décrivez votre parcours, vos thématiques de recherche...">{{ old('biographie', $user->biographie) }}</textarea>
                                @error('biographie') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition shadow-md shadow-primary/20">
                                    {{ __('Enregistrer les modifications') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
