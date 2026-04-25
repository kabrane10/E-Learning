@extends('layouts.instructor')

@section('title', 'Tentatives - ' . $quiz->title)
@section('page-title', 'Tentatives du quiz')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.quizzes.index') }}" class="text-gray-400 hover:text-gray-500">Mes Quiz</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="text-gray-400 hover:text-gray-500">{{ Str::limit($quiz->title, 30) }}</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Tentatives</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="attemptsManager({{ $quiz->id }})">
    
    <!-- En-tête avec retour -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('instructor.quizzes.index') }}" class="text-indigo-600 hover:text-indigo-700">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux quiz
        </a>
        <div class="flex gap-3">
            <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
                <i class="fas fa-edit mr-2"></i>Modifier le quiz
            </a>
            <a href="{{ route('instructor.quizzes.statistics', $quiz) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                <i class="fas fa-chart-bar mr-2"></i>Statistiques
            </a>
        </div>
    </div>

    <!-- Informations du quiz -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $quiz->title }}</h2>
                <p class="text-gray-500 mt-1">{{ $quiz->description ?: 'Aucune description' }}</p>
                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                    <span><i class="fas fa-book-open mr-1"></i>{{ $quiz->lesson->course->title ?? 'N/A' }}</span>
                    <span><i class="fas fa-question-circle mr-1"></i>{{ $quiz->questions_count }} questions</span>
                    <span><i class="fas fa-check-circle mr-1"></i>Score minimum : {{ $quiz->passing_score }}%</span>
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1.5 text-xs font-medium rounded-full {{ $quiz->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $quiz->is_published ? 'Publié' : 'Brouillon' }}
                </span>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Total tentatives</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Réussites</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['passed'] ?? 0) }}</p>
                    @if($stats['total'] > 0)
                        <p class="text-xs text-gray-500">{{ round(($stats['passed'] / $stats['total']) * 100) }}% de réussite</p>
                    @endif
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Échecs</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['failed'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Score moyen</p>
                    <p class="text-2xl font-bold text-gray-900">{{ round($stats['avg_score'] ?? 0) }}%</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Rechercher un étudiant..." 
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </div>
            <select name="result" class="w-full py-2.5 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                <option value="">Tous les résultats</option>
                <option value="passed" {{ request('result') == 'passed' ? 'selected' : '' }}>Réussites</option>
                <option value="failed" {{ request('result') == 'failed' ? 'selected' : '' }}>Échecs</option>
            </select>
            <div class="flex justify-end">
                <a href="{{ route('instructor.quizzes.attempts', $quiz) }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 text-sm">
                    Réinitialiser les filtres
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des tentatives -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Score</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Résultat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Temps</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bonnes réponses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attempts as $attempt)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $attempt->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($attempt->user->name) }}" 
                                         class="w-8 h-8 rounded-full mr-3">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $attempt->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $attempt->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-lg {{ $attempt->score >= $quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $attempt->score }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $attempt->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $attempt->is_passed ? '✅ Réussi' : '❌ Échoué' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                @if($attempt->time_spent)
                                    {{ floor($attempt->time_spent / 60) }}:{{ str_pad($attempt->time_spent % 60, 2, '0', STR_PAD_LEFT) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                {{ $attempt->correct_answers }}/{{ $attempt->total_questions }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">
                                {{ $attempt->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="viewDetails({{ $attempt->id }})" 
                                        class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>Détails
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium">Aucune tentative</p>
                                <p class="text-sm mt-1">Aucun étudiant n'a encore tenté ce quiz.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($attempts->hasPages())
        <div class="mt-6">
            {{ $attempts->withQueryString()->links() }}
        </div>
    @endif

    <!-- Modal Détails -->
    <div x-show="detailModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="detailModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Détails de la tentative</h3>
                    <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6" id="attempt-details-content">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-indigo-600 text-2xl"></i>
                        <p class="text-gray-500 mt-2">Chargement des détails...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function attemptsManager(quizId) {
        return {
            detailModalOpen: false,
            
            async viewDetails(attemptId) {
                this.detailModalOpen = true;
                
                try {
                    const response = await fetch(`/instructor/quizzes/attempts/${attemptId}/details`);
                    const html = await response.text();
                    document.getElementById('attempt-details-content').innerHTML = html;
                } catch (error) {
                    document.getElementById('attempt-details-content').innerHTML = `
                        <div class="text-center py-4 text-red-500">
                            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                            <p>Erreur lors du chargement des détails</p>
                        </div>
                    `;
                }
            }
        }
    }
</script>
@endpush