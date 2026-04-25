<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Administration') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind via CDN (garantit que ça fonctionne) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @stack('styles')
    <style>
        /* Styles critiques pour la sidebar - garantis de fonctionner */
        .admin-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        .admin-sidebar {
            width: 256px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            display: flex;
            flex-direction: column;
            z-index: 40;
            overflow-y: auto;
        }
        
        .admin-main {
            flex: 1;
            margin-left: 256px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            color: #d1d5db;
            border-radius: 8px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }
        
        .sidebar-link:hover {
            background-color: #374151;
            color: white;
        }
        
        .sidebar-link.active {
            background-color: #4f46e5;
            color: white;
        }
        
        .sidebar-link i {
            width: 20px;
            margin-right: 12px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .admin-sidebar {
                display: none;
            }
            
            .admin-main {
                margin-left: 0;
            }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.3s ease-out;
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .table-row-hover {
            transition: background-color 0.2s ease;
        }
        
        .table-row-hover:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="h-full overflow-hidden" x-data="{ sidebarOpen: false, userMenuOpen: false, notificationsOpen: false }">
    
    <div class="admin-layout">
        <!-- Sidebar Desktop -->
        <aside class="admin-sidebar">
            <!-- Logo -->
            <div style="display: flex; align-items: center; justify-content: center; height: 64px; padding: 0 16px; background-color: #111827; flex-shrink: 0;">
                <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <div style="width: 32px; height: 32px; background-color: #4f46e5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-graduation-cap" style="color: white; font-size: 18px;"></i>
                    </div>
                    <span style="color: white; font-weight: bold; font-size: 20px;">E-Learn Admin</span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav style="flex: 1; padding: 16px 12px; overflow-y: auto;">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    Tableau de bord
                </a>
                
                <div style="padding-top: 16px;">
                    <p style="padding: 0 12px; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Gestion</p>
                </div>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Utilisateurs
                </a>
                
                <a href="{{ route('admin.courses.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i>
                    Cours
                </a>
                
                <a href="{{ route('admin.categories.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    Catégories
                </a>
                
                <a href="{{ route('admin.quizzes.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
                    <i class="fas fa-puzzle-piece"></i>
                    Quiz
                </a>
                
                <div style="padding-top: 16px;">
                    <p style="padding: 0 12px; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Analytique</p>
                </div>
                
                <a href="{{ route('admin.reports.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    Rapports
                </a>
                
                <a href="{{ route('admin.analytics.index') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Analytique avancée
                </a>
                <div class="pt-4">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Communauté</p>
                </div>

                <x-admin.sidebar-link :href="route('admin.forum.categories.index')" :active="request()->routeIs('admin.forum.*')" icon="comments">
                    Forum
                </x-admin.sidebar-link>

                <x-admin.sidebar-link :href="route('admin.gamification.index')" :active="request()->routeIs('admin.gamification.*')" icon="trophy">
                    Gamification
                </x-admin.sidebar-link>

                <x-admin.sidebar-link :href="route('chat.index')" :active="request()->routeIs('admin.chat.*')" icon="comment-dots">
                    Chat
                </x-admin.sidebar-link>

                <div style="padding-top: 16px;">
                    <p style="padding: 0 12px; font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Système</p>
                </div>
                
                <a href="{{ route('admin.settings') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    Paramètres
                </a>
                
                <a href="{{ route('admin.logs') }}" 
                   class="sidebar-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    Logs système
                </a>
            </nav>
            
            <!-- Profil -->
            <div style="padding: 16px; border-top: 1px solid #374151; flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                         style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #4f46e5;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 14px; font-weight: 500; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</p>
                        <p style="font-size: 12px; color: #9ca3af;">Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header style="background: white; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 30;">
                <div style="padding: 0 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; height: 64px;">
                        <!-- Gauche -->
                        <div style="display: flex; align-items: center;">
                            <button @click="sidebarOpen = true" 
                                    style="display: none; padding: 8px; border-radius: 6px; color: #6b7280; background: none; border: none; cursor: pointer;"
                                    class="mobile-menu-btn">
                                <i class="fas fa-bars" style="font-size: 20px;"></i>
                            </button>
                            
                            <h1 style="font-size: 18px; font-weight: 600; color: #111827;" class="page-title-mobile">
                                @yield('title', 'Administration')
                            </h1>
                        </div>
                        
                        <!-- Search -->
                        <div style="flex: 1; max-width: 512px; margin: 0 16px;" class="search-desktop">
                            <form action="{{ route('admin.search') }}" method="GET" style="position: relative;">
                                <input type="text" 
                                       name="q" 
                                       placeholder="Rechercher..." 
                                       style="width: 100%; padding: 8px 16px 8px 40px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb;">
                                <button type="submit" style="position: absolute; left: 12px; top: 8px; color: #9ca3af; background: none; border: none;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Right -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <!-- Notifications -->
                            <div style="position: relative;" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        style="position: relative; padding: 8px; color: #6b7280; background: none; border: none; border-radius: 50%; cursor: pointer;">
                                    <i class="far fa-bell" style="font-size: 20px;"></i>
                                    <span style="position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></span>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     style="position: absolute; right: 0; top: 40px; width: 320px; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; z-index: 50; max-height: 384px; overflow: auto; display: none;"
                                     x-transition>
                                    <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                                        <h3 style="font-weight: 600; color: #111827;">Notifications</h3>
                                    </div>
                                    <div>
                                        <div style="padding: 16px; cursor: pointer;">
                                            <div style="display: flex;">
                                                <div style="width: 32px; height: 32px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                                    <i class="fas fa-user-plus" style="color: #059669; font-size: 14px;"></i>
                                                </div>
                                                <div>
                                                    <p style="font-size: 14px; color: #111827;">Nouvel utilisateur inscrit</p>
                                                    <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Il y a 5 minutes</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Menu -->
                            <div style="position: relative;" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        style="display: flex; align-items: center; gap: 8px; padding: 4px; background: none; border: none; border-radius: 50%; cursor: pointer;">
                                    <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                                         style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #e5e7eb;">
                                    <span style="font-size: 14px; font-weight: 500; color: #374151;" class="user-name-desktop">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down" style="font-size: 12px; color: #9ca3af;"></i>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     style="position: absolute; right: 0; top: 40px; width: 224px; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; z-index: 50; display: none;"
                                     x-transition>
                                    <div style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                        <p style="font-size: 14px; font-weight: 500; color: #111827;">{{ Auth::user()->name }}</p>
                                        <p style="font-size: 12px; color: #6b7280;">{{ Auth::user()->email }}</p>
                                    </div>
                                    <div style="padding: 4px 0;">
                                        <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;">
                                            <i class="fas fa-user" style="width: 20px; margin-right: 12px; color: #9ca3af;"></i>
                                            Mon profil
                                        </a>
                                        <a href="{{ route('admin.settings') }}" style="display: flex; align-items: center; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;">
                                            <i class="fas fa-cog" style="width: 20px; margin-right: 12px; color: #9ca3af;"></i>
                                            Paramètres
                                        </a>
                                        <hr style="margin: 4px 0; border: none; border-top: 1px solid #e5e7eb;">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" style="width: 100%; text-align: left; padding: 8px 16px; font-size: 14px; color: #dc2626; background: none; border: none; display: flex; align-items: center; cursor: pointer;">
                                                <i class="fas fa-sign-out-alt" style="width: 20px; margin-right: 12px;"></i>
                                                Déconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Breadcrumb -->
            @hasSection('breadcrumb')
                <div style="background: white; border-bottom: 1px solid #e5e7eb; padding: 12px 24px;">
                    @yield('breadcrumb')
                </div>
            @endif
            
            <!-- Content -->
            <main style="flex: 1; background: #f9fafb;">
                @if(session('success'))
                    <div style="max-width: 1280px; margin: 0 auto; padding: 16px 24px 0;">
                        <div x-data="{ show: true }" x-show="show" x-transition
                             style="background: #ecfdf5; border-left: 4px solid #34d399; padding: 16px; border-radius: 0 8px 8px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <div style="display: flex;">
                                <div style="flex-shrink: 0;">
                                    <i class="fas fa-check-circle" style="color: #34d399;"></i>
                                </div>
                                <div style="margin-left: 12px; flex: 1;">
                                    <p style="font-size: 14px; color: #065f46;">{{ session('success') }}</p>
                                </div>
                                <button @click="show = false" style="margin-left: auto; color: #34d399; background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div style="max-width: 1280px; margin: 0 auto; padding: 16px 24px 0;">
                        <div x-data="{ show: true }" x-show="show" x-transition
                             style="background: #fef2f2; border-left: 4px solid #f87171; padding: 16px; border-radius: 0 8px 8px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <div style="display: flex;">
                                <div style="flex-shrink: 0;">
                                    <i class="fas fa-exclamation-circle" style="color: #f87171;"></i>
                                </div>
                                <div style="margin-left: 12px; flex: 1;">
                                    <p style="font-size: 14px; color: #991b1b;">{{ session('error') }}</p>
                                </div>
                                <button @click="show = false" style="margin-left: auto; color: #f87171; background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" 
         style="position: fixed; inset: 0; z-index: 50; display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div style="position: fixed; inset: 0; background: rgba(17, 24, 39, 0.75);" @click="sidebarOpen = false"></div>
        
        <div style="position: relative; width: 256px; height: 100%; background: #111827;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform -translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform -translate-x-full">
            
            <div style="position: absolute; top: 0; right: -48px; padding-top: 8px;">
                <button @click="sidebarOpen = false" 
                        style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: none; border: none; color: white; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div style="height: 100%; overflow-y: auto; padding: 20px 0 16px;">
                <div style="display: flex; align-items: center; padding: 0 16px;">
                    <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                        <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-graduation-cap" style="color: white; font-size: 18px;"></i>
                        </div>
                        <span style="color: white; font-weight: bold; font-size: 20px;">E-Learn</span>
                    </a>
                </div>
                
                <nav style="margin-top: 20px; padding: 0 12px;">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        Tableau de bord
                    </a>
                    <a href="{{ route('admin.users.index') }}" 
                       class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Utilisateurs
                    </a>
                    <a href="{{ route('admin.courses.index') }}" 
                       class="sidebar-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                        <i class="fas fa-book-open"></i>
                        Cours
                    </a>
                    <a href="{{ route('admin.settings') }}" 
                       class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        Paramètres
                    </a>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Styles responsifs supplémentaires -->
    <style>
        /* Cacher le nom d'utilisateur sur mobile */
        @media (max-width: 640px) {
            .user-name-desktop {
                display: none;
            }
            
            .search-desktop {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block !important;
            }
        }
        
        /* Afficher le titre sur mobile */
        @media (min-width: 1024px) {
            .page-title-mobile {
                display: none;
            }
        }
        
        /* S'assurer que x-show fonctionne */
        [x-show] {
            display: none;
        }
        
        [x-show].block {
            display: block !important;
        }
    </style>
    
    @stack('scripts')
</body>
</html>