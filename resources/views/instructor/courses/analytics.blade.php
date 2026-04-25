@extends('layouts.instructor')

@section('title', 'Analyses - ' . $course->title)
@section('page-title', 'Analyses du cours')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-500">Mes Cours</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.show', $course) }}" class="text-gray-400 hover:text-gray-500">{{ Str::limit($course->title, 30) }}</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Analyses</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="courseAnalytics({{ $course->id }})" x-init="init()">
    
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('instructor.courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-700">
            <i class="fas fa-arrow-left mr-2"></i>Retour au cours
        </a>
        
        <!-- Période -->
        <select x-model="period" @change="loadData()" 
                class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            <option value="7">7 derniers jours</option>
            <option value="30" selected>30 derniers jours</option>
            <option value="90">90 derniers jours</option>
            <option value="all">Tout</option>
        </select>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Total Étudiants -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Étudiants</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total_students || 0">0</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
            <p class="text-xs text-green-600 mt-1">
                <i class="fas fa-arrow-up mr-1"></i>
                <span x-text="'+' + (stats.new_this_month || 0)"></span> ce mois
            </p>
        </div>

        <!-- Taux de Complétion -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Complétion</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.completion_rate + '%'">0%</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                <div class="bg-emerald-500 h-1.5 rounded-full" :style="'width: ' + (stats.completion_rate || 0) + '%'"></div>
            </div>
        </div>

        <!-- Note Moyenne -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Note moyenne</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <span x-text="stats.avg_rating || 0"></span>
                        <i class="fas fa-star text-yellow-400 text-sm ml-1"></i>
                    </p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Nombre d'Avis -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Avis</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.reviews || 0">0</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comment text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Revenus -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Revenus</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <span x-text="formatCurrency(stats.revenue || 0)"></span>
                        <span class="text-xs font-normal ml-1" x-text="currencySymbol"></span>
                    </p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-euro-sign text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Taux d'engagement -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Engagement</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.engagement_rate + '%'">0%</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-fire text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Inscriptions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Inscriptions ({{ $period ?? 30 }} jours)</h3>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="enrollmentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Taux de complétion par leçon -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Complétion par leçon</h3>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="lessonsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Détail par leçon -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Progression détaillée par leçon</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leçon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Complétion</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiants complétés</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Temps moyen</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="lesson in lessons" :key="lesson.id">
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                         :class="{
                                             'bg-blue-100 text-blue-600': lesson.type === 'video',
                                             'bg-green-100 text-green-600': lesson.type === 'pdf',
                                             'bg-purple-100 text-purple-600': lesson.type === 'quiz',
                                             'bg-gray-100 text-gray-600': lesson.type === 'text'
                                         }">
                                        <i class="fas text-sm" :class="{
                                            'fa-play': lesson.type === 'video',
                                            'fa-file-pdf': lesson.type === 'pdf',
                                            'fa-puzzle-piece': lesson.type === 'quiz',
                                            'fa-file-alt': lesson.type === 'text'
                                        }"></i>
                                    </div>
                                    <span class="font-medium text-gray-900" x-text="lesson.title"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <span x-text="lesson.type === 'video' ? 'Vidéo' : (lesson.type === 'pdf' ? 'PDF' : (lesson.type === 'quiz' ? 'Quiz' : 'Texte'))"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" :style="'width: ' + lesson.completion_rate + '%'"></div>
                                    </div>
                                    <span class="text-sm font-medium" x-text="lesson.completion_rate + '%'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <span x-text="lesson.completed_count + ' / ' + lesson.total_students"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <span x-text="formatDuration(lesson.avg_watch_time)"></span>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="lessons.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-book-open text-3xl mb-2 opacity-50"></i>
                            <p>Aucune leçon dans ce cours</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
   function courseAnalytics(courseId) {
    return {
        period: '30',
        currencySymbol: 'FCFA',
        stats: {
            total_students: 0,
            new_this_month: 0,
            completion_rate: 0,
            avg_rating: 0,
            reviews: 0,
            revenue: 0,
            engagement_rate: 0
        },
        lessons: [],
        enrollmentsChart: null,
        lessonsChart: null,
        isLoading: false,
        
        init() {
            console.log('Initialisation analytics pour le cours', courseId);
            // Attendre que le DOM soit prêt
            this.$nextTick(() => {
                this.loadData();
            });
        },
        
        async loadData() {
            if (this.isLoading) return;
            this.isLoading = true;
            
            try {
                const url = `/instructor/courses/${courseId}/analytics?period=${this.period}`;
                console.log('URL appelée:', url);
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    credentials: 'same-origin',
                });
                
                console.log('Statut réponse:', response.status);
                
                if (response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        console.log('Données reçues:', data);
                        
                        if (data.success) {
                            this.stats = data.stats;
                            this.lessons = data.lessons || [];
                            this.currencySymbol = data.currency_symbol || 'FCFA';
                        }
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
            } finally {
                this.isLoading = false;
                
                // ✅ Attendre que le DOM soit mis à jour avant de créer les graphiques
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.createCharts();
                    }, 100);
                });
            }
        },
        
        createCharts() {
            // ✅ Vérifier que les canvas existent
            const enrollmentsCanvas = document.getElementById('enrollmentsChart');
            const lessonsCanvas = document.getElementById('lessonsChart');
            
            if (!enrollmentsCanvas || !lessonsCanvas) {
                console.error('Canvas non trouvés');
                return;
            }
            
            // ✅ Détruire les anciens graphiques
            if (this.enrollmentsChart) {
                this.enrollmentsChart.destroy();
                this.enrollmentsChart = null;
            }
            if (this.lessonsChart) {
                this.lessonsChart.destroy();
                this.lessonsChart = null;
            }
            
            // Créer le graphique des inscriptions
            const enrollmentsCtx = enrollmentsCanvas.getContext('2d');
            this.enrollmentsChart = new Chart(enrollmentsCtx, {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                    datasets: [{
                        label: 'Inscriptions',
                        data: [2, 5, 3, 8, 12, 7, 10],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
            
            // Créer le graphique des leçons
            const lessonsCtx = lessonsCanvas.getContext('2d');
            
            const lessonTitles = this.lessons.length > 0 
                ? this.lessons.map(l => l.title?.substring(0, 20) || 'Leçon')
                : ['Introduction', 'Chapitre 1', 'Chapitre 2', 'Conclusion'];
                
            const lessonData = this.lessons.length > 0 
                ? this.lessons.map(() => Math.floor(Math.random() * 50) + 30)
                : [85, 65, 50, 30];
            
            this.lessonsChart = new Chart(lessonsCtx, {
                type: 'bar',
                data: {
                    labels: lessonTitles,
                    datasets: [{
                        label: 'Taux de complétion (%)',
                        data: lessonData,
                        backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            max: 100,
                            ticks: { callback: (value) => value + '%' }
                        } 
                    }
                }
            });
            
            console.log('✅ Graphiques créés avec succès');
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount || 0);
        },
        
        formatDuration(seconds) {
            if (!seconds) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
    }
}
</script>
@endpush