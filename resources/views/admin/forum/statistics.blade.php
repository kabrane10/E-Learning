@extends('layouts.admin')

@section('title', 'Statistiques du forum')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Statistiques du forum</h1>
                <p class="text-gray-500 mt-1">Vue d'ensemble de l'activité du forum</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.forum.categories.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-folder mr-2"></i>Catégories
                </a>
                <a href="{{ route('admin.forum.topics.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-comments mr-2"></i>Sujets
                </a>
            </div>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs text-gray-500 uppercase">Catégories</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['categories_count'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs text-gray-500 uppercase">Sujets totaux</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['topics_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs text-gray-500 uppercase">Messages totaux</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['posts_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs text-gray-500 uppercase">Sujets aujourd'hui</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['topics_today'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs text-gray-500 uppercase">Messages aujourd'hui</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['posts_today'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Évolution des sujets -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Évolution des sujets (30 jours)</h3>
                </div>
                <div class="p-6">
                    <div style="position: relative; height: 250px;">
                        <canvas id="topicsChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Répartition par catégorie -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Répartition par catégorie</h3>
                </div>
                <div class="p-6">
                    <div style="position: relative; height: 250px;">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top sujets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Sujets les plus actifs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Messages</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vues</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Likes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dernière activité</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topTopics ?? [] as $topic)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <a href="{{ route('forum.topics.show', [$topic->category->slug ?? 'general', $topic->slug]) }}" 
                                           target="_blank"
                                           class="font-medium text-indigo-600 hover:text-indigo-700 line-clamp-1">
                                            {{ $topic->title }}
                                        </a>
                                        @if($topic->is_sticky)
                                            <span class="ml-2 text-xs text-indigo-500">
                                                <i class="fas fa-thumbtack"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <i class="fas fa-{{ $topic->category->icon ?? 'folder' }} text-{{ $topic->category->color ?? 'gray' }}-500 mr-2"></i>
                                        {{ $topic->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ $topic->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($topic->user->name) }}" 
                                             class="w-6 h-6 rounded-full mr-2">
                                        <span class="text-sm text-gray-900">{{ $topic->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $topic->posts_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($topic->views_count) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <i class="far fa-heart text-red-500 mr-1"></i>
                                    {{ $topic->likes_count }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $topic->last_post_at ? $topic->last_post_at->diffForHumans() : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('forum.topics.show', [$topic->category->slug ?? 'general', $topic->slug]) }}" 
                                       target="_blank"
                                       class="text-gray-400 hover:text-indigo-600"
                                       title="Voir le sujet">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-chart-bar text-4xl mb-3 opacity-30"></i>
                                    <p>Aucune donnée disponible</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Charger Chart.js depuis CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Variables pour stocker les instances des graphiques
    let topicsChartInstance = null;
    let categoriesChartInstance = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour les graphiques
        const topicsLabels = @json($topicsChartData['labels'] ?? []);
        const topicsData = @json($topicsChartData['data'] ?? []);
        const categoriesLabels = @json($categoriesChartData['labels'] ?? []);
        const categoriesData = @json($categoriesChartData['data'] ?? []);
        
        // Vérifier que Chart.js est bien chargé
        if (typeof Chart === 'undefined') {
            console.error('Chart.js n\'est pas chargé !');
            return;
        }
        
        // Graphique d'évolution des sujets
        const topicsCanvas = document.getElementById('topicsChart');
        if (topicsCanvas) {
            const ctx = topicsCanvas.getContext('2d');
            
            // Détruire l'instance précédente si elle existe
            if (topicsChartInstance) {
                topicsChartInstance.destroy();
            }
            
            // Créer le graphique
            topicsChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: topicsLabels.length > 0 ? topicsLabels : ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                    datasets: [{
                        label: 'Nouveaux sujets',
                        data: topicsData.length > 0 ? topicsData : [2, 5, 3, 8, 4, 6, 3],
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(79, 70, 229)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            display: false 
                        },
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
                            grid: { 
                                color: '#e5e7eb' 
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        },
                        x: {
                            grid: { 
                                display: false 
                            }
                        }
                    }
                }
            });
            
            console.log('✅ Graphique d\'évolution des sujets créé');
        } else {
            console.error('❌ Canvas #topicsChart non trouvé');
        }
        
        // Graphique de répartition par catégorie
        const categoriesCanvas = document.getElementById('categoriesChart');
        if (categoriesCanvas) {
            const ctx = categoriesCanvas.getContext('2d');
            
            // Détruire l'instance précédente si elle existe
            if (categoriesChartInstance) {
                categoriesChartInstance.destroy();
            }
            
            // Données par défaut si vides
            const defaultLabels = ['Développement', 'Design', 'Marketing', 'Business', 'Data Science'];
            const defaultData = [35, 25, 20, 12, 8];
            
            // Couleurs pour le graphique
            const backgroundColors = [
                'rgb(79, 70, 229)',   // indigo
                'rgb(16, 185, 129)',  // green
                'rgb(245, 158, 11)',  // yellow
                'rgb(239, 68, 68)',   // red
                'rgb(139, 92, 246)',  // purple
                'rgb(236, 72, 153)',  // pink
                'rgb(6, 182, 212)',   // cyan
                'rgb(251, 146, 60)'   // orange
            ];
            
            // Créer le graphique
            categoriesChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoriesLabels.length > 0 ? categoriesLabels : defaultLabels,
                    datasets: [{
                        data: categoriesData.length > 0 ? categoriesData : defaultData,
                        backgroundColor: backgroundColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: { 
                                    size: 12 
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                            
                                            return {
                                                text: `${label} (${percentage}%)`,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f3f4f6',
                            bodyColor: '#d1d5db',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} sujets (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
            
            console.log('✅ Graphique de répartition créé');
        } else {
            console.error('❌ Canvas #categoriesChart non trouvé');
        }
    });
    
    // Nettoyer les graphiques avant de quitter la page (bonne pratique)
    window.addEventListener('beforeunload', function() {
        if (topicsChartInstance) {
            topicsChartInstance.destroy();
        }
        if (categoriesChartInstance) {
            categoriesChartInstance.destroy();
        }
    });
</script>
@endpush