@extends('layouts.admin')

@section('title', $category->name . ' - Catégorie')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.categories.index') }}" class="text-gray-400 hover:text-gray-500">Catégories</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">{{ $category->name }}</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- En-tête de la catégorie -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-{{ $category->color }}-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-{{ $category->icon }} text-{{ $category->color }}-600 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                        <p class="text-gray-500">{{ $category->description }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="editCategory({{ $category->id }})" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </button>
                </div>
            </div>
            
            <!-- Statistiques -->
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $category->topics_count }}</p>
                    <p class="text-sm text-gray-500">Sujets</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $category->topics_sum_posts_count ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Messages</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $category->order }}</p>
                    <p class="text-sm text-gray-500">Ordre</p>
                </div>
            </div>
        </div>

        <!-- Sujets de la catégorie -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Sujets dans cette catégorie</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse($category->topics()->latest()->limit(20)->get() as $topic)
                    <div class="p-4 hover:bg-gray-50">
                        <a href="{{ route('admin.forum.topics.show', $topic) }}" class="font-medium text-indigo-600 hover:text-indigo-700">
                            {{ $topic->title }}
                        </a>
                        <p class="text-sm text-gray-500 mt-1">
                            Par {{ $topic->user->name }} • {{ $topic->created_at->diffForHumans() }}
                        </p>
                    </div>
                @empty
                    <p class="p-6 text-center text-gray-500">Aucun sujet dans cette catégorie</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection