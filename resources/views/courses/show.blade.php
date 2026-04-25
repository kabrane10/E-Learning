@extends('layouts.public')

@section('title', $course->title)

@push('styles')
<style>
    .curriculum-item:hover { background-color: #f9fafb; cursor: pointer; }
    
    /* Badges sur l'image */
    .badge-level {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        backdrop-filter: blur(4px);
    }
    .badge-level.beginner { background: rgba(16,185,129,0.9); color: white; }
    .badge-level.intermediate { background: rgba(245,158,11,0.9); color: white; }
    .badge-level.advanced { background: rgba(239,68,68,0.9); color: white; }
    
    .badge-price {
        position: absolute;
        bottom: 12px;
        right: 12px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }
    .badge-price.free { background: rgba(16,185,129,0.9); color: white; }
    .badge-price.paid { background: rgba(255,255,255,0.95); color: #1f2937; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    
    /* Ressources */
    .resource-link {
        transition: all 0.2s;
    }
    .resource-link:hover {
        background: #eef2ff;
        border-color: #6366f1;
        transform: translateX(4px);
    }
    
    /* Lecteur PDF */
    .pdf-viewer {
        width: 100%;
        height: 500px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }
    
    /* Vidéo */
    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
    }
    .video-wrapper video {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
    }
</style>
@endpush

@section('content')
<!-- Course Header -->
<div class="bg-gray-900 text-white">
    <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="lg:col-span-2">
                {{-- Fil d'Ariane --}}
                <div class="flex items-center text-sm text-gray-300 mb-4">
                    <a href="{{ route('courses.index') }}" class="hover:text-white">Cours</a>
                    <i class="fas fa-chevron-right mx-2 text-xs"></i>
                    <span class="text-white">{{ $course->category }}</span>
                </div>
                
                <h1 class="text-3xl font-bold sm:text-4xl">{{ $course->title }}</h1>
                <p class="mt-4 text-lg text-gray-300">{{ $course->short_description }}</p>
                
                {{-- Note et étudiants --}}
                <div class="flex items-center mt-6 space-x-6">
                    <div class="flex items-center">
                        <span class="text-yellow-400 mr-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($course->reviews_avg_rating ?? 0)) ★ @else ☆ @endif
                            @endfor
                        </span>
                        <span class="text-gray-300">{{ number_format($course->reviews_avg_rating ?? 0, 1) }} ({{ $course->reviews_count ?? 0 }} avis)</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user-graduate mr-2"></i>
                        <span>{{ $course->students_count ?? 0 }} étudiants inscrits</span>
                    </div>
                </div>
                
                {{-- Formateur --}}
                <div class="flex items-center mt-4">
                    <img src="{{ $course->instructor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) }}" 
                         class="w-12 h-12 rounded-full mr-3">
                    <div>
                        <p class="font-medium">Créé par {{ $course->instructor->name }}</p>
                        <p class="text-sm text-gray-300">Dernière mise à jour : {{ $course->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            
            {{-- ✅ Carte latérale avec badges sur l'image --}}
            <div class="mt-8 lg:mt-0">
                <div class="bg-white rounded-xl shadow-xl overflow-hidden sticky top-24">
                    
                    {{-- ✅ IMAGE AVEC BADGES --}}
                    <div class="relative">
                        <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=400' }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-48 object-cover">
                        
                        {{-- ✅ Badge Niveau (haut à gauche) --}}
                        <span class="badge-level {{ $course->level }}">
                            <i class="fas fa-signal mr-1"></i>
                            {{ $course->level === 'beginner' ? 'Débutant' : ($course->level === 'intermediate' ? 'Intermédiaire' : 'Avancé') }}
                        </span>
                        
                        {{-- ✅ Badge Prix/Gratuit (bas à droite) --}}
                        @if($course->is_free)
                            <span class="badge-price free">
                                <i class="fas fa-gift mr-1"></i>Gratuit
                            </span>
                        @else
                            <span class="badge-price paid">
                                {{ number_format($course->price, 0, ',', ' ') }} FCFA
                            </span>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        {{-- Bouton d'action --}}
                        @auth
                            @if($isEnrolled)
                                <a href="{{ route('student.learn', $course) }}" 
                                   class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3.5 rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition-colors text-center shadow-md">
                                    <i class="fas fa-play-circle mr-2"></i>Continuer l'apprentissage
                                </a>
                            @else
                                <form action="{{ route('student.enroll', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3.5 rounded-xl font-bold hover:from-indigo-700 hover:to-purple-700 transition-colors shadow-md">
                                        @if($course->is_free)
                                            <i class="fas fa-user-plus mr-2"></i>S'inscrire gratuitement
                                        @else
                                            <i class="fas fa-shopping-cart mr-2"></i>S'inscrire pour {{ number_format($course->price, 0, ',', ' ') }} FCFA
                                        @endif
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="block w-full bg-indigo-600 text-white py-3.5 rounded-xl font-bold hover:bg-indigo-700 transition-colors text-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>Se connecter pour s'inscrire
                            </a>
                        @endauth
                        
                        {{-- Infos --}}
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-book-open w-6 text-indigo-500"></i>
                                <span>{{ $course->lessons->count() }} leçons</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock w-6 text-indigo-500"></i>
                                <span>{{ $course->lessons->sum('duration') > 0 ? floor($course->lessons->sum('duration') / 3600) . 'h de contenu' : '0h' }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-mobile-alt w-6 text-indigo-500"></i>
                                <span>Accessible sur mobile</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-certificate w-6 text-indigo-500"></i>
                                <span>Certificat de réussite</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenu du cours -->
<div class="py-12 bg-gray-50">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="lg:col-span-2">
                
                {{-- ✅ Objectifs d'apprentissage --}}
                @if(!empty($course->learning_outcomes) && count(array_filter($course->learning_outcomes)) > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Ce que vous apprendrez</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($course->learning_outcomes as $outcome)
                                @if(!empty(trim($outcome)))
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                        <span class="text-gray-700">{{ $outcome }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                
                {{-- ✅ Prérequis --}}
                @if(!empty($course->prerequisites) && count(array_filter($course->prerequisites)) > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Prérequis</h2>
                        <ul class="space-y-2">
                            @foreach($course->prerequisites as $prereq)
                                @if(!empty(trim($prereq)))
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-circle text-xs text-indigo-500 mt-1.5"></i>
                                        <span class="text-gray-700">{{ $prereq }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Description --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-700">
                        @if($course->description)
                            {!! nl2br(e($course->description)) !!}
                        @else
                            <p class="text-gray-400 italic">Aucune description pour ce cours.</p>
                        @endif
                    </div>
                </div>
                
                {{-- Public cible --}}
                @if($course->target_audience)
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">À qui s'adresse ce cours ?</h2>
                        <p class="text-gray-700">{{ $course->target_audience }}</p>
                    </div>
                @endif
                
                {{-- ✅ CURRICULUM --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Contenu du cours</h2>
                    
                    @if($course->chapters->count() > 0)
                        <div class="space-y-3" x-data="{ expandedChapter: null }">
                            @foreach($course->chapters as $chapter)
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <button @click="expandedChapter = expandedChapter === {{ $chapter->id }} ? null : {{ $chapter->id }}"
                                            class="w-full bg-gray-50 px-4 py-3 font-medium text-gray-900 flex items-center justify-between hover:bg-gray-100 transition-colors">
                                        <span>{{ $chapter->title }}</span>
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm text-gray-500">({{ $chapter->lessons->count() }} leçons)</span>
                                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" 
                                               :class="{ 'rotate-180': expandedChapter === {{ $chapter->id }} }"></i>
                                        </div>
                                    </button>
                                    <div x-show="expandedChapter === {{ $chapter->id }}" x-collapse>
                                        <div class="divide-y divide-gray-200">
                                            @foreach($chapter->lessons as $lesson)
                                                <div class="curriculum-item flex items-center justify-between px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        @if($lesson->type === 'video')
                                                            <i class="fas fa-play-circle text-blue-500"></i>
                                                        @elseif($lesson->type === 'pdf')
                                                            <i class="fas fa-file-pdf text-red-500"></i>
                                                        @elseif($lesson->type === 'quiz')
                                                            <i class="fas fa-puzzle-piece text-purple-500"></i>
                                                        @else
                                                            <i class="fas fa-file-alt text-gray-500"></i>
                                                        @endif
                                                        <span class="text-gray-700">{{ $lesson->title }}</span>
                                                    </div>
                                                    <div class="flex items-center text-sm text-gray-500 gap-3">
                                                        @if($lesson->is_free_preview)
                                                            <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-medium">Aperçu</span>
                                                        @endif
                                                        @if($lesson->duration)
                                                            <span><i class="far fa-clock mr-1"></i>{{ gmdate('i:s', $lesson->duration) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-book-open text-4xl mb-3 opacity-30"></i>
                            <p>Le contenu est en cours de préparation.</p>
                        </div>
                    @endif
                </div>
                
                {{-- ✅ VIDÉO DE PRÉSENTATION (si disponible) --}}
                @if($course->getFirstMediaUrl('promo_video'))
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">🎬 Vidéo de présentation</h2>
                        <div class="video-wrapper">
                            <video controls controlsList="nodownload">
                                <source src="{{ $course->getFirstMediaUrl('promo_video') }}" type="video/mp4">
                                Votre navigateur ne supporte pas la lecture vidéo.
                            </video>
                        </div>
                    </div>
                @endif
                
                {{-- ✅ RESSOURCES TÉLÉCHARGEABLES --}}
                @php $courseResources = $course->getMedia('resources'); @endphp
                @if($courseResources->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">📎 Ressources téléchargeables</h2>
                        <p class="text-sm text-gray-500 mb-4">Documents, PDF et fichiers mis à disposition par le formateur</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($courseResources as $resource)
                                @php
                                    $isPdf = str_contains(strtolower($resource->file_name), '.pdf');
                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $resource->file_name);
                                @endphp
                                <div class="resource-link flex items-center gap-3 p-4 border border-gray-200 rounded-xl group">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                                        @if($isPdf)
                                            <i class="fas fa-file-pdf text-red-500"></i>
                                        @elseif($isImage)
                                            <i class="fas fa-file-image text-blue-500"></i>
                                        @else
                                            <i class="fas fa-file-alt text-indigo-500"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $resource->file_name }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($resource->size / 1024, 1) }} KB</p>
                                    </div>
                                    <div class="flex gap-2">
                                        @if($isPdf)
                                            {{-- ✅ Bouton Lire en ligne pour les PDF --}}
                                            <button onclick="openPdfViewer('{{ $resource->getUrl() }}', '{{ $resource->file_name }}')"
                                                    class="px-3 py-1.5 text-xs bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-eye mr-1"></i>Lire
                                            </button>
                                        @endif
                                        {{-- ✅ Bouton Télécharger --}}
                                        <a href="{{ $resource->getUrl() }}" 
                                           download
                                           class="px-3 py-1.5 text-xs bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors">
                                            <i class="fas fa-download mr-1"></i>Télécharger
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Section Forum du cours -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-900">Discussions du cours</h2>
                        <a href="{{ route('forum.categories.show', ['category' => 'general']) }}?course={{ $course->id }}" 
                           class="text-indigo-600 hover:text-indigo-700 text-sm">
                            Voir toutes les discussions <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    @php
                        $courseTopics = \App\Models\ForumTopic::where('course_id', $course->id)
                            ->with(['user', 'lastPostUser'])
                            ->orderBy('last_post_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    
                    @if($courseTopics->count() > 0)
                        <div class="space-y-4 mb-6">
                            @foreach($courseTopics as $topic)
                                <a href="{{ $topic->url }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $topic->title }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Par {{ $topic->user->name }} • {{ $topic->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            <i class="far fa-comment mr-1"></i>{{ $topic->posts_count }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-6">Aucune discussion pour le moment. Soyez le premier à poser une question !</p>
                    @endif
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('forum.topics.create', ['course_id' => $course->id, 'category_id' => 1]) }}" 
                           class="flex-1 py-3 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-center">
                            <i class="fas fa-question-circle mr-2"></i>Poser une question
                        </a>
                        <a href="{{ route('forum.index') }}" 
                           class="flex-1 py-3 px-4 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center">
                            <i class="fas fa-comments mr-2"></i>Voir le forum
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Sidebar --}}
            <div class="mt-8 lg:mt-0 space-y-8">
                <!-- Formateur -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">À propos du formateur</h3>
                    <div class="flex items-center mb-4">
                        <img src="{{ $course->instructor->avatar }}" 
                             class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $course->instructor->name }}</p>
                            <p class="text-sm text-gray-500">Formateur expert</p>
                        </div>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 space-x-4">
                        <div>
                            <i class="fas fa-star text-yellow-400"></i>
                            <span class="ml-1">4.8 note</span>
                        </div>
                        <div>
                            <i class="fas fa-user-graduate"></i>
                            <span class="ml-1">{{ $course->instructor->taughtCourses->count() }} cours</span>
                        </div>
                    </div>
                    
                    <!-- Contacter le formateur -->
                    @auth
                        @if(auth()->id() !== $course->instructor_id)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('chat.index') }}" 
                                   class="block w-full py-2 text-center text-indigo-600 hover:text-indigo-700 text-sm">
                                    <i class="fas fa-comment-dots mr-1"></i>Contacter le formateur
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
                
                <!-- Cours similaires -->
                @if($relatedCourses->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cours similaires</h3>
                        <div class="space-y-4">
                            @foreach($relatedCourses as $related)
                                <a href="{{ route('courses.show', $related) }}" class="flex items-start space-x-3 group">
                                    <img src="{{ $related->thumbnail_url }}" class="w-16 h-16 rounded object-cover">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 line-clamp-2">
                                            {{ $related->title }}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1">{{ $related->lessons_count }} leçons</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ✅ MODAL LECTEUR PDF --}}
<div id="pdfViewerModal" class="fixed inset-0 z-50 hidden bg-black/60 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col">
            {{-- En-tête --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-file-pdf text-red-500"></i>
                    <span id="pdfViewerTitle">Document PDF</span>
                </h3>
                <div class="flex items-center gap-2">
                    <a id="pdfDownloadBtn" href="#" download class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                        <i class="fas fa-download mr-1"></i>Télécharger
                    </a>
                    <button onclick="closePdfViewer()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            {{-- Contenu PDF --}}
            <div class="flex-1 p-2">
                <iframe id="pdfViewerFrame" src="" class="w-full h-full rounded-lg" style="min-height: 70vh;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openPdfViewer(url, title) {
        document.getElementById('pdfViewerFrame').src = url;
        document.getElementById('pdfViewerTitle').textContent = title;
        document.getElementById('pdfDownloadBtn').href = url;
        document.getElementById('pdfViewerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closePdfViewer() {
        document.getElementById('pdfViewerFrame').src = '';
        document.getElementById('pdfViewerModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePdfViewer();
    });
    
    // Fermer en cliquant sur le fond
    document.getElementById('pdfViewerModal').addEventListener('click', function(e) {
        if (e.target === this) closePdfViewer();
    });
</script>
@endpush
