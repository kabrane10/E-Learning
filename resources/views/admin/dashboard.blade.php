@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-home"></i>
            </a>
        </li>
        <li>
            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
        </li>
        <li class="text-sm font-medium text-gray-700">Tableau de bord</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6 animate-fade-in">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Bonjour, {{ Auth::user()->name }} ! 👋</h1>
            <p class="text-gray-500 mt-1">Voici ce qui se passe sur votre plateforme aujourd'hui.</p>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Users -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 cursor-pointer hover:border-indigo-300"
                 onclick="window.location='{{ route('admin.users.index') }}'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Utilisateurs</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_users'] ?? 0) }}</p>
                        <div class="flex items-center mt-2 text-green-600">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span class="text-sm font-medium">+12%</span>
                            <span class="text-xs text-gray-400 ml-1">vs mois dernier</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Total Courses -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 cursor-pointer hover:border-indigo-300"
                 onclick="window.location='{{ route('admin.courses.index') }}'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Cours</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_courses'] ?? 0) }}</p>
                        <div class="flex items-center mt-2 text-green-600">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span class="text-sm font-medium">+8%</span>
                            <span class="text-xs text-gray-400 ml-1">vs mois dernier</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Enrollments -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6 cursor-pointer hover:border-indigo-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Inscriptions</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_enrollments'] ?? 0) }}</p>
                        <div class="flex items-center mt-2 text-green-600">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span class="text-sm font-medium">+23%</span>
                            <span class="text-xs text-gray-400 ml-1">vs mois dernier</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Completion Rate -->
            <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Taux de complétion</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['completion_rate'] ?? 0 }}%</p>
                        <div class="flex items-center mt-2 text-orange-600">
                            <i class="fas fa-minus text-xs mr-1"></i>
                            <span class="text-sm font-medium">Stable</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-trophy text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Enrollments Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Inscriptions (30 jours)</h3>
                    <select class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option>7 jours</option>
                        <option selected>30 jours</option>
                        <option>90 jours</option>
                    </select>
                </div>
                <!-- IMPORTANT: Conteneur avec hauteur fixe -->
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="enrollmentsChart"></canvas>
                </div>
            </div>
    
            <!-- Course Categories -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Répartition par catégorie</h3>
                    <a href="{{ route('admin.categories.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <!-- IMPORTANT: Conteneur avec hauteur fixe -->
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Derniers utilisateurs</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir tout
                    </a>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($latestUsers ?? [] as $user)
                        <div class="px-6 py-4 table-row-hover">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $user->avatar }}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                               {{ $user->hasRole('admin') ? 'bg-red-100 text-red-700' : 
                                                  ($user->hasRole('instructor') ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                        {{ $user->roles->first()->name ?? 'student' }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-3xl mb-2 opacity-50"></i>
                            <p>Aucun utilisateur récent</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Recent Courses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Derniers cours</h3>
                    <a href="{{ route('admin.courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir tout
                    </a>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($latestCourses ?? [] as $course)
                        <div class="px-6 py-4 table-row-hover">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $course->thumbnail_url }}" class="w-12 h-12 rounded-lg object-cover">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $course->instructor->name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if(!$course->is_published)
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">
                                            Brouillon
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                            Publié
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $course->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-book text-3xl mb-2 opacity-50"></i>
                            <p>Aucun cours récent</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Popular Courses -->
        <div class="mt-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Cours les plus populaires</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formateur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiants</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de complétion</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($popularCourses ?? [] as $index => $course)
                                <tr class="table-row-hover animate-slide-in" style="animation-delay: {{ $index * 50 }}ms">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img src="{{ $course->thumbnail_url }}" class="w-10 h-10 rounded object-cover mr-3">
                                            <span class="text-sm font-medium text-gray-900">{{ $course->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $course->instructor->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $course->students_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-yellow-400 mr-1">★</span>
                                            <span class="text-sm text-gray-600">{{ number_format($course->average_rating, 1) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ rand(40, 95) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-chart-bar text-3xl mb-2 opacity-50"></i>
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Valeurs par défaut
        const defaultEnrollmentsData = {
            labels: ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'],
            data: [12, 19, 15, 22]
        };
        
        const defaultCategoriesData = {
            labels: ['Développement', 'Design', 'Marketing', 'Business', 'Data Science'],
            data: [35, 25, 20, 12, 8]
        };
        
        // Enrollments Chart
        const enrollmentsCtx = document.getElementById('enrollmentsChart')?.getContext('2d');
        if (enrollmentsCtx) {
            let enrollmentsData;
            try {
                const phpData = @json($enrollmentsChart ?? null);
                enrollmentsData = (phpData && phpData.labels?.length) ? phpData : defaultEnrollmentsData;
            } catch (e) {
                enrollmentsData = defaultEnrollmentsData;
            }
            
            new Chart(enrollmentsCtx, {
                type: 'line',
                data: {
                    labels: enrollmentsData.labels,
                    datasets: [{
                        label: 'Inscriptions',
                        data: enrollmentsData.data,
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
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        
        // Categories Chart
        const categoriesCtx = document.getElementById('categoriesChart')?.getContext('2d');
        if (categoriesCtx) {
            let categoriesData;
            try {
                const phpData = @json($categoriesChart ?? null);
                categoriesData = (phpData && phpData.labels?.length) ? phpData : defaultCategoriesData;
            } catch (e) {
                categoriesData = defaultCategoriesData;
            }
            
            new Chart(categoriesCtx, {
                type: 'doughnut',
                data: {
                    labels: categoriesData.labels,
                    datasets: [{
                        data: categoriesData.data,
                        backgroundColor: [
                            'rgb(79, 70, 229)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(139, 92, 246)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f3f4f6',
                            bodyColor: '#d1d5db',
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    });
</script>
@endpush