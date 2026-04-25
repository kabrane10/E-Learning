@extends('layouts.instructor')

@section('title', 'Statistiques - ' . $quiz->title)
@section('page-title', 'Statistiques du quiz')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.quizzes.index') }}" class="text-gray-400 hover:text-gray-500">Mes Quiz</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">{{ $quiz->title }} - Statistiques</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="quizStatistics({{ $quiz->id }})" x-init="init()">
    
    <!-- Retour -->
    <div class="mb-6">
        <a href="{{ route('instructor.quizzes.index') }}" class="text-indigo-600 hover:text-indigo-700">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux quiz
        </a>
    </div>

    <!-- En-tête du quiz -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $quiz->title }}</h1>
                <p class="text-gray-600">{{ $quiz->description ?: 'Aucune description' }}</p>
                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                    <span><i class="fas fa-book-open mr-1"></i>{{ $quiz->lesson->course->title ?? 'Cours' }}</span>
                    <span><i class="fas fa-question-circle mr-1"></i>{{ $quiz->questions_count }} questions</span>
                    <span><i class="fas fa-users mr-1"></i>{{ $quiz->attempts_count }} tentatives</span>
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $quiz->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $quiz->is_published ? 'Publié' : 'Brouillon' }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold {{ ($averageScore >= $quiz->passing_score) ? 'text-green-600' : 'text-red-600' }}">
                    {{ round($averageScore) }}%
                </div>
                <div class="text-sm text-gray-500">Score moyen</div>
                <div class="text-xs text-gray-400">Minimum requis : {{ $quiz->passing_score }}%</div>
            </div>
        </div>
        
        <!-- KPIs rapides -->
        <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $quiz->attempts_count }}</p>
                <p class="text-xs text-gray-500">Tentatives totales</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $passedCount }}</p>
                <p class="text-xs text-gray-500">Réussites</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">{{ $failedCount }}</p>
                <p class="text-xs text-gray-500">Échecs</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ round($successRate) }}%</p>
                <p class="text-xs text-gray-500">Taux de réussite</p>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Distribution des scores -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Distribution des scores</h3>
            <div style="position: relative; height: 250px;">
                <canvas id="scoresChart"></canvas>
            </div>
        </div>
        
        <!-- Questions les plus échouées -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Questions les plus échouées
            </h3>
            @if($mostFailedQuestions->count() > 0)
                <div class="space-y-3">
                    @foreach($mostFailedQuestions as $question)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $question->question_text }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-24 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $question->fail_rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-red-600 font-medium">{{ $question->fail_rate }}% d'échec</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucune donnée disponible</p>
            @endif
        </div>
    </div>

    <!-- Détail par question -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">
                <i class="fas fa-list-ul mr-2 text-indigo-500"></i>Détail par question
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Taux de réussite</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Réponses correctes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total réponses</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Temps moyen</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($questions as $index => $question)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $question->question_text }}</p>
                                <span class="text-xs text-gray-500">{{ $question->points }} point(s)</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $question->success_rate >= 70 ? 'bg-green-600' : ($question->success_rate >= 40 ? 'bg-amber-500' : 'bg-red-500') }}" 
                                             style="width: {{ $question->success_rate }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium {{ $question->success_rate >= 70 ? 'text-green-600' : ($question->success_rate >= 40 ? 'text-amber-600' : 'text-red-600') }}">
                                        {{ $question->success_rate }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $question->correct_answers }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $question->total_answers }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                {{ $question->avg_time > 0 ? $question->avg_time . 's' : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-chart-bar text-3xl mb-2 opacity-30"></i>
                                <p>Aucune donnée disponible</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function quizStatistics(quizId) {
        return {
            init() {
                this.initScoreChart();
            },
            
            initScoreChart() {
                const canvas = document.getElementById('scoresChart');
                if (!canvas) return;
                
                const ctx = canvas.getContext('2d');
                
                // ✅ Données dynamiques
                const labels = {!! json_encode($scoreDistribution['labels'] ?? ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%']) !!};
                const data = {!! json_encode($scoreDistribution['data'] ?? [0, 0, 0, 0, 0]) !!};
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nombre d\'étudiants',
                            data: data,
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(79, 70, 229, 0.8)'
                            ],
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                titleColor: '#f3f4f6',
                                bodyColor: '#d1d5db',
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#e5e7eb' },
                                ticks: { precision: 0 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
@endpush