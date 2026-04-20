@extends('layouts.public')

@section('title', 'À propos')

@section('content')
<div class="bg-gradient-to-br from-indigo-50 to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Hero -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">À propos d'E-Learn</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Notre mission est de rendre l'éducation accessible à tous, partout dans le monde.
            </p>
        </div>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white rounded-xl p-8 text-center shadow-sm">
                <div class="text-4xl font-bold text-indigo-600 mb-2">150+</div>
                <div class="text-gray-600">Cours disponibles</div>
            </div>
            <div class="bg-white rounded-xl p-8 text-center shadow-sm">
                <div class="text-4xl font-bold text-indigo-600 mb-2">10k+</div>
                <div class="text-gray-600">Étudiants actifs</div>
            </div>
            <div class="bg-white rounded-xl p-8 text-center shadow-sm">
                <div class="text-4xl font-bold text-indigo-600 mb-2">50+</div>
                <div class="text-gray-600">Formateurs experts</div>
            </div>
        </div>
        
        <!-- Notre histoire -->
        <div class="bg-white rounded-2xl p-8 md:p-12 shadow-sm mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Notre histoire</h2>
            <p class="text-gray-600 mb-4">
                Fondée en 2024, E-Learn est née de la conviction que l'éducation de qualité devrait être accessible à tous.
            </p>
            <p class="text-gray-600">
                Nous rassemblons les meilleurs formateurs pour créer des cours gratuits et interactifs dans divers domaines.
            </p>
        </div>
        
        <!-- Équipe -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Notre équipe</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-24 h-24 bg-indigo-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">Marie Dubois</h3>
                    <p class="text-gray-500">Fondatrice & CEO</p>
                </div>
                <div class="text-center">
                    <div class="w-24 h-24 bg-purple-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">Thomas Martin</h3>
                    <p class="text-gray-500">Directeur pédagogique</p>
                </div>
                <div class="text-center">
                    <div class="w-24 h-24 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-green-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">Sophie Laurent</h3>
                    <p class="text-gray-500">Responsable technique</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection