@extends('layouts.public')

@section('title', $lesson->title . ' - ' . $course->title)

@push('styles')
<style>
    .curriculum-sidebar {
        max-height: calc(100vh - 80px);
        overflow-y: auto;
    }
    
    .lesson-item {
        transition: all 0.2s ease;
    }
    
    .lesson-item:hover {
        background-color: #f3f4f6;
    }
    
    .lesson-item.active {
        background-color: #e0e7ff;
        border-left: 3px solid #4f46e5;
    }
    
    .lesson-item.completed {
        opacity: 0.8;
    }
    
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        height: 0;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen bg-gray-900 overflow-hidden">
    <!-- Sidebar Curriculum -->
    <div class="hidden lg:block lg:w-96 bg-white border-r border-gray-200 curriculum-sidebar">
        <div class="p-4 border-b border-gray-200">
            <a href="{{ route('student.my-courses') }}" class="text-gray-500 hover:text-gray-700 mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux cours
            </a>
            <h2 class="font-semibold text-gray-900 line-clamp-2">{{ $course->title }}</h2>
            
            <!-- Progression globale -->
            <div class="mt-3">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progression globale</span>
                    <span>{{ $enrollment->progress_percentage }}%</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-600 rounded-full" 
                         style="width: {{ $enrollment->progress_percentage }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="p-4">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Curriculum</h3>
            
            <div class="space-y-3">
                @foreach($course->chapters as $chapter)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ $chapter->title }}</h4>
                            <span class="text-xs text-gray-400">{{ $chapter->lessons->count() }}</span>
                        </div>
                        
                        <div class="space-y-1">
                            @foreach($chapter->lessons as $curriculumLesson)
                                @php
                                    $isCurrentLesson = $curriculumLesson->id === $lesson->id;
                                    $isLessonCompleted = in_array($curriculumLesson->id, $completedLessons);
                                @endphp
                                
                                <a href="{{ route('student.learn.lesson', [$course, $curriculumLesson]) }}" 
                                   class="lesson-item flex items-start p-2 rounded-lg {{ $isCurrentLesson ? 'active' : '' }} {{ $isLessonCompleted ? 'completed' : '' }}">
                                    <div class="flex-shrink-0 mt-0.5">
                                        @if($isLessonCompleted)
                                            <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                        @else
                                            <i class="fas fa-{{ $curriculumLesson->content_type === 'video' ? 'play-circle' : ($curriculumLesson->content_type === 'pdf' ? 'file-pdf' : 'puzzle-piece') }} text-gray-400 text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-sm {{ $isCurrentLesson ? 'text-indigo-600 font-medium' : 'text-gray-700' }} truncate">
                                            {{ $curriculumLesson->title }}
                                        </p>
                                        @if($curriculumLesson->duration)
                                            <p class="text-xs text-gray-400">{{ floor($curriculumLesson->duration / 60) }}:{{ str_pad($curriculumLesson->duration % 60, 2, '0', STR_PAD_LEFT) }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Zone de contenu principal -->
    <div class="flex-1 overflow-y-auto">
        <div class="max-w-5xl mx-auto px-4 py-6 lg:py-8">
            <!-- Lecteur vidéo -->
            @if($lesson->content_type === 'video' && $lesson->video_path)
                <div class="video-container mb-6">
                    <video id="lesson-video" 
                           controls 
                           controlsList="nodownload"
                           data-lesson-id="{{ $lesson->id }}"
                           data-course-id="{{ $course->id }}">
                        <source src="{{ Storage::url($lesson->video_path) }}" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>
                </div>
            @elseif($lesson->content_type === 'pdf' && $lesson->pdf_path)
                <div class="bg-white rounded-xl p-6 mb-6">
                    <embed src="{{ Storage::url($lesson->pdf_path) }}" 
                           type="application/pdf" 
                           class="w-full h-[70vh] rounded-lg">
                </div>
            @else
                <div class="bg-white rounded-xl p-8 text-center mb-6">
                    <i class="fas fa-file-alt text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Contenu en cours de préparation</p>
                </div>
            @endif
            
            <!-- Informations de la leçon -->
            <div class="bg-white rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $lesson->title }}</h1>
                    
                    <button id="mark-complete-btn" 
                            data-lesson-id="{{ $lesson->id }}"
                            data-course-id="{{ $course->id }}"
                            class="px-4 py-2 {{ $isCompleted ? 'bg-green-100 text-green-700' : 'bg-indigo-600 text-white hover:bg-indigo-700' }} rounded-lg font-medium transition-colors">
                        @if($isCompleted)
                            <i class="fas fa-check-circle mr-2"></i>Terminé
                        @else
                            Marquer comme terminé
                        @endif
                    </button>
                </div>
                
                <!-- Navigation -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div>
                        @if($previousLesson)
                            <a href="{{ route('student.learn.lesson', [$course, $previousLesson]) }}" 
                               class="text-indigo-600 hover:text-indigo-700">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Précédent
                            </a>
                        @endif
                    </div>
                    <div>
                        @if($nextLesson)
                            <a href="{{ route('student.learn.lesson', [$course, $nextLesson]) }}" 
                               class="text-indigo-600 hover:text-indigo-700">
                                Suivant
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Message de félicitations (caché par défaut) -->
            <div id="congratulations-message" class="hidden bg-green-50 border border-green-200 rounded-xl p-6 mt-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-trophy text-3xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-green-900">Félicitations ! 🎉</h3>
                        <p class="text-green-700">Vous avez terminé ce cours avec succès !</p>
                        <a href="{{ route('student.my-courses') }}" class="mt-2 inline-block text-sm font-medium text-green-600 hover:text-green-700">
                            Voir tous mes cours <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton mobile pour ouvrir le curriculum -->
<div class="lg:hidden fixed bottom-4 right-4 z-50">
    <button id="mobile-curriculum-btn" 
            class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700">
        <i class="fas fa-list"></i>
    </button>
</div>

<!-- Modal curriculum mobile -->
<div id="mobile-curriculum-modal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50">
    <div class="absolute right-0 top-0 h-full w-80 bg-white overflow-y-auto">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-semibold">Curriculum</h3>
            <button id="close-mobile-curriculum" class="text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" id="mobile-curriculum-content">
            <!-- Chargé dynamiquement -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('lesson-video');
        const markCompleteBtn = document.getElementById('mark-complete-btn');
        const congratsMessage = document.getElementById('congratulations-message');
        
        // Gestion du marquage comme terminé
        if (markCompleteBtn) {
            markCompleteBtn.addEventListener('click', async function() {
                const lessonId = this.dataset.lessonId;
                const courseId = this.dataset.courseId;
                
                // Si c'est une vidéo, envoyer la durée regardée
                let watchedDuration = 0;
                if (video) {
                    watchedDuration = Math.floor(video.currentTime);
                }
                
                try {
                    const response = await fetch(`/student/progress/${courseId}/${lessonId}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ watched_duration: watchedDuration })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Mettre à jour le bouton
                        this.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                        this.classList.add('bg-green-100', 'text-green-700');
                        this.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Terminé';
                        
                        // Mettre à jour la progression dans la sidebar
                        updateProgressBar(data.progress);
                        
                        // Afficher message de félicitations si cours terminé
                        if (data.is_completed) {
                            congratsMessage.classList.remove('hidden');
                            
                            // Animation de confettis simple
                            createConfetti();
                        }
                        
                        // Rediriger vers la prochaine leçon après 2 secondes
                        @if($nextLesson)
                            setTimeout(() => {
                                window.location.href = '{{ route('student.learn.lesson', [$course, $nextLesson]) }}';
                            }, 2000);
                        @endif
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });
        }
        
        // Sauvegarde automatique de la progression vidéo
        if (video) {
            let lastSavedTime = 0;
            
            video.addEventListener('timeupdate', function() {
                const currentTime = Math.floor(this.currentTime);
                
                // Sauvegarder toutes les 30 secondes
                if (currentTime - lastSavedTime >= 30) {
                    saveVideoProgress(this.dataset.courseId, this.dataset.lessonId, currentTime);
                    lastSavedTime = currentTime;
                }
            });
            
            // Reprendre là où l'utilisateur s'est arrêté
            const savedTime = localStorage.getItem(`video_${video.dataset.lessonId}`);
            if (savedTime) {
                video.currentTime = parseFloat(savedTime);
            }
        }
        
        async function saveVideoProgress(courseId, lessonId, currentTime) {
            localStorage.setItem(`video_${lessonId}`, currentTime);
            
            try {
                await fetch(`/student/progress/${courseId}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        lesson_id: lessonId,
                        watched_duration: currentTime 
                    })
                });
            } catch (error) {
                console.error('Erreur de sauvegarde:', error);
            }
        }
        
        function updateProgressBar(progress) {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                bar.style.width = progress + '%';
            });
            
            const progressTexts = document.querySelectorAll('.progress-text');
            progressTexts.forEach(text => {
                text.textContent = progress + '%';
            });
        }
        
        function createConfetti() {
            // Animation simple de confettis
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'fixed w-2 h-2 bg-indigo-500 rounded-full pointer-events-none z-50';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = '-10px';
                confetti.style.animation = `confetti-fall ${1 + Math.random() * 2}s linear forwards`;
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 3000);
            }
        }
        
        // Ajouter l'animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes confetti-fall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Gestion du menu mobile
        const mobileBtn = document.getElementById('mobile-curriculum-btn');
        const mobileModal = document.getElementById('mobile-curriculum-modal');
        const closeBtn = document.getElementById('close-mobile-curriculum');
        
        if (mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                mobileModal.classList.remove('hidden');
                // Copier le contenu du curriculum dans le modal
                const sidebarContent = document.querySelector('.curriculum-sidebar .p-4:last-child');
                document.getElementById('mobile-curriculum-content').innerHTML = sidebarContent.innerHTML;
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                mobileModal.classList.add('hidden');
            });
        }
        
        mobileModal.addEventListener('click', (e) => {
            if (e.target === mobileModal) {
                mobileModal.classList.add('hidden');
            }
        });
    });
</script>
@endpush