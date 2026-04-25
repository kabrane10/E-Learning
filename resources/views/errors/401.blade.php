@extends('layouts.public')

@section('title', 'Non autorisé - 401')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-cyan-50 to-teal-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-600">
                401
            </h1>
        </div>
        
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl shadow-lg flex items-center justify-center">
                <i class="fas fa-user-lock text-6xl text-blue-400"></i>
            </div>
        </div>
        
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Non autorisé</h2>
        <p class="text-gray-600 mb-8 text-lg">
            Vous devez être connecté pour accéder à cette page.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Se connecter
            </a>
            <a href="{{ route('register') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-user-plus mr-2"></i>
                S'inscrire
            </a>
        </div>
    </div>
</div>
@endsection