@extends('layouts.public')

@section('title', 'Erreur serveur - 500')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-zinc-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <!-- Code d'erreur -->
        <div class="mb-8">
            <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-600 to-gray-800">
                500
            </h1>
        </div>
        
        <!-- Illustration -->
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center">
                <i class="fas fa-server text-6xl text-gray-400"></i>
            </div>
        </div>
        
        <!-- Message -->
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Erreur serveur</h2>
        <p class="text-gray-600 mb-8 text-lg">
            Une erreur interne est survenue. Nos équipes techniques ont été notifiées.
        </p>
        
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('welcome') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
                <i class="fas fa-home mr-2"></i>
                Retour à l'accueil
            </a>
            <button onclick="window.location.reload()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-redo-alt mr-2"></i>
                Réessayer
            </button>
        </div>
        
        <p class="mt-8 text-sm text-gray-500">
            Si le problème persiste, 
            <a href="{{ route('contact') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">contactez le support</a>.
        </p>
    </div>
</div>
@endsection