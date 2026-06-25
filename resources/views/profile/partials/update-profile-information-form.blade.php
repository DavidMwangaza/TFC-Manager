<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900">
            {{ __('Informations du profil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __('Ces informations sont gérées par l\'administrateur et ne peuvent pas être modifiées.') }}
        </p>
    </header>

    <div class="mt-6 space-y-4">
        <div>
            <x-input-label :value="__('Nom complet')" />
            <p class="mt-1 text-slate-900 font-medium">{{ $user->name }}</p>
        </div>

        <div>
            <x-input-label :value="__('Adresse e-mail')" />
            <p class="mt-1 text-slate-900 font-medium">{{ $user->email }}</p>
        </div>

        @if($user->matricule)
        <div>
            <x-input-label :value="__('Matricule')" />
            <p class="mt-1 text-slate-900 font-medium">{{ $user->matricule }}</p>
        </div>
        @endif

        <div>
            <x-input-label :value="__('Rôle(s)')" />
            <div class="mt-1 flex flex-wrap gap-2">
                        @foreach($user->getRoleNames() as $role)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
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

        @if($user->department)
        <div>
            <x-input-label :value="__('Filière')" />
            <p class="mt-1 text-slate-900 font-medium">{{ $user->department->name }}</p>
        </div>
        @endif
    </div>
</section>
