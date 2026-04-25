<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Espace Formateur') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @stack('styles')
    <style>
        /* Layout */
        .instructor-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background: #f9fafb;
        }
        
        /* Sidebar */
        .instructor-sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            display: flex;
            flex-direction: column;
            z-index: 40;
            overflow-y: auto;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Main Content */
        .instructor-main {
            flex: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-y: auto;
        }
        
        /* Sidebar Link */
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #c7d2fe;
            border-radius: 10px;
            transition: all 0.2s;
            margin-bottom: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }
        
        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar-link.active {
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }
        
        .sidebar-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
        }
        
        /* Section Title */
        .sidebar-section-title {
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #818cf8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Stats Card */
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .instructor-sidebar {
                display: none;
            }
            
            .instructor-main {
                margin-left: 0;
            }
        }
        
        /* Cacher sur mobile */
        @media (max-width: 640px) {
            .user-name-desktop {
                display: none;
            }
            
            .search-desktop {
                display: none;
            }
        }
        
        /* x-cloak */
        [x-cloak] { 
            display: none !important; 
        }
    </style>
</head>
<body class="h-full overflow-hidden" x-data="{ sidebarOpen: false }">
    
    <div class="instructor-layout">
        <!-- Sidebar Desktop -->
        <aside class="instructor-sidebar">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 bg-[#1e1b4b] flex-shrink-0 border-b border-indigo-800/30">
                <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 text-decoration-none">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-chalkboard-teacher text-white text-lg"></i>
                    </div>
                    <span class="text-white font-bold text-xl">Espace Formateur</span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('instructor.dashboard') }}" 
                   class="sidebar-link {{ request()->routeIs('instructor.dashboard.index') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    Tableau de bord
                </a>
                
                <!-- Cours -->
                <div class="sidebar-section-title mt-6">
                    <i class="fas fa-book-open mr-1"></i> Mes Cours
                </div>
                
                <a href="{{ route('instructor.courses.index') }}" 
                   class="sidebar-link {{ request()->routeIs('instructor.courses.index') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    Tous mes cours
                </a>
                
                <a href="{{ route('instructor.courses.create') }}" 
                   class="sidebar-link {{ request()->routeIs('instructor.courses.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    Créer un cours
                </a>
                
                <!-- Quiz -->
                <div class="sidebar-section-title mt-6">
                    <i class="fas fa-puzzle-piece mr-1"></i> Quiz
                </div>
                
                <a href="{{ route('instructor.quizzes.index') ?? '#' }}" 
                   class="sidebar-link opacity-75">
                    <i class="fas fa-puzzle-piece"></i>
                    Mes quiz
                </a>
                
                <!-- Analyses -->
                <div class="sidebar-section-title mt-6">
                    <i class="fas fa-chart-bar mr-1"></i> Analyses
                </div>
                
                <a href="{{ route('instructor.analytics') }}" 
                   class="sidebar-link {{ request()->routeIs('instructor.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Vue d'ensemble
                </a>
                
                <a href="{{ route('instructor.earnings') }}" 
                   class="sidebar-link {{ request()->routeIs('instructor.earnings') ? 'active' : '' }}">
                    <i class="fas fa-euro-sign"></i>
                    Revenus
                </a>
                
                <!-- Communauté -->
                <div class="sidebar-section-title mt-6">
                    <i class="fas fa-users mr-1"></i> Communauté
                </div>
                
                <a href="{{ route('instructor.courses.reviews', ['course' => 'all']) ?? '#' }}" 
                   class="sidebar-link opacity-75">
                    <i class="fas fa-star"></i>
                    Avis & Commentaires
                </a>
                
                <a href="{{ route('chat.index') }}" 
                   class="sidebar-link">
                    <i class="fas fa-comment-dots"></i>
                    Messages
                    <span class="ml-auto w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                </a>
                
                <!-- Paramètres -->
                <div class="sidebar-section-title mt-6">
                    <i class="fas fa-cog mr-1"></i> Paramètres
                </div>
                
                <a href="{{ route('instructor.profile.edit') }}" 
                   class="sidebar-link">
                    <i class="fas fa-user-circle"></i>
                    Mon profil
                </a>
                
                <a href="{{ route('instructor.profile.settings') ?? '#' }}" 
                   class="sidebar-link opacity-75">
                    <i class="fas fa-sliders-h"></i>
                    Préférences
                </a>
                
                <!-- Retour à l'accueil -->
                <div class="sidebar-section-title mt-6">
                    <i class="fas fa-globe mr-1"></i> Navigation
                </div>
                
                <a href="{{ route('dashboard') }}" 
                   class="sidebar-link">
                    <i class="fas fa-user-graduate"></i>
                    Espace étudiant
                </a>
                
                <a href="{{ route('welcome') }}" 
                   class="sidebar-link">
                    <i class="fas fa-external-link-alt"></i>
                    Voir le site
                </a>
            </nav>
            
            <!-- Profil -->
            <div class="p-4 border-t border-indigo-800/30 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                         class="w-10 h-10 rounded-full border-2 border-indigo-500 object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-indigo-300">Formateur</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white transition-colors" title="Déconnexion">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="instructor-main">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Gauche -->
                        <div class="flex items-center gap-4">
                            <button @click="sidebarOpen = true" 
                                    class="hidden max-lg:block p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            
                            <h1 class="text-lg font-semibold text-gray-900 truncate">
                                @yield('page-title', 'Tableau de bord')
                            </h1>
                        </div>
                        
                        <!-- Centre - Recherche -->
                        <div class="flex-1 max-w-lg mx-4 search-desktop">
                            <form class="relative">
                                <input type="text" 
                                       placeholder="Rechercher..." 
                                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            </form>
                        </div>
                        
                        <!-- Droite -->
                        <div class="flex items-center gap-3">
                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                                    <i class="far fa-bell text-lg"></i>
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     x-cloak
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
                                    <div class="p-4 border-b border-gray-200">
                                        <h3 class="font-semibold text-gray-900">Notifications</h3>
                                    </div>
                                    <div class="divide-y divide-gray-100">
                                        <div class="p-4 hover:bg-gray-50 cursor-pointer">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user-plus text-green-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-800">Nouvel étudiant inscrit à votre cours</p>
                                                    <p class="text-xs text-gray-500 mt-1">Il y a 2 heures</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-4 hover:bg-gray-50 cursor-pointer">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-star text-blue-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-800">Nouvel avis 5 étoiles reçu !</p>
                                                    <p class="text-xs text-gray-500 mt-1">Hier</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 border-t border-gray-200 bg-gray-50">
                                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                            Voir toutes les notifications
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Menu Utilisateur -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 transition-colors">
                                    <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                                         class="w-8 h-8 rounded-full border border-gray-200 object-cover">
                                    <span class="text-sm font-medium text-gray-700 user-name-desktop">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 user-name-desktop"></i>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     x-cloak
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                                    <div class="p-3 border-b border-gray-200">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="{{ route('instructor.profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i>
                                            Mon profil
                                        </a>
                                        <a href="{{ route('instructor.profile.settings') ?? '#' }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                                            Paramètres
                                        </a>
                                        <hr class="my-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
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
                <div class="bg-white border-b border-gray-200 px-6 lg:px-8 py-3">
                    @yield('breadcrumb')
                </div>
            @endif
            
            <!-- Content -->
            <main class="flex-1 p-6 lg:p-8">
                {{-- Messages Flash --}}
                @if(session('success'))
                    <div class="mb-6" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                                <button @click="show = false" class="ml-auto text-green-500 hover:text-green-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6" x-data="{ show: true }" x-show="show" x-transition>
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                                <button @click="show = false" class="ml-auto text-red-500 hover:text-red-600">
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
         class="fixed inset-0 z-50 lg:hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75" @click="sidebarOpen = false"></div>
        
        <div class="relative w-72 h-full bg-gradient-to-b from-[#1e1b4b] to-[#312e81]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform -translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform -translate-x-full">
            
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="sidebarOpen = false" 
                        class="flex items-center justify-center w-10 h-10 rounded-full text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="h-full overflow-y-auto py-6">
                <div class="flex items-center justify-center px-4 mb-6">
                    <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-white"></i>
                        </div>
                        <span class="text-white font-bold text-xl">Formateur</span>
                    </a>
                </div>
                
                <nav class="px-4 space-y-1">
                    <a href="{{ route('instructor.dashboard') }}" class="sidebar-link">Tableau de bord</a>
                    <a href="{{ route('instructor.courses.index') }}" class="sidebar-link">Mes cours</a>
                    <a href="{{ route('instructor.courses.create') }}" class="sidebar-link">Créer un cours</a>
                    <a href="{{ route('instructor.analytics') }}" class="sidebar-link">Analyses</a>
                    <a href="{{ route('instructor.profile.edit') }}" class="sidebar-link">Mon profil</a>
                </nav>
            </div>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>