<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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
    
    <!-- Alpine.js - IMPORTANT -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .auth-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="h-full antialiased">
    <div class="min-h-full flex">
        <!-- Section gauche - Formulaire -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:max-w-md">
                <!-- Logo -->
                <div class="mb-8 text-center lg:text-left">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center space-x-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            E-Learn
                        </span>
                    </a>
                </div>
                
                @yield('content')
                
                <!-- Footer -->
                <div class="mt-8 text-center lg:text-left text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} E-Learn. Tous droits réservés.</p>
                </div>
            </div>
        </div>
        
        <!-- Section droite - Illustration -->
        <div class="hidden lg:block relative w-0 flex-1 auth-bg">
            <div class="absolute inset-0 flex flex-col justify-center px-12 text-white z-10">
                <div class="max-w-lg">
                    <h2 class="text-4xl font-bold mb-4">
                        @yield('auth_title', 'Apprenez sans limites')
                    </h2>
                    <p class="text-lg text-white/80 mb-8">
                        @yield('auth_description', 'Rejoignez notre communauté et développez vos compétences.')
                    </p>
                    
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-3xl font-bold">150+</div>
                            <div class="text-sm text-white/70">Cours</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold">10k+</div>
                            <div class="text-sm text-white/70">Étudiants</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold">50+</div>
                            <div class="text-sm text-white/70">Formateurs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>