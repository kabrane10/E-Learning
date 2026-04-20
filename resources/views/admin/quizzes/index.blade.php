@extends('layouts.admin')

@section('title', 'Gestion des quiz')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Quiz</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="quizManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quiz</h1>
                <p class="text-gray-500 mt-1">Gérez tous les quiz de la plateforme</p>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total quiz</p>
                        <p class="text-2xl font-bold text-gray-900">48</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-puzzle-piece text-indigo-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Tentatives totales</p>
                        <p class="text-2xl font-bold text-gray-900">2,847</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Taux de réussite</p>
                        <p class="text-2xl font-bold text-gray-900">76%</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-trophy text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Score moyen</p>
                        <p class="text-2xl font-bold text-gray-900">72/100</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <input type="text" placeholder="Rechercher un quiz..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <select class="border-gray-300 rounded-lg">
                    <option>Tous les cours</option>
                </select>
                <select class="border-gray-300 rounded-lg">
                    <option>Tous les statuts</option>
                    <option>Publié</option>
                    <option>Brouillon</option>
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
            </form>
        </div>
        
        <!-- Table des quiz -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tentatives</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux réussite</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $quizzes = [
                                ['id' => 1, 'title' => 'Quiz HTML/CSS', 'course' => 'Développement Web', 'questions' => 15, 'attempts' => 234, 'pass_rate' => 82, 'is_published' => true],
                                ['id' => 2, 'title' => 'JavaScript Avancé', 'course' => 'JavaScript Moderne', 'questions' => 20, 'attempts' => 189, 'pass_rate' => 71, 'is_published' => true],
                                ['id' => 3, 'title' => 'UI/UX Design', 'course' => 'Design d\'interfaces', 'questions' => 12, 'attempts' => 156, 'pass_rate' => 88, 'is_published' => true],
                                ['id' => 4, 'title' => 'SEO Fundamentals', 'course' => 'Marketing Digital', 'questions' => 18, 'attempts' => 98, 'pass_rate' => 65, 'is_published' => false],
                                ['id' => 5, 'title' => 'Python Basics', 'course' => 'Data Science', 'questions' => 25, 'attempts' => 312, 'pass_rate' => 79, 'is_published' => true],
                            ];
                        @endphp
                        
                        @foreach($quizzes as $index => $quiz)
                            <tr class="table-row-hover animate-slide-in" style="animation-delay: {{ $index * 50 }}ms">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $quiz['title'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $quiz['course'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $quiz['questions'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $quiz['attempts'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $quiz['pass_rate'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $quiz['pass_rate'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $quiz['is_published'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $quiz['is_published'] ? 'Publié' : 'Brouillon' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.quizzes.show', $quiz['id']) }}" class="text-gray-400 hover:text-indigo-600">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.quizzes.statistics', $quiz['id']) }}" class="text-gray-400 hover:text-green-600">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <button class="text-gray-400 hover:text-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection