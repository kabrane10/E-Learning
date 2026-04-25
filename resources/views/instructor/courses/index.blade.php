@extends('layouts.instructor')

@section('title', 'Mes Cours')
@section('page-title', 'Mes Cours')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Mes Cours</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="coursesManager()" x-init="init()">
    
    <!-- En-tête avec actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Gérez vos cours</h2>
            <p class="text-gray-500 text-sm mt-1">{{ $courses->total() ?? 0 }} cours au total</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('instructor.courses.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>
                Nouveau cours
            </a>
            <button @click="exportCourses" 
                    class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Exporter
            </button>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('instructor.courses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Rechercher un cours..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select name="level" class="w-full py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les niveaux</option>
                    <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Débutant</option>
                    <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                    <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Avancé</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publiés</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillons</option>
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                <a href="{{ route('instructor.courses.index') }}" class="ml-2 px-4 py-2 text-gray-600 hover:text-gray-900 text-sm">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des cours (Grille) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @forelse($courses as $index => $course)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 animate-fade-in"
                 style="animation-delay: {{ $index * 50 }}ms">
                
                <!-- Image du cours -->
                <div class="relative">
                    <a href="{{ route('instructor.courses.show', $course) }}">
                        <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=400' }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-44 object-cover">
                    </a>
                    
                    <!-- Badge Statut -->
                    <span class="absolute top-3 left-3 px-3 py-1.5 text-xs font-medium rounded-full shadow-sm
                               {{ $course->is_published ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                        {{ $course->is_published ? 'Publié' : 'Brouillon' }}
                    </span>
                    
                    <!-- Badge Niveau -->
                    <span class="absolute top-3 right-3 px-2.5 py-1 text-xs font-medium rounded-full bg-black/50 backdrop-blur-sm text-white">
                        {{ $course->level === 'beginner' ? 'Débutant' : ($course->level === 'intermediate' ? 'Intermédiaire' : 'Avancé') }}
                    </span>
                    
                    <!-- Badge Prix -->
                    @if(!$course->is_free)
                        <span class="absolute bottom-3 right-3 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-500 text-white shadow-sm">
                            {{ number_format($course->price, 2) }} €
                        </span>
                    @else
                        <span class="absolute bottom-3 right-3 px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-500 text-white shadow-sm">
                            Gratuit
                        </span>
                    @endif
                </div>
                
                <!-- Contenu -->
                <div class="p-5">
                    <!-- Catégorie -->
                    <div class="mb-2">
                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
                            {{ $course->category ?? 'Non catégorisé' }}
                        </span>
                    </div>
                    
                    <!-- Titre -->
                    <a href="{{ route('instructor.courses.show', $course) }}" class="block group">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                            {{ $course->title }}
                        </h3>
                    </a>
                    
                    <!-- Progression (pour les brouillons) -->
                    @if(!$course->is_published)
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-500">Progression</span>
                                <span class="font-medium text-gray-700">{{ $course->completion_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $course->completion_percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Statistiques -->
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ number_format($course->students_count ?? 0) }}</p>
                            <p class="text-xs text-gray-500">Étudiants</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $course->lessons_count ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Leçons</p>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-star text-yellow-400 text-sm mr-0.5"></i>
                                <span class="text-lg font-bold text-gray-900">{{ number_format($course->average_rating ?? 0, 1) ?: '-' }}</span>
                            </div>
                            <p class="text-xs text-gray-500">({{ $course->reviews_count ?? 0 }})</p>
                        </div>
                    </div>
                    
                    <!-- Taux de complétion (si publié) -->
                    @if($course->is_published && ($course->students_count ?? 0) > 0)
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-500">Taux de complétion</span>
                                <span class="font-medium text-gray-700">{{ $course->completion_rate ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $course->completion_rate ?? 0 }}%"></div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-400">
                            <i class="far fa-calendar mr-1"></i>{{ $course->updated_at->format('d/m/Y') }}
                        </span>
                        
                        <div class="flex items-center gap-2">
                            {{-- Voir le cours (public) --}}
                            @if($course->is_published)
                                <a href="{{ route('courses.show', $course->slug) }}" 
                                   target="_blank"
                                   class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors"
                                   title="Voir la page publique">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                            
                            {{-- Modifier --}}
                            <a href="{{ route('instructor.courses.edit', $course) }}" 
                               class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-gray-100 transition-colors"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            {{-- Menu déroulant --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     x-cloak
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50">
                                    
                                    <a href="{{ route('instructor.courses.analytics', $course) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                                    </a>
                                    
                                    <a href="{{ route('instructor.courses.students', $course) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-users mr-2"></i>Étudiants
                                    </a>
                                    
                                    <a href="{{ route('instructor.courses.reviews', $course) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-star mr-2"></i>Avis
                                    </a>
                                    
                                    <form action="{{ route('instructor.courses.toggle-publish', $course) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full text-left px-4 py-2 text-sm {{ $course->is_published ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50' }}">
                                            <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-check-circle' }} mr-2"></i>
                                            {{ $course->is_published ? 'Dépublier' : 'Publier' }}
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('instructor.courses.duplicate', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-copy mr-2"></i>Dupliquer
                                        </button>
                                    </form>
                                    
                                    <hr class="my-1">
                                    
                                    <button @click="confirmDelete({{ $course->id }})" 
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash mr-2"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-16">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-book-open text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun cours trouvé</h3>
                <p class="text-gray-500 mb-6">
                    @if(request()->has('search') || request()->has('level') || request()->has('status'))
                        Aucun cours ne correspond à vos critères de recherche.
                    @else
                        Commencez par créer votre premier cours !
                    @endif
                </p>
                <a href="{{ route('instructor.courses.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Créer un cours
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
        <div class="mt-8">
            {{ $courses->withQueryString()->links() }}
        </div>
    @endif

    <!-- Modal de confirmation de suppression -->
    <div x-show="deleteModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p class="text-gray-500 mb-6">
                        Êtes-vous sûr de vouloir supprimer ce cours ?<br>
                        <span class="text-red-600 font-medium">Cette action est irréversible.</span>
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Supprimer
                            </button>
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
    function coursesManager() {
        return {
            deleteModalOpen: false,
            courseToDelete: null,
            
            init() {
                console.log('Gestionnaire de cours initialisé');
            },
            
            confirmDelete(id) {
                this.courseToDelete = id;
                const form = document.getElementById('deleteForm');
                form.action = `/instructor/courses/${id}`;
                this.deleteModalOpen = true;
            },
            
            exportCourses() {
                window.location.href = '{{ route("instructor.courses.index") }}?export=1';
            },
        }
    }
</script>
@endpush