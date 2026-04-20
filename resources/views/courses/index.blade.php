@extends('layouts.public')

@section('title', 'Catalogue des cours')

@section('content')
<div class="bg-gradient-to-br from-indigo-50 to-purple-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Explorez nos cours</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Développez vos compétences avec des cours créés par des experts
            </p>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <form action="{{ route('courses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}"
                           placeholder="Rechercher un cours..."
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select name="category" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                    <select name="level" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous les niveaux</option>
                        <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Débutant</option>
                        <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                        <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Avancé</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Résultats -->
        <div class="mb-4 text-gray-600">
            {{ $courses->total() }} cours trouvés
        </div>
        
        <!-- Grille des cours -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($courses as $course)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
                    <a href="{{ route('courses.show', $course) }}">
                        <div class="relative">
                            <img src="{{ $course->thumbnail_url }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Badge de niveau -->
                            <span class="absolute top-3 left-3 px-3 py-1 text-xs font-medium rounded-full 
                                       {{ $course->level === 'beginner' ? 'bg-green-100 text-green-800' : 
                                          ($course->level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($course->level) }}
                            </span>
                            
                            <!-- Badge de favori -->
                            @auth
                                <button class="bookmark-btn absolute top-3 right-3 p-2 bg-white rounded-full shadow-md hover:scale-110 transition-transform"
                                        data-course-id="{{ $course->id }}">
                                    <i class="far fa-bookmark text-gray-600"></i>
                                </button>
                            @endauth
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $course->category }}
                                </span>
                                <span class="mx-2">•</span>
                                <span><i class="far fa-clock mr-1"></i>{{ $course->formatted_duration }}</span>
                            </div>
                            
                            <h3 class="text-xl font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ $course->title }}
                            </h3>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ $course->short_description }}
                            </p>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <img src="{{ $course->instructor->avatar }}" 
                                         class="w-8 h-8 rounded-full mr-2">
                                    <span class="text-sm text-gray-600">{{ $course->instructor->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($course->average_rating))
                                                ★
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-500 ml-1">({{ $course->reviews_count }})</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-2xl font-bold text-gray-900">Gratuit</span>
                                <a href="{{ route('courses.show', $course) }}" 
                                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                    Voir le cours
                                </a>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-3 py-16 text-center">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Aucun cours trouvé</h3>
                    <p class="text-gray-500">Essayez d'ajuster vos filtres de recherche</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-12">
            {{ $courses->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des favoris
        document.querySelectorAll('.bookmark-btn').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const courseId = this.dataset.courseId;
                const icon = this.querySelector('i');
                
                try {
                    const response = await fetch(`/student/bookmark/${courseId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.bookmarked) {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-indigo-600');
                    } else {
                        icon.classList.remove('fas', 'text-indigo-600');
                        icon.classList.add('far');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });
        });
    });
</script>
@endpush