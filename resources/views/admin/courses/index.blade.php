@extends('layouts.admin')

@section('title', 'Gestion des cours')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Cours</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="courseManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Cours</h1>
                <p class="text-gray-500 mt-1">Gérez tous les cours de la plateforme</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-download mr-2"></i>Exporter
                </button>
                <button @click="openCreateModal()" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Nouveau cours
                </button>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" placeholder="Rechercher un cours..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <select class="border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option>Toutes les catégories</option>
                    <option>Développement</option>
                    <option>Design</option>
                    <option>Marketing</option>
                </select>
                <select class="border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option>Tous les statuts</option>
                    <option>Publié</option>
                    <option>Brouillon</option>
                    <option>En attente</option>
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
            </form>
        </div>
        
        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses ?? [] as $index => $course)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 animate-scale-in"
                     style="animation-delay: {{ $index * 50 }}ms">
                    <div class="relative">
                        <img src="{{ $course->thumbnail_url }}" class="w-full h-40 object-cover">
                        <div class="absolute top-3 right-3 flex space-x-1">
                            <button @click="editCourse({{ $course->id }})" 
                                    class="p-2 bg-white rounded-lg shadow-md hover:bg-gray-50 transition-colors">
                                <i class="fas fa-edit text-gray-600 text-sm"></i>
                            </button>
                            <button @click="confirmDelete({{ $course->id }})" 
                                    class="p-2 bg-white rounded-lg shadow-md hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash text-red-600 text-sm"></i>
                            </button>
                        </div>
                        <span class="absolute top-3 left-3 px-2 py-1 text-xs font-medium rounded-full 
                                   {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $course->is_published ? 'Publié' : 'Brouillon' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-900 mb-1 line-clamp-1">{{ $course->title }}</h3>
                        <p class="text-sm text-gray-500 mb-3">{{ $course->instructor->name }}</p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span><i class="fas fa-users mr-1"></i>{{ $course->students_count ?? 0 }}</span>
                            <span><i class="fas fa-book-open mr-1"></i>{{ $course->lessons_count ?? 0 }} leçons</span>
                            <span class="flex items-center text-yellow-400">★ {{ number_format($course->average_rating ?? 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-12 text-center">
                    <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Aucun cours</h3>
                    <p class="text-gray-500 mt-1">Commencez par créer votre premier cours</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if(isset($courses) && $courses->hasPages())
            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function courseManager() {
        return {
            openCreateModal() {
                // Logique d'ouverture du modal
            },
            editCourse(id) {
                window.location.href = `/admin/courses/${id}/edit`;
            },
            confirmDelete(id) {
                if (confirm('Supprimer ce cours ?')) {
                    // Logique de suppression
                }
            }
        }
    }
</script>
@endpush