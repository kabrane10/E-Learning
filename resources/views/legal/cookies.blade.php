@extends('layouts.public')

@section('title', 'Politique des cookies')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Politique des cookies</h1>
            <p class="text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>
            
            <div class="prose prose-lg max-w-none">
                <h2>Qu'est-ce qu'un cookie ?</h2>
                <p>Un cookie est un petit fichier texte stocké sur votre appareil lors de votre visite sur notre site.</p>
                
                <h2>Types de cookies utilisés</h2>
                <ul>
                    <li><strong>Cookies essentiels</strong> : nécessaires au fonctionnement du site</li>
                    <li><strong>Cookies de préférences</strong> : mémorisent vos choix (langue, thème)</li>
                    <li><strong>Cookies analytiques</strong> : nous aident à comprendre comment vous utilisez le site</li>
                </ul>
                
                <h2>Gestion des cookies</h2>
                <p>Vous pouvez gérer vos préférences de cookies dans les paramètres de votre navigateur.</p>
            </div>
        </div>
    </div>
</div>
@endsection