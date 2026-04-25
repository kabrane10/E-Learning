@extends('layouts.public')

@section('title', 'Trop de requêtes - 429')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-rose-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">
                429
            </h1>
        </div>
        
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center">
                <i class="fas fa-tachometer-alt text-6xl text-purple-400"></i>
            </div>
        </div>
        
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Trop de requêtes</h2>
        <p class="text-gray-600 mb-8 text-lg">
            Vous avez effectué trop de requêtes. Veuillez patienter quelques instants avant de réessayer.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.location.reload()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
                <i class="fas fa-redo-alt mr-2"></i>
                Réessayer
            </button>
            <a href="{{ route('welcome') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-home mr-2"></i>
                Accueil
            </a>
        </div>
    </div>
</div>
@endsection