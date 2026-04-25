@extends('layouts.public')

@section('title', 'Tous les Badges')

@push('styles')
<style>
    .badge-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .badge-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.15);
    }
    .badge-card.earned {
        position: relative;
        overflow: hidden;
        border-color: #10b981;
    }
    .badge-card.earned::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 35px 35px 0;
        border-color: transparent #10b981 transparent transparent;
    }
    .badge-card.earned::before {
        content: '✓';
        position: absolute;
        top: 5px;
        right: 5px;
        color: white;
        font-size: 14px;
        font-weight: bold;
        z-index: 1;
    }
    .badge-card.locked {
        opacity: 0.6;
    }
    
    .filter-btn {
        transition: all 0.2s ease;
    }
    .filter-btn.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    
    .progress-ring {
        transition: stroke-dashoffset 0.5s ease;
    }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-slate-50 via-indigo-50/20 to-purple-50/20 min-h-screen py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- En-tête --}}
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('gamification.index') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour à la progression
                </a>
            </div>
            <h1 class="text-3xl lg:text-4xl font-black text-gray-900 flex items-center gap-3">
                <span class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-award text-white text-xl"></i>
                </span>
                Collection de Badges
            </h1>
            <p class="text-gray-600 mt-2 ml-16">Débloquez des badges en progressant dans votre apprentissage</p>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total Badges</p>
                <p class="text-3xl font-black text-gray-900 mt-1">{{ $badges->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Badges Obtenus</p>
                <p class="text-3xl font-black text-emerald-600 mt-1">{{ $badges->where('is_earned', true)->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">En Cours</p>
                <p class="text-3xl font-black text-amber-600 mt-1">{{ $badges->where('is_earned', false)->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Complétion</p>
                @php
                    $total = $badges->count();
                    $earned = $badges->where('is_earned', true)->count();
                    $pct = $total > 0 ? round(($earned / $total) * 100) : 0;
                @endphp
                <p class="text-3xl font-black text-indigo-600 mt-1">{{ $pct }}%</p>
            </div>
        </div>

        {{-- Filtres par catégorie --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <button onclick="filterBadges('all')" class="filter-btn active px-5 py-2.5 rounded-xl text-sm font-bold" data-filter="all">
                <i class="fas fa-th-large mr-2"></i>Tous
            </button>
            <button onclick="filterBadges('course')" class="filter-btn px-5 py-2.5 rounded-xl text-sm font-bold bg-white border border-gray-200 text-gray-600 hover:bg-gray-50" data-filter="course">
                <i class="fas fa-book-open mr-2"></i>Cours
            </button>
            <button onclick="filterBadges('quiz')" class="filter-btn px-5 py-2.5 rounded-xl text-sm font-bold bg-white border border-gray-200 text-gray-600 hover:bg-gray-50" data-filter="quiz">
                <i class="fas fa-puzzle-piece mr-2"></i>Quiz
            </button>
            <button onclick="filterBadges('activity')" class="filter-btn px-5 py-2.5 rounded-xl text-sm font-bold bg-white border border-gray-200 text-gray-600 hover:bg-gray-50" data-filter="activity">
                <i class="fas fa-fire mr-2"></i>Activité
            </button>
            <button onclick="filterBadges('special')" class="filter-btn px-5 py-2.5 rounded-xl text-sm font-bold bg-white border border-gray-200 text-gray-600 hover:bg-gray-50" data-filter="special">
                <i class="fas fa-star mr-2"></i>Spécial
            </button>
        </div>

        {{-- Grille des badges --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5" id="badges-grid">
            @forelse($badges as $badge)
                <div class="badge-card bg-white rounded-2xl shadow-sm border-2 border-gray-200 p-5 text-center {{ $badge->is_earned ? 'earned' : 'locked' }}"
                     data-category="{{ $badge->category }}">
                    
                    {{-- Icône du badge --}}
                    <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-{{ $badge->color }}-100 to-{{ $badge->color }}-200 flex items-center justify-center shadow-md relative">
                        <span class="text-4xl">{{ $badge->icon }}</span>
                        
                        {{-- Points bonus --}}
                        @if($badge->points_reward > 0)
                            <span class="absolute -bottom-2 -right-2 bg-{{ $badge->color }}-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-md">
                                +{{ $badge->points_reward }}
                            </span>
                        @endif
                    </div>
                    
                    {{-- Nom --}}
                    <h3 class="font-bold text-gray-900 text-sm mb-1.5">{{ $badge->name }}</h3>
                    
                    {{-- Description --}}
                    <p class="text-xs text-gray-500 line-clamp-2 mb-3 leading-relaxed">{{ $badge->description }}</p>
                    
                    {{-- Catégorie --}}
                    <span class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-3 block">
                        <i class="fas fa-tag mr-1"></i>{{ $badge->category }}
                    </span>
                    
                    {{-- Statut --}}
                    @if($badge->is_earned)
                        <div class="text-sm font-bold text-emerald-600 bg-emerald-50 py-2 px-4 rounded-full inline-flex items-center">
                            <i class="fas fa-check-circle mr-1.5"></i>Obtenu
                        </div>
                        @if($badge->earned_at)
                            <p class="text-xs text-gray-400 mt-2">{{ $badge->earned_at->format('d/m/Y') }}</p>
                        @endif
                    @else
                        <div class="mt-1">
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden mb-1.5">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2.5 rounded-full transition-all duration-500" 
                                     style="width: {{ $badge->progress['percentage'] ?? 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">
                                {{ $badge->progress['current'] ?? 0 }} / {{ $badge->progress['target'] ?? 1 }}
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-3xl flex items-center justify-center">
                        <i class="fas fa-medal text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucun badge disponible</h3>
                    <p class="text-gray-500">Les badges apparaîtront ici au fur et à mesure de votre progression.</p>
                    <a href="{{ route('gamification.index') }}" class="mt-6 inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour à la progression
                    </a>
                </div>
            @endforelse
        </div>
        
        {{-- Message si aucun résultat après filtre --}}
        <div id="no-results" class="hidden text-center py-16">
            <div class="w-20 h-20 mx-auto mb-5 bg-gray-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-filter text-gray-300 text-3xl"></i>
            </div>
            <p class="text-gray-500 font-medium text-lg">Aucun badge dans cette catégorie</p>
            <button onclick="filterBadges('all')" class="mt-4 text-indigo-600 hover:text-indigo-700 font-bold">
                <i class="fas fa-sync-alt mr-2"></i>Réinitialiser le filtre
            </button>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterBadges(category) {
        // Mettre à jour les boutons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-gradient-to-r', 'from-indigo-600', 'to-purple-600', 'text-white', 'shadow-md');
            btn.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
        });
        
        const activeBtn = document.querySelector(`[data-filter="${category}"]`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            activeBtn.classList.add('active', 'text-white');
        }
        
        // Filtrer les badges
        const badges = document.querySelectorAll('#badges-grid .badge-card');
        let visibleCount = 0;
        
        badges.forEach(badge => {
            if (category === 'all' || badge.dataset.category === category) {
                badge.style.display = '';
                visibleCount++;
            } else {
                badge.style.display = 'none';
            }
        });
        
        // Afficher/masquer le message "aucun résultat"
        const noResults = document.getElementById('no-results');
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }
</script>
@endpush