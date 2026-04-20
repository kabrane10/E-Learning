@extends('layouts.admin')

@section('title', 'Rapports et statistiques')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Rapports</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="reportsManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Rapports et statistiques</h1>
            <p class="text-gray-500 mt-1">Analysez les performances de votre plateforme</p>
        </div>
        
        <!-- Période -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <button @click="period = '7d'; updateCharts()" 
                            :class="period === '7d' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        7 jours
                    </button>
                    <button @click="period = '30d'; updateCharts()" 
                            :class="period === '30d' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        30 jours
                    </button>
                    <button @click="period = '90d'; updateCharts()" 
                            :class="period === '90d' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        90 jours
                    </button>
                    <button @click="period = '12m'; updateCharts()" 
                            :class="period === '12m' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        12 mois
                    </button>
                </div>
                <div class="flex items-center space-x-2 ml-auto">
                    <button @click="exportPDF()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
                    </button>
                    <button @click="exportExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Exporter Excel
                    </button>
                </div>
            </div>
        </div>
        
        <!-- KPIs -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 stat-card">
                <p class="text-xs text-gray-500 uppercase">Nouveaux utilisateurs</p>
                <p class="text-2xl font-bold text-gray-900">1,247</p>
                <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +12.5%</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 stat-card">
                <p class="text-xs text-gray-500 uppercase">Inscriptions</p>
                <p class="text-2xl font-bold text-gray-900">3,892</p>
                <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +8.3%</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 stat-card">
                <p class="text-xs text-gray-500 uppercase">Cours complétés</p>
                <p class="text-2xl font-bold text-gray-900">1,023</p>
                <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +15.2%</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 stat-card">
                <p class="text-xs text-gray-500 uppercase">Quiz réussis</p>
                <p class="text-2xl font-bold text-gray-900">2,156</p>
                <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +10.7%</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 stat-card">
                <p class="text-xs text-gray-500 uppercase">Temps moyen</p>
                <p class="text-2xl font-bold text-gray-900">4.2h</p>
                <p class="text-xs text-orange-600"><i class="fas fa-minus"></i> Stable</p>
            </div>
        </div>
        
        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution des inscriptions</h3>
                <div style="position: relative; height: 250px;">
                    <canvas id="enrollmentsEvolutionChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de complétion par cours</h3>
                <div style="position: relative; height: 250px;">
                    <canvas id="completionRateChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tableau détaillé -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Détail par cours</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrits</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Complétés</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux complétion</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note moyenne</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz réussis</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $courseStats = [
                                ['name' => 'Développement Web Complet', 'enrolled' => 1247, 'completed' => 423, 'completion' => 34, 'rating' => 4.8, 'quiz_passed' => 389],
                                ['name' => 'JavaScript Avancé', 'enrolled' => 892, 'completed' => 312, 'completion' => 35, 'rating' => 4.7, 'quiz_passed' => 278],
                                ['name' => 'UI/UX Design', 'enrolled' => 756, 'completed' => 298, 'completion' => 39, 'rating' => 4.9, 'quiz_passed' => 245],
                                ['name' => 'Python pour Data Science', 'enrolled' => 1023, 'completed' => 401, 'completion' => 39, 'rating' => 4.6, 'quiz_passed' => 356],
                                ['name' => 'Marketing Digital', 'enrolled' => 634, 'completed' => 189, 'completion' => 30, 'rating' => 4.5, 'quiz_passed' => 167],
                            ];
                        @endphp
                        
                        @foreach($courseStats as $stat)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $stat['name'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ number_format($stat['enrolled']) }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ number_format($stat['completed']) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $stat['completion'] }}%"></div>
                                        </div>
                                        <span>{{ $stat['completion'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="text-yellow-400 mr-1">★</span>
                                        <span>{{ $stat['rating'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ number_format($stat['quiz_passed']) }}</td>
                            </tr>
                        @endforeach
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
    // Variables globales pour les graphiques
    let enrollmentsChart = null;
    let completionRateChart = null;
    
    function reportsManager() {
        return {
            period: '30d',
            
            updateCharts() {
                // Mettre à jour les données selon la période
                let enrollmentsData;
                let completionData;
                
                switch(this.period) {
                    case '7d':
                        enrollmentsData = [120, 145, 168, 190, 210, 235, 260];
                        completionData = [28, 30, 32, 35, 33, 36, 34];
                        break;
                    case '30d':
                        enrollmentsData = [520, 580, 650, 720, 810, 950, 1100, 1250, 1420, 1600, 1850, 2100];
                        completionData = [32, 34, 33, 36, 35, 38, 37, 39, 38, 40, 39, 41];
                        break;
                    case '90d':
                        enrollmentsData = [1800, 2100, 2400, 2800, 3200, 3600, 4100, 4600, 5200, 5800, 6500, 7200];
                        completionData = [35, 37, 36, 39, 38, 40, 42, 41, 43, 44, 42, 45];
                        break;
                    case '12m':
                        enrollmentsData = [650, 720, 890, 1020, 1150, 1340, 1580, 1890, 2100, 2450, 2890, 3247];
                        completionData = [34, 35, 39, 39, 30, 35, 38, 36, 40, 42, 38, 41];
                        break;
                }
                
                // Mettre à jour le graphique d'évolution
                if (enrollmentsChart) {
                    enrollmentsChart.data.datasets[0].data = enrollmentsData;
                    enrollmentsChart.update();
                }
                
                // Mettre à jour le graphique de complétion
                if (completionRateChart) {
                    completionRateChart.data.datasets[0].data = completionData;
                    completionRateChart.update();
                }
            },
            
            exportPDF() {
                // Récupérer les canvas
                const canvas1 = document.getElementById('enrollmentsEvolutionChart');
                const canvas2 = document.getElementById('completionRateChart');
                
                // Créer une nouvelle fenêtre pour l'export
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Rapport E-Learn</title>
                            <style>
                                body { font-family: Arial, sans-serif; padding: 20px; }
                                h1 { color: #1f2937; }
                                .header { text-align: center; margin-bottom: 30px; }
                                .chart-container { margin-bottom: 40px; }
                                img { max-width: 100%; height: auto; }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>Rapport E-Learn</h1>
                                <p>Généré le ${new Date().toLocaleDateString('fr-FR')}</p>
                                <p>Période : ${this.period}</p>
                            </div>
                            <div class="chart-container">
                                <h2>Évolution des inscriptions</h2>
                                <img src="${canvas1.toDataURL('image/png')}" />
                            </div>
                            <div class="chart-container">
                                <h2>Taux de complétion par cours</h2>
                                <img src="${canvas2.toDataURL('image/png')}" />
                            </div>
                        </body>
                    </html>
                `);
                printWindow.document.close();
                
                setTimeout(() => {
                    printWindow.print();
                }, 500);
            },
            
            exportExcel() {
                // Créer un CSV simple
                let csv = 'Cours,Inscrits,Complétés,Taux de complétion,Note moyenne,Quiz réussis\n';
                
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 6) {
                        const courseName = cells[0].innerText.trim();
                        const enrolled = cells[1].innerText.trim();
                        const completed = cells[2].innerText.trim();
                        const completionRate = cells[3].querySelector('span:last-child')?.innerText.trim() || '';
                        const rating = cells[4].querySelector('span:last-child')?.innerText.trim() || '';
                        const quizPassed = cells[5].innerText.trim();
                        
                        csv += `"${courseName}",${enrolled},${completed},${completionRate},${rating},${quizPassed}\n`;
                    }
                });
                
                // Télécharger le CSV
                const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `rapport_${this.period}_${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
    
    // Initialisation des graphiques au chargement
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier que Chart est bien chargé
        if (typeof Chart === 'undefined') {
            console.error('Chart.js n\'est pas chargé !');
            return;
        }
        
        // Graphique évolution des inscriptions
        const canvas1 = document.getElementById('enrollmentsEvolutionChart');
        if (canvas1) {
            const ctx1 = canvas1.getContext('2d');
            
            // Détruire l'ancien graphique s'il existe
            if (enrollmentsChart) {
                enrollmentsChart.destroy();
            }
            
            enrollmentsChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Inscriptions',
                        data: [650, 720, 890, 1020, 1150, 1340, 1580, 1890, 2100, 2450, 2890, 3247],
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
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
                            grid: { color: '#e5e7eb' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        } else {
            console.error('Canvas #enrollmentsEvolutionChart non trouvé !');
        }
        
        // Graphique taux de complétion
        const canvas2 = document.getElementById('completionRateChart');
        if (canvas2) {
            const ctx2 = canvas2.getContext('2d');
            
            // Détruire l'ancien graphique s'il existe
            if (completionRateChart) {
                completionRateChart.destroy();
            }
            
            completionRateChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['Dev Web', 'JavaScript', 'UI/UX', 'Python', 'Marketing'],
                    datasets: [{
                        label: 'Taux de complétion (%)',
                        data: [34, 35, 39, 39, 30],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)'
                        ],
                        borderRadius: 8
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
                            max: 100,
                            grid: { color: '#e5e7eb' },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        } else {
            console.error('Canvas #completionRateChart non trouvé !');
        }
    });
</script>
@endpush