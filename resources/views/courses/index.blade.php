@extends('layouts.public')

@section('title', 'Catalogue des cours')

@push('styles')
<style>
    .course-card {
        transition: all 0.3s ease;
    }
    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
    }
    .course-card:hover .course-img {
        transform: scale(1.05);
    }
    .course-img {
        transition: transform 0.5s ease;
    }
    .level-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
    .level-beginner { background: #d1fae5; color: #065f46; }
    .level-intermediate { background: #fef3c7; color: #92400e; }
    .level-advanced { background: #fee2e2; color: #991b1b; }
    .price-tag { font-size: 1.25rem; font-weight: 700; }
    .free-tag { color: #059669; }
    .paid-tag { color: #d97706; }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- En-tête --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-3">
                Explorez nos <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">cours</span>
            </h1>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                Développez vos compétences avec des cours créés par des experts passionnés
            </p>
        </div>
        
        {{-- Filtres --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-8">
            <form action="{{ route('courses.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Recherche</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Mot-clé..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Catégorie</label>
                    <select name="category" class="w-full py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Toutes</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Niveau</label>
                    <select name="level" class="w-full py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Tous</option>
                        <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Débutant</option>
                        <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                        <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Avancé</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors text-sm font-medium">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                    @if(request()->anyFilled(['q', 'category', 'level']))
                        <a href="{{ route('courses.index') }}" class="px-4 py-2.5 text-gray-500 hover:text-gray-700 border border-gray-300 rounded-xl text-sm">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        {{-- Résultats --}}
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500">{{ $courses->total() }} cours trouvé(s)</p>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">Trier par :</span>
                <select class="text-sm border-gray-200 rounded-lg py-1.5 focus:ring-indigo-500">
                    <option>Plus récents</option>
                    <option>Plus populaires</option>
                    <option>Mieux notés</option>
                </select>
            </div>
        </div>
        
        {{-- Grille des cours --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="course-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
                    <a href="{{ route('courses.show', $course) }}" class="block">
                        {{-- Image --}}
                        <div class="relative overflow-hidden h-48">
                            <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=500' }}" 
                                 alt="{{ $course->title }}" 
                                 class="course-img w-full h-full object-cover">
                            
                            {{-- ✅ Badge Niveau (top left) --}}
                            <span class="absolute top-3 left-3 level-badge 
                                       {{ $course->level === 'beginner' ? 'level-beginner' : ($course->level === 'intermediate' ? 'level-intermediate' : 'level-advanced') }}">
                                <i class="fas fa-signal text-xs"></i>
                                {{ $course->level === 'beginner' ? 'Débutant' : ($course->level === 'intermediate' ? 'Intermédiaire' : 'Avancé') }}
                            </span>
                            
                            {{-- ✅ Badge Prix (top right) --}}
                            @if($course->is_free)
                                <span class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-xs font-semibold text-green-600 shadow-sm">
                                    <i class="fas fa-gift"></i> Gratuit
                                </span>
                            @else
                                <span class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-xs font-semibold text-amber-600 shadow-sm">
                                    {{ number_format($course->price, 0, ',', ' ') }} FCFA
                                </span>
                            @endif
                            
                            {{-- Favori --}}
                            @auth
                                <button class="bookmark-btn absolute bottom-3 right-3 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-transform"
                                        data-course-id="{{ $course->id }}" onclick="event.preventDefault(); event.stopPropagation();">
                                    <i class="far fa-bookmark text-gray-600"></i>
                                </button>
                            @endauth
                        </div>
                        
                        {{-- Contenu --}}
                        <div class="p-5">
                            {{-- Catégorie --}}
                            <span class="inline-block px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium mb-3">
                                {{ $course->category }}
                            </span>
                            
                            {{-- Titre --}}
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ $course->title }}
                                
                            </h3>
                            
                            {{-- Description --}}
                            <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                                {{ $course->short_description }}
                            </p>
                            
                            {{-- Formateur + Note --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $course->instructor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) }}" 
                                         class="w-7 h-7 rounded-full">
                                    <span class="text-xs text-gray-600">{{ $course->instructor->name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    <span class="text-xs font-medium text-gray-700">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                    <span class="text-xs text-gray-400">({{ $course->reviews_count ?? 0 }})</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-3 py-20 text-center">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-search text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun cours trouvé</h3>
                    <p class="text-gray-500 mb-6">Essayez d'ajuster vos filtres ou revenez plus tard</p>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                        <i class="fas fa-redo-alt mr-2"></i>Réinitialiser les filtres
                    </a>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($courses->hasPages())
            <div class="mt-10">
                {{ $courses->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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