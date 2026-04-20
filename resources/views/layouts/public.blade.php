<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'E-Learn') - {{ config('app.name', 'E-Learn') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 h-full" x-data="{ 
    mobileMenuOpen: false, 
    userMenuOpen: false, 
    notificationsOpen: false 
}">
    <div class="min-h-full flex flex-col">
        <!-- Navigation Publique -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo et navigation principale -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                                </div>
                                <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent hidden sm:block">
                                    E-Learn
                                </span>
                            </a>
                        </div>
                        
                        <!-- Navigation desktop -->
                        <div class="hidden md:flex md:ml-10 md:items-center md:space-x-1">
                            <a href="{{ route('welcome') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                <i class="fas fa-home mr-1"></i>Accueil
                            </a>
                            <a href="{{ route('courses.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('courses.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                <i class="fas fa-book-open mr-1"></i>Cours
                            </a>
                            <a href="{{ route('forum.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('forum.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                <i class="fas fa-comments mr-1"></i>Forum
                            </a>
                            
                            @auth
                                <a href="{{ route('chat.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors relative {{ request()->routeIs('chat.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <i class="fas fa-comment-dots mr-1"></i>Messages
                                    <span id="unread-messages-badge" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center hidden"></span>
                                </a>
                                <a href="{{ route('gamification.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('gamification.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <i class="fas fa-trophy mr-1"></i>Progression
                                </a>
                            @endauth
                        </div>
                    </div>
                    
                    <!-- Recherche (desktop) -->
                    <div class="hidden lg:flex lg:items-center lg:ml-6 lg:flex-1 lg:max-w-md">
                        <form action="{{ route('courses.search') }}" method="GET" class="relative w-full">
                            <input type="text" 
                                   name="q" 
                                   placeholder="Rechercher un cours..." 
                                   value="{{ request('q') }}"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 text-sm">
                            <button type="submit" class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Menu utilisateur -->
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        @auth
                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                                    <i class="far fa-bell text-lg"></i>
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
                                    <div class="p-4 border-b border-gray-200">
                                        <h3 class="font-semibold text-gray-900">Notifications</h3>
                                    </div>
                                    <div class="divide-y divide-gray-200">
                                        @forelse(auth()->user()->notifications()->latest()->limit(5)->get() as $notification)
                                            <div class="p-4 hover:bg-gray-50 cursor-pointer transition-colors">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-8 h-8 bg-{{ $notification->data['color'] ?? 'indigo' }}-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }} text-{{ $notification->data['color'] ?? 'indigo' }}-600 text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm text-gray-900">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-4 text-center text-gray-500">
                                                <i class="far fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                                <p class="text-sm">Aucune notification</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-100 transition-colors">
                                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" 
                                         class="w-8 h-8 rounded-full border border-gray-200">
                                    <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:inline"></i>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                                    <div class="p-3 border-b border-gray-200">
                                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-user w-5 mr-3 text-gray-400"></i>Tableau de bord
                                        </a>
                                        <a href="{{ route('student.my-courses') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-book-open w-5 mr-3 text-gray-400"></i>Mes cours
                                        </a>
                                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>Profil
                                        </a>
                                        
                                        @if(auth()->user()->hasRole('instructor'))
                                            <hr class="my-1">
                                            <a href="{{ route('instructor.courses.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-chalkboard-teacher w-5 mr-3 text-gray-400"></i>Espace formateur
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->hasRole('admin'))
                                            <hr class="my-1">
                                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-shield-alt w-5 mr-3 text-gray-400"></i>Administration
                                            </a>
                                        @endif
                                        
                                        <hr class="my-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                                <i class="fas fa-sign-out-alt w-5 mr-3"></i>Déconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                                Connexion
                            </a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition-all">
                                S'inscrire
                            </a>
                        @endauth
                        
                        <!-- Bouton menu mobile -->
                        <button @click="mobileMenuOpen = true" class="md:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Menu mobile -->
        <div x-show="mobileMenuOpen" 
             class="fixed inset-0 z-50 md:hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75" @click="mobileMenuOpen = false"></div>
            
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white h-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="transform -translate-x-full"
                 x-transition:enter-end="transform translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="transform translate-x-0"
                 x-transition:leave-end="transform -translate-x-full">
                
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="mobileMenuOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none">
                        <i class="fas fa-times text-white text-xl"></i>
                    </button>
                </div>
                
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-900">E-Learn</span>
                        </a>
                    </div>
                    
                    <nav class="mt-5 px-3 space-y-1">
                        <a href="{{ route('welcome') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                            <i class="fas fa-home w-5 mr-3"></i>Accueil
                        </a>
                        <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('courses.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                            <i class="fas fa-book-open w-5 mr-3"></i>Cours
                        </a>
                        <a href="{{ route('forum.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('forum.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                            <i class="fas fa-comments w-5 mr-3"></i>Forum
                        </a>
                        
                        @auth
                            <a href="{{ route('chat.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('chat.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-comment-dots w-5 mr-3"></i>Messages
                            </a>
                            <a href="{{ route('gamification.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('gamification.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-trophy w-5 mr-3"></i>Progression
                            </a>
                            
                            <hr class="my-3">
                            
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user w-5 mr-3"></i>Tableau de bord
                            </a>
                            <a href="{{ route('student.my-courses') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-book-open w-5 mr-3"></i>Mes cours
                            </a>
                            
                            @if(auth()->user()->hasRole('instructor'))
                                <a href="{{ route('instructor.courses.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-chalkboard-teacher w-5 mr-3"></i>Espace formateur
                                </a>
                            @endif
                            
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-shield-alt w-5 mr-3"></i>Administration
                                </a>
                            @endif
                            
                            <hr class="my-3">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-base font-medium text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-5 mr-3"></i>Déconnexion
                                </button>
                            </form>
                        @else
                            <hr class="my-3">
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-sign-in-alt w-5 mr-3"></i>Connexion
                            </a>
                            <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-indigo-600 hover:bg-indigo-50">
                                <i class="fas fa-user-plus w-5 mr-3"></i>S'inscrire
                            </a>
                        @endauth
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <main class="flex-1">
            <!-- Messages flash -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }" x-show="show" x-transition
                         class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm">
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
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }" x-show="show" x-transition
                         class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm">
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
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">E-Learn</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('welcome') }}" class="text-sm text-gray-500 hover:text-gray-900">Accueil</a></li>
                            <li><a href="{{ route('courses.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Cours</a></li>
                            <li><a href="{{ route('forum.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Forum</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Communauté</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('forum.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Discussions</a></li>
                            <li><a href="{{ route('gamification.leaderboard') }}" class="text-sm text-gray-500 hover:text-gray-900">Classement</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Support</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900">Aide</a></li>
                            <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Légal</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900">Confidentialité</a></li>
                            <li><a href="#" class="text-sm text-gray-500 hover:text-gray-900">Conditions</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <p class="text-sm text-center text-gray-400">&copy; {{ date('Y') }} E-Learn. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </div>
    
    @stack('scripts')
    
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier les messages non lus
            fetch('/api/unread-messages-count')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        const badge = document.getElementById('unread-messages-badge');
                        if (badge) {
                            badge.textContent = data.count > 9 ? '9+' : data.count;
                            badge.classList.remove('hidden');
                        }
                    }
                })
                .catch(error => console.error('Erreur:', error));
            
            // Écouter les nouveaux messages via Echo si disponible
            if (typeof window.Echo !== 'undefined') {
                window.Echo.private(`user.{{ auth()->id() }}`)
                    .listen('.message.received', () => {
                        fetch('/api/unread-messages-count')
                            .then(response => response.json())
                            .then(data => {
                                const badge = document.getElementById('unread-messages-badge');
                                if (badge) {
                                    if (data.count > 0) {
                                        badge.textContent = data.count > 9 ? '9+' : data.count;
                                        badge.classList.remove('hidden');
                                    } else {
                                        badge.classList.add('hidden');
                                    }
                                }
                            });
                    });
            }
        });
    </script>
    @endauth
</body>
</html>