<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-primary/10 text-primary rounded-xl">
                    <x-icon name="bell" class="w-6 h-6" />
                </div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Notifications</h2>
            </div>
            @php
                $unreadCount = Auth::user()->unreadNotifications->count();
                $totalNotifications = Auth::user()->notifications()->count();
            @endphp
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200/60 rounded-xl text-sm font-semibold text-slate-700 shadow-sm transition-all">
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif
                @if($totalNotifications > 0)
                    <form method="POST" action="{{ route('notifications.destroyAll') }}" onsubmit="return confirm('Supprimer toutes vos notifications ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-200/60 rounded-xl text-sm font-semibold text-red-700 shadow-sm transition-all">
                            Tout supprimer
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Messages flash --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-sm border border-slate-200/60 rounded-2xl">
                <div class="p-6 lg:p-8">
                    @if($notifications->count() === 0)
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-900">Aucune notification</h3>
                            <p class="mt-1 text-sm text-slate-500">Vous n'avez pas encore de notifications.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($notifications as $notification)
                                <div class="flex items-start gap-4 p-5 rounded-xl border {{ $notification->read_at ? 'bg-white border-slate-100/60' : 'bg-primary/5 border-primary/20 border-l-4 border-l-primary' }} shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                                    {{-- Ic&ocirc;ne --}}
                                    <div class="p-2.5 rounded-xl flex-shrink-0 {{ $notification->read_at ? 'bg-slate-100 text-slate-400' : 'bg-primary/10 text-primary' }}">
                                        <x-icon name="bell" class="w-5 h-5" />
                                    </div>

                                    {{-- Contenu --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-extrabold text-slate-800">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                            @unless($notification->read_at)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-primary/10 text-primary border border-primary/20">
                                                    Nouveau
                                                </span>
                                            @endunless
                                        </div>
                                        <p class="mt-1 text-sm text-slate-600">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    <div class="flex-shrink-0 flex items-center gap-3">
                                        @unless($notification->read_at)
                                            <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="text-xs font-bold text-primary hover:text-primary-light hover:underline" title="Marquer comme lu">
                                                    Marquer comme lu
                                                </button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" onsubmit="return confirm('Supprimer cette notification ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 hover:underline" title="Supprimer la notification">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
