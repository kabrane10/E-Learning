@extends('layouts.instructor')

@section('title', 'Mes Quiz')
@section('page-title', 'Mes Quiz')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Mes Quiz</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="quizzesManager()" x-init="init()">
    
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Gérez vos quiz</h2>
            <p class="text-gray-500 text-sm mt-1">{{ $stats['total_quizzes'] ?? 0 }} quiz au total</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('instructor.courses.index') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fas fa-book-open mr-2"></i>
                Voir mes cours
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Total Quiz</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_quizzes'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-puzzle-piece text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Publiés</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['published_quizzes'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Tentatives</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_attempts'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Questions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_questions'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-question-circle text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('instructor.quizzes.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Rechercher un quiz..." 
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </div>
            <select name="course_id" class="w-full py-2.5 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                <option value="">Tous les cours</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="w-full py-2.5 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publiés</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillons</option>
            </select>
        </form>
    </div>

    <!-- Liste des quiz -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Questions</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tentatives</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Score moyen</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($quizzes as $quiz)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-puzzle-piece text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $quiz->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $quiz->lesson->title ?? 'Leçon' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $quiz->lesson->course->title ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center text-gray-900 font-medium">{{ $quiz->questions_count }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ number_format($quiz->attempts_count) }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($quiz->attempts_count > 0)
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="font-medium {{ $quiz->avg_score >= $quiz->passing_score ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ round($quiz->avg_score) }}%
                                        </span>
                                        <span class="text-xs text-gray-400">/ {{ $quiz->passing_score }}%</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $quiz->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $quiz->is_published ? 'Publié' : 'Brouillon' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('instructor.quizzes.statistics', $quiz) }}" 
                                       class="p-2 text-gray-400 hover:text-green-600 rounded-lg hover:bg-green-50"
                                       title="Statistiques">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('instructor.quizzes.attempts', $quiz) }}" 
                                       class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50"
                                       title="Tentatives">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <a href="{{ route('instructor.quizzes.edit', $quiz) }}" 
                                       class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                                             class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50">
                                            <button @click="duplicateQuiz({{ $quiz->id }})" 
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="fas fa-copy mr-2"></i>Dupliquer
                                            </button>
                                            <hr class="my-1">
                                            <button @click="confirmDelete({{ $quiz->id }})" 
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-trash mr-2"></i>Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-puzzle-piece text-purple-500 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium">Aucun quiz trouvé</p>
                                <p class="text-sm mt-1">Créez votre premier quiz en ajoutant une leçon de type "Quiz" à l'un de vos cours.</p>
                                <a href="{{ route('instructor.courses.index') }}" class="mt-4 inline-flex items-center text-indigo-600 hover:text-indigo-700">
                                    <i class="fas fa-arrow-right mr-2"></i>Voir mes cours
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($quizzes->hasPages())
        <div class="mt-6">
            {{ $quizzes->withQueryString()->links() }}
        </div>
    @endif

    <!-- Modal de confirmation -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Supprimer le quiz</h3>
                    <p class="text-gray-500 mb-6">Êtes-vous sûr de vouloir supprimer ce quiz ? Cette action est irréversible.</p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Annuler</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function quizzesManager() {
        return {
            deleteModalOpen: false,
            quizToDelete: null,
            
            init() { 
                console.log('Quizzes manager initialisé'); 
            },
            
            duplicateQuiz(id) { 
                fetch(`/instructor/quizzes/${id}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la duplication');
                    }
                })
                .catch(() => alert('Erreur de connexion'));
            },
            
            confirmDelete(id) { 
                this.quizToDelete = id;
                const form = document.getElementById('deleteForm');
                form.action = `/instructor/quizzes/${id}`;
                this.deleteModalOpen = true;
            }
        }
    }
</script>
@endpush