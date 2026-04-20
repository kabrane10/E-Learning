@extends('layouts.admin')

@section('title', 'Gestion des catégories')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Catégories</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="categoryManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Catégories</h1>
                <p class="text-gray-500 mt-1">Organisez vos cours par catégories</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button @click="openCreateModal()" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Nouvelle catégorie
                </button>
            </div>
        </div>
        
        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $categories = [
                    ['id' => 1, 'name' => 'Développement Web', 'slug' => 'developpement-web', 'icon' => 'code', 'color' => 'blue', 'courses_count' => 24, 'is_active' => true],
                    ['id' => 2, 'name' => 'Design & Créativité', 'slug' => 'design', 'icon' => 'paint-brush', 'color' => 'pink', 'courses_count' => 18, 'is_active' => true],
                    ['id' => 3, 'name' => 'Marketing Digital', 'slug' => 'marketing', 'icon' => 'bullhorn', 'color' => 'green', 'courses_count' => 15, 'is_active' => true],
                    ['id' => 4, 'name' => 'Business & Entrepreneuriat', 'slug' => 'business', 'icon' => 'briefcase', 'color' => 'purple', 'courses_count' => 12, 'is_active' => true],
                    ['id' => 5, 'name' => 'Data Science', 'slug' => 'data-science', 'icon' => 'chart-line', 'color' => 'orange', 'courses_count' => 10, 'is_active' => true],
                    ['id' => 6, 'name' => 'Photographie', 'slug' => 'photographie', 'icon' => 'camera', 'color' => 'red', 'courses_count' => 8, 'is_active' => true],
                    ['id' => 7, 'name' => 'Musique & Audio', 'slug' => 'musique', 'icon' => 'music', 'color' => 'indigo', 'courses_count' => 6, 'is_active' => true],
                    ['id' => 8, 'name' => 'Langues', 'slug' => 'langues', 'icon' => 'language', 'color' => 'teal', 'courses_count' => 9, 'is_active' => true],
                    ['id' => 9, 'name' => 'Développement Mobile', 'slug' => 'mobile', 'icon' => 'mobile-alt', 'color' => 'yellow', 'courses_count' => 11, 'is_active' => false],
                ];
            @endphp
            
            @foreach($categories as $index => $category)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 animate-scale-in"
                     style="animation-delay: {{ $index * 50 }}ms">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-{{ $category['color'] }}-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-{{ $category['icon'] }} text-{{ $category['color'] }}-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $category['name'] }}</h3>
                                    <p class="text-xs text-gray-500">{{ $category['slug'] }}</p>
                                </div>
                            </div>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    <button @click="editCategory({{ $category['id'] }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-edit mr-2"></i>Modifier
                                    </button>
                                    <button @click="toggleStatus({{ $category['id'] }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-eye{{ $category['is_active'] ? '-slash' : '' }} mr-2"></i>
                                        {{ $category['is_active'] ? 'Désactiver' : 'Activer' }}
                                    </button>
                                    <hr class="my-1">
                                    <button @click="confirmDelete({{ $category['id'] }})" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash mr-2"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">
                                <i class="fas fa-book-open mr-1"></i>
                                {{ $category['courses_count'] }} cours
                            </span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $category['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $category['is_active'] ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a href="{{ route('courses.index', ['category' => $category['slug']]) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                Voir les cours <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Statistiques -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition des cours</h3>
                <canvas id="categoriesDistributionChart" height="250"></canvas>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Catégories populaires</h3>
                <div class="space-y-4">
                    @foreach(array_slice($categories, 0, 5) as $cat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-{{ $cat['color'] }}-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-{{ $cat['icon'] }} text-{{ $cat['color'] }}-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $cat['name'] }}</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">{{ $cat['courses_count'] }} cours</span>
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($cat['courses_count'] / 24) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Création/Édition -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="modalOpen = false"></div>
            
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="modalTitle"></h3>
                </div>
                
                <form @submit.prevent="saveCategory">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la catégorie</label>
                            <input type="text" x-model="categoryForm.name" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ex: Développement Web">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                            <input type="text" x-model="categoryForm.slug" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="ex: developpement-web">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icône (Font Awesome)</label>
                            <select x-model="categoryForm.icon"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="code">Code (Développement)</option>
                                <option value="paint-brush">Pinceau (Design)</option>
                                <option value="bullhorn">Mégaphone (Marketing)</option>
                                <option value="briefcase">Mallette (Business)</option>
                                <option value="chart-line">Graphique (Data)</option>
                                <option value="camera">Caméra (Photo)</option>
                                <option value="music">Musique</option>
                                <option value="language">Langues</option>
                                <option value="mobile-alt">Mobile</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                            <select x-model="categoryForm.color"
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="blue">Bleu</option>
                                <option value="green">Vert</option>
                                <option value="red">Rouge</option>
                                <option value="yellow">Jaune</option>
                                <option value="purple">Violet</option>
                                <option value="pink">Rose</option>
                                <option value="indigo">Indigo</option>
                                <option value="orange">Orange</option>
                                <option value="teal">Turquoise</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" x-model="categoryForm.is_active" id="is_active"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Catégorie active</label>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="modalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Suppression -->
    <div x-show="deleteModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Supprimer la catégorie</h3>
                    <p class="text-gray-500 mb-6">
                        Êtes-vous sûr de vouloir supprimer cette catégorie ? 
                        Les cours associés ne seront pas supprimés mais perdront leur catégorie.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button @click="deleteCategory()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function categoryManager() {
        return {
            modalOpen: false,
            deleteModalOpen: false,
            modalTitle: 'Nouvelle catégorie',
            editingCategory: null,
            categoryToDelete: null,
            categoryForm: {
                name: '',
                slug: '',
                icon: 'code',
                color: 'blue',
                is_active: true
            },
            
            openCreateModal() {
                this.editingCategory = null;
                this.modalTitle = 'Nouvelle catégorie';
                this.categoryForm = { name: '', slug: '', icon: 'code', color: 'blue', is_active: true };
                this.modalOpen = true;
            },
            
            editCategory(id) {
                this.editingCategory = id;
                this.modalTitle = 'Modifier la catégorie';
                this.categoryForm = { name: 'Développement Web', slug: 'developpement-web', icon: 'code', color: 'blue', is_active: true };
                this.modalOpen = true;
            },
            
            toggleStatus(id) {
                // Logique d'activation/désactivation
                alert('Statut de la catégorie modifié');
            },
            
            confirmDelete(id) {
                this.categoryToDelete = id;
                this.deleteModalOpen = true;
            },
            
            saveCategory() {
                this.modalOpen = false;
                alert('Catégorie enregistrée avec succès !');
            },
            
            deleteCategory() {
                this.deleteModalOpen = false;
                alert('Catégorie supprimée avec succès !');
            }
        }
    }
    
    // Graphique de distribution
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('categoriesDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Développement', 'Design', 'Marketing', 'Business', 'Data', 'Photo', 'Musique', 'Langues', 'Mobile'],
                datasets: [{
                    label: 'Nombre de cours',
                    data: [24, 18, 15, 12, 10, 8, 6, 9, 11],
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
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
    });
</script>
@endpush