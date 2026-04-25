@extends('layouts.public')

@section('title', 'Accès interdit - 403')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <!-- Code d'erreur -->
        <div class="mb-8">
            <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600">
                403
            </h1>
        </div>
        
        <!-- Illustration -->
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center">
                <i class="fas fa-lock text-6xl text-red-400"></i>
            </div>
        </div>
        
        <!-- Message -->
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Accès interdit</h2>
        <p class="text-gray-600 mb-8 text-lg">
            Vous n'avez pas les autorisations nécessaires pour accéder à cette page.
        </p>
        
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('welcome') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
                <i class="fas fa-home mr-2"></i>
                Retour à l'accueil
            </a>
            @guest
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Se connecter
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection