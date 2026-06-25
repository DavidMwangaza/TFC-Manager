<div x-data="{ isCollapsed: localStorage.getItem('sidebar-collapsed') === 'true', mobileOpen: false }" class="relative flex h-screen">
    
    <!-- Mobile Toggle Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-primary text-white flex items-center justify-between px-4 z-40 shadow-md">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-8 w-8 object-contain bg-white rounded-full p-0.5">
            <div>
                <span class="text-sm font-bold block">TFC Manager</span>
                <span class="text-[10px] text-blue-200">UDBL</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Notifications cloche mobile -->
            <a href="{{ route('notifications.index') }}" class="relative p-2 text-blue-100 hover:text-white transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-accent"></span>
                    </span>
                @endif
            </a>
            
            <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-lg text-blue-100 hover:text-white focus:outline-none hover:bg-primary-light transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Overlay under mobile sidebar -->
    <div x-show="mobileOpen" x-cloak @click="mobileOpen = false" class="lg:hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 transition-opacity"></div>

    <!-- Sidebar Wrapper -->
    <aside 
        :class="{ 
            'w-64': !isCollapsed, 
            'w-20': isCollapsed,
            'translate-x-0': mobileOpen,
            '-translate-x-full lg:translate-x-0': !mobileOpen
        }"
        class="fixed lg:sticky top-0 left-0 bottom-0 z-40 h-screen bg-gradient-to-b from-slate-900 via-primary-dark to-slate-950 text-slate-300 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800 shrink-0 custom-scrollbar overflow-y-auto"
    >
        <!-- Sidebar Brand / Logo -->
        <div class="h-20 flex items-center justify-between px-5 border-b border-slate-800 relative mt-16 lg:mt-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-10 w-10 object-contain bg-white rounded-full p-0.5 shrink-0 shadow-inner">
                <div x-show="!isCollapsed" class="transition-opacity duration-300 whitespace-nowrap">
                    <span class="text-base font-bold text-white tracking-wide block">TFC Manager</span>
                    <span class="text-[10px] text-slate-400 font-medium tracking-wider uppercase">Université Don Bosco de Lubumbashi</span>
                </div>
            </a>
            
            <!-- Collapse Button (Desktop Only) -->
            <button @click="isCollapsed = !isCollapsed; localStorage.setItem('sidebar-collapsed', isCollapsed)" class="hidden lg:flex absolute -right-3 top-7 bg-slate-900 border border-slate-700 hover:border-slate-500 text-slate-400 hover:text-white rounded-full p-1 shadow-md transition-colors z-50">
                <svg :class="{'rotate-180': isCollapsed}" class="h-3 w-3 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 py-6 px-3 space-y-1.5">
            <!-- GENERAL SECTION -->
            <div x-show="!isCollapsed" class="px-3 mb-2 text-[10px] font-bold text-slate-500 tracking-wider uppercase">Menu principal</div>
            
            <!-- Link: Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
               title="Tableau de bord"
            >
                <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                    <x-icon name="home" class="h-4 w-4" />
                </span>
                <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Tableau de bord</span>
            </a>

            <!-- Link: Subjects -->
            <a href="{{ route('subjects.index') }}" 
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('subjects.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
               title="Sujets de TFC"
            >
                <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                    <x-icon name="document-text" class="h-4 w-4" />
                </span>
                <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Sujets de TFC</span>
            </a>

            <!-- Link: Archives Publiques -->
            <a href="{{ route('archives.index') }}" 
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('archives.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
               title="Archives publiques"
            >
                <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                    <x-icon name="folder-open" class="h-4 w-4" />
                </span>
                <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Archives publiques</span>
            </a>

            <!-- Link: Notifications -->
            <a href="{{ route('notifications.index') }}" 
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('notifications.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
               title="Notifications"
            >
                <span class="relative shrink-0 text-slate-400 group-hover:text-slate-200">
                    <x-icon name="bell" class="h-4 w-4" />
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-accent"></span>
                        </span>
                    @endif
                </span>
                <div x-show="!isCollapsed" class="flex-1 flex items-center justify-between whitespace-nowrap">
                    <span class="text-sm">Notifications</span>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="bg-accent text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </div>
            </a>

            <!-- ADMIN SECTION -->
            @if(Auth::user()->hasRole('Admin'))
                <div class="pt-6 border-t border-slate-800 mt-6">
                    <div x-show="!isCollapsed" class="px-3 mb-2 text-[10px] font-bold text-slate-500 tracking-wider uppercase">Administration</div>
                    
                    <!-- Users CRUD -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
                       title="Utilisateurs"
                    >
                        <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                            <x-icon name="users" class="h-4 w-4" />
                        </span>
                        <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Utilisateurs</span>
                    </a>

                    <!-- Departments CRUD -->
                    <a href="{{ route('admin.departments.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.departments.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
                       title="Filières"
                    >
                        <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                            <x-icon name="academic-cap" class="h-4 w-4" />
                        </span>
                        <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Filières</span>
                    </a>

                    <!-- Academic Years CRUD -->
                    <a href="{{ route('admin.academic-years.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.academic-years.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
                       title="Années académiques"
                    >
                        <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                            <x-icon name="calendar" class="h-4 w-4" />
                        </span>
                        <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Années académiques</span>
                    </a>

                    <!-- Settings System -->
                    <a href="{{ route('admin.settings.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
                       title="Paramètres"
                    >
                        <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                            <x-icon name="cog-6-tooth" class="h-4 w-4" />
                        </span>
                        <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Paramètres</span>
                    </a>

                    <!-- Activity Log -->
                    <a href="{{ route('admin.logs.index') }}" 
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.logs.*') ? 'bg-white/10 text-white border-l-4 border-accent font-medium shadow-sm' : 'hover:bg-white/5 hover:text-slate-100 hover:shadow-sm hover-lift' }}"
                       title="Journal d'activité"
                    >
                        <span class="shrink-0 text-slate-400 group-hover:text-slate-200">
                            <x-icon name="clipboard-document-list" class="h-4 w-4" />
                        </span>
                        <span x-show="!isCollapsed" class="text-sm whitespace-nowrap">Journal</span>
                    </a>
                </div>
            @endif
        </nav>

        <!-- Sidebar Footer / Profile Info -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center gap-3 overflow-hidden">
                <!-- Avatar / Initials Circle -->
                <div class="h-10 w-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold shrink-0 shadow-inner border border-slate-850">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                
                <!-- Info Section -->
                <div x-show="!isCollapsed" class="flex-1 min-w-0 transition-opacity duration-300">
                    <span class="text-sm font-semibold text-white block truncate">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-slate-400 block truncate leading-tight">
                        {{ Auth::user()->roles->pluck('name')->first() ?? 'Utilisateur' }}
                        @if(Auth::user()->department)
                             · {{ Auth::user()->department->code ?? Auth::user()->department->name }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Profile & Logout Buttons -->
            <div x-show="!isCollapsed" class="mt-4 flex gap-2">
                <a href="{{ route('profile.edit') }}" class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium py-1.5 px-3 rounded-lg text-center transition-colors">
                    Profil
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-slate-800 hover:bg-red-900/50 text-slate-300 hover:text-red-200 text-xs font-medium py-1.5 px-3 rounded-lg transition-colors">
                        Décon.
                    </button>
                </form>
            </div>
            
            <!-- Collapsed simple power off button -->
            <div x-show="isCollapsed" class="mt-4 flex justify-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 text-slate-500 hover:text-red-400 rounded-lg hover:bg-slate-850 transition" title="Déconnexion">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
