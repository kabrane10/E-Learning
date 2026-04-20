@extends('layouts.guest')

@section('title', 'Mot de passe oublié')
@section('auth_title', 'Réinitialisez votre mot de passe')
@section('auth_description', 'Pas de panique ! Nous allons vous aider à retrouver l\'accès à votre compte.')

@section('content')
<div x-data="{ submitted: false }" class="animate-fade-in">
    
    <!-- Titre -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Mot de passe oublié ?</h1>
        <p class="text-gray-500 mt-2">
            Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>
    </div>
    
    <!-- Message de statut -->
    @if(session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl"
             x-show="!submitted"
             x-transition>
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-600 mt-0.5 mr-3"></i>
                <div>
                    <p class="text-sm font-medium text-green-800">Email envoyé !</p>
                    <p class="text-sm text-green-700 mt-1">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm text-red-700 font-medium mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>Erreur
            </p>
            <ul class="text-xs text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Formulaire -->
    <form method="POST" action="{{ route('password.email') }}" @submit="submitted = true">
        @csrf
        
        <div class="mb-6">
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
        
        <button type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-paper-plane mr-2"></i>Envoyer le lien
        </button>
        
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-1"></i>Retour à la connexion
            </a>
        </div>
    </form>
</div>
@endsection