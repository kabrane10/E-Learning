@extends('layouts.public')

@section('title', 'Page non trouvée - 404')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <!-- Code d'erreur -->
        <div class="mb-8">
            <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                404
            </h1>
        </div>
        
        <!-- Illustration -->
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center">
                <i class="fas fa-map-signs text-6xl text-indigo-400"></i>
            </div>
        </div>
        
        <!-- Message -->
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Page non trouvée</h2>
        <p class="text-gray-600 mb-8 text-lg">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        
        <!-- Suggestions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 text-left">
            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Suggestions :
            </h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                    <span>Vérifiez l'URL pour d'éventuelles erreurs de frappe</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                    <span>Retournez à la page d'accueil pour naviguer sur le site</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                    <span>Utilisez la recherche pour trouver ce que vous cherchez</span>
                </li>
            </ul>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('welcome') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
                <i class="fas fa-home mr-2"></i>
                Retour à l'accueil
            </a>
            <a href="{{ route('courses.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-book-open mr-2"></i>
                Explorer les cours
            </a>
        </div>
        
        <!-- Contact -->
        <p class="mt-8 text-sm text-gray-500">
            Si vous pensez qu'il s'agit d'une erreur, 
            <a href="{{ route('contact') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">contactez-nous</a>.
        </p>
    </div>
</div>
@endsection