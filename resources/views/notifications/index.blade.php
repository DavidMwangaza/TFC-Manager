<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-flex items-center gap-2">
                <x-icon name="bell" class="w-6 h-6" /> Notifications
            </h2>
            @php
                $unreadCount = Auth::user()->unreadNotifications->count();
                $totalNotifications = Auth::user()->notifications()->count();
            @endphp
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-200 transition">
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif
                @if($totalNotifications > 0)
                    <form method="POST" action="{{ route('notifications.destroyAll') }}" onsubmit="return confirm('Supprimer toutes vos notifications ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-300 rounded-md text-sm text-red-700 hover:bg-red-100 transition">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($notifications->count() === 0)
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune notification</h3>
                            <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de notifications.</p>
                        </div>
                    @else
                        <div class="space-y-1">
                            @foreach($notifications as $notification)
                                <div class="flex items-start gap-4 p-4 rounded-lg transition {{ $notification->read_at ? 'bg-white' : 'bg-blue-50 border-l-4 border-blue-500' }}">
                                    {{-- Ic&ocirc;ne --}}
                                    <div class="text-2xl flex-shrink-0 mt-0.5">
                                        <x-icon name="bell" class="w-6 h-6 text-blue-500" />
                                    </div>

                                    {{-- Contenu --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                            @unless($notification->read_at)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Nouveau
                                                </span>
                                            @endunless
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    <div class="flex-shrink-0 flex items-center gap-3">
                                        @unless($notification->read_at)
                                            <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 hover:underline" title="Marquer comme lu">
                                                    Marquer comme lu
                                                </button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" onsubmit="return confirm('Supprimer cette notification ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 hover:underline" title="Supprimer la notification">
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
