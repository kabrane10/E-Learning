@extends('layouts.guest')

@section('title', 'Connexion')
@section('auth_title', 'Content de vous revoir !')
@section('auth_description', 'Connectez-vous pour accéder à vos cours et continuer votre apprentissage.')

@section('content')
<div x-data="{ 
    showPassword: false,
    remember: true 
}" class="animate-fade-in">
    
    <!-- Titre -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Connexion</h1>
        <p class="text-gray-500 mt-1">
            Pas encore de compte ? 
            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                S'inscrire
            </a>
        </p>
    </div>
    
    <!-- Messages de session -->
    @if(session('status'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm text-red-700 font-medium mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>Erreur de connexion
            </p>
            <ul class="text-xs text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Formulaire -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Adresse email
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('email') border-red-300 @enderror"
                       placeholder="votre@email.com">
            </div>
        </div>
        
        <!-- Mot de passe -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Mot de passe
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                    Mot de passe oublié ?
                </a>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input :type="showPassword ? 'text' : 'password'" 
                       name="password" 
                       id="password" 
                       required
                       class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-300 @enderror"
                       placeholder="••••••••">
                <button type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas text-lg" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>
        
        <!-- Se souvenir de moi -->
        <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="remember" 
                       x-model="remember"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
            </label>
        </div>
        
        <!-- Bouton de connexion -->
        <button type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
        </button>
        
        <!-- Séparateur -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">Ou continuer avec</span>
            </div>
        </div>
        
        <!-- Connexion sociale -->
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('social.redirect', 'google') }}" 
               class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors group">
                <i class="fab fa-google text-red-500 mr-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Google</span>
            </a>
            <a href="{{ route('social.redirect', 'github') }}" 
               class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors group">
                <i class="fab fa-github text-gray-800 mr-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">GitHub</span>
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'entrée
        const form = document.querySelector('form');
        form.style.opacity = '0';
        form.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            form.style.transition = 'all 0.5s ease';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 100);
    });
</script>
@endpush