@extends('layouts.public')

@section('title', 'Session expirée - 419')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <!-- Code d'erreur -->
        <div class="mb-8">
            <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-orange-600">
                419
            </h1>
        </div>
        
        <!-- Illustration -->
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center">
                <i class="fas fa-clock text-6xl text-amber-400"></i>
            </div>
        </div>
        
        <!-- Message -->
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Session expirée</h2>
        <p class="text-gray-600 mb-8 text-lg">
            Votre session a expiré. Veuillez vous reconnecter pour continuer.
        </p>
        
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Se reconnecter
            </a>
            <a href="{{ route('welcome') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-home mr-2"></i>
                Accueil
            </a>
        </div>
    </div>
</div>
@endsection