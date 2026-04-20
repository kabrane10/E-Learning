@extends('layouts.public')

@section('title', $course->title)

@push('styles')
<style>
    .curriculum-item:hover {
        background-color: #f9fafb;
    }
</style>
@endpush

@section('content')
<!-- Course Header -->
<div class="bg-gray-900 text-white">
    <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="lg:col-span-2">
                <div class="flex items-center text-sm text-gray-300 mb-4">
                    <a href="{{ route('courses.index') }}" class="hover:text-white">Cours</a>
                    <i class="fas fa-chevron-right mx-2 text-xs"></i>
                    <a href="{{ route('courses.index', ['category' => $course->category]) }}" class="hover:text-white">{{ $course->category }}</a>
                </div>
                <h1 class="text-3xl font-bold sm:text-4xl">{{ $course->title }}</h1>
                <p class="mt-4 text-lg text-gray-300">{{ $course->short_description }}</p>
                <div class="flex items-center mt-6 space-x-6">
                    <div class="flex items-center">
                        <span class="text-yellow-400 mr-1">★★★★★</span>
                        <span class="text-gray-300">{{ number_format($course->average_rating, 1) }} ({{ $course->reviews_count }} avis)</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user-graduate mr-2"></i>
                        <span>{{ $course->students_count }} étudiants inscrits</span>
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <img src="{{ $course->instructor->avatar }}" 
                         class="w-12 h-12 rounded-full mr-3">
                    <div>
                        <p class="font-medium">Créé par {{ $course->instructor->name }}</p>
                        <p class="text-sm text-gray-300">Dernière mise à jour : {{ $course->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 lg:mt-0">
                <div class="bg-white rounded-xl shadow-xl overflow-hidden sticky top-24">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                    <div class="p-6">
                        @if(!$isEnrolled)
                            <div class="text-center mb-4">
                                <span class="text-3xl font-bold text-gray-900">Gratuit</span>
                            </div>
                            <form action="{{ route('student.enroll', $course) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                    S'inscrire maintenant
                                </button>
                            </form>
                        @else
                            <a href="{{ route('student.learn', $course) }}" 
                               class="block w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition-colors text-center">
                                Continuer l'apprentissage
                            </a>
                        @endif
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-video w-6"></i>
                                <span>{{ $course->lessons->count() }} leçons au total</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock w-6"></i>
                                <span>{{ floor($course->lessons->sum('duration') / 60) }} heures de contenu</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-mobile-alt w-6"></i>
                                <span>Accessible sur mobile</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-certificate w-6"></i>
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
                <!-- Description -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                </div>
                
                <!-- Curriculum -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Contenu du cours</h2>
                    <div class="space-y-3">
                        @foreach($course->chapters as $chapter)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-3 font-medium text-gray-900">
                                    {{ $chapter->title }}
                                    <span class="ml-2 text-sm text-gray-500">({{ $chapter->lessons->count() }} leçons)</span>
                                </div>
                                <div class="divide-y divide-gray-200">
                                    @foreach($chapter->lessons as $lesson)
                                        <div class="curriculum-item flex items-center justify-between px-4 py-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-{{ $lesson->content_type === 'video' ? 'play-circle' : ($lesson->content_type === 'pdf' ? 'file-pdf' : 'puzzle-piece') }} text-gray-400 mr-3"></i>
                                                <span class="text-gray-700">{{ $lesson->title }}</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500">
                                                @if($lesson->is_free_preview)
                                                    <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs mr-2">Aperçu</span>
                                                @endif
                                                @if($lesson->duration)
                                                    <span>{{ floor($lesson->duration / 60) }}:{{ str_pad($lesson->duration % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
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
            
            <!-- Sidebar -->
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
@endsection