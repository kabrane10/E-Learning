@extends('layouts.admin')

@section('title', 'Forum - Administration')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Gestion du forum</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Accès rapide aux catégories -->
            <a href="{{ route('admin.forum.categories.index') }}" 
               class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-folder text-indigo-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Catégories</h3>
                <p class="text-gray-500 text-sm mt-1">Gérer les catégories du forum</p>
            </a>
            
            <!-- Accès rapide aux sujets -->
            <a href="{{ route('admin.forum.topics.index') }}" 
               class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-comments text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Sujets</h3>
                <p class="text-gray-500 text-sm mt-1">Gérer les sujets de discussion</p>
            </a>
            
            <!-- Accès rapide aux messages -->
            <a href="{{ route('admin.forum.posts.index') }}" 
               class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-reply text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Messages</h3>
                <p class="text-gray-500 text-sm mt-1">Gérer tous les messages</p>
            </a>
            
            <!-- Accès aux statistiques -->
            <a href="{{ route('admin.forum.statistics') }}" 
               class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Statistiques</h3>
                <p class="text-gray-500 text-sm mt-1">Voir les statistiques du forum</p>
            </a>
        </div>
    </div>
</div>
@endsection