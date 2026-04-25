@extends('student.layouts.learning')

@section('title', $lesson->title . ' - ' . $course->title)

@section('sidebar')
{{-- Sidebar curriculum (inchangée) --}}
<div class="p-4 border-b border-gray-200">
    <a href="{{ route('student.learn', $course) }}" class="text-sm text-indigo-600 hover:text-indigo-700 mb-3 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Vue d'ensemble
    </a>
    <h2 class="font-semibold text-gray-900 line-clamp-2">{{ $course->title }}</h2>
    
    <div class="mt-3">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span>Progression</span>
            <span>{{ $enrollment->progress_percentage ?? 0 }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-indigo-600 h-2 rounded-full transition-all" 
                 style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
        </div>
    </div>
</div>

<div class="p-4 flex-1 overflow-y-auto">
    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Curriculum</h3>
    
    @foreach($course->chapters as $chapter)
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-900">{{ $chapter->title }}</h4>
                <span class="text-xs text-gray-400">{{ $chapter->lessons->count() }}</span>
            </div>
            
            <div class="space-y-1">
                @foreach($chapter->lessons as $curriculumLesson)
                    @php
                        $isCurrent = $curriculumLesson->id === $lesson->id;
                        $isLessonCompleted = in_array($curriculumLesson->id, $completedLessons);
                    @endphp
                    
                    <a href="{{ route('student.learn.lesson', [$course, $curriculumLesson]) }}" 
                       class="lesson-item flex items-start p-2.5 rounded-lg {{ $isCurrent ? 'active' : '' }} {{ $isLessonCompleted ? 'completed' : '' }}">
                        <div class="flex-shrink-0 mt-0.5 mr-3">
                            @if($isLessonCompleted)
                                <i class="fas fa-check-circle text-green-500"></i>
                            @else
                                <i class="fas fa-{{ $curriculumLesson->type === 'video' ? 'play-circle' : ($curriculumLesson->type === 'pdf' ? 'file-pdf' : ($curriculumLesson->type === 'quiz' ? 'puzzle-piece' : 'file-alt')) }} text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm {{ $isCurrent ? 'text-indigo-600 font-medium' : 'text-gray-700' }} truncate">
                                {{ $curriculumLesson->title }}
                            </p>
                            @if($curriculumLesson->duration)
                                <p class="text-xs text-gray-400">{{ gmdate('i:s', $curriculumLesson->duration) }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('content')
<div x-data="lessonPlayer({{ $lesson->id }}, {{ $course->id }})" class="h-full flex flex-col">
    
    {{-- Bouton ouvrir sidebar (mobile) --}}
    <div class="lg:hidden p-4 flex items-center justify-between bg-gray-900 text-white">
        <button @click="toggleSidebar()"><i class="fas fa-bars mr-2"></i>Curriculum</button>
        <span class="font-medium truncate">{{ $lesson->title }}</span>
    </div>
    
    <div class="flex-1 flex flex-col">
        
        {{-- ============================================ --}}
        {{-- ✅ LECTEUR VIDÉO --}}
        {{-- ============================================ --}}
        @if($lesson->type === 'video')
            @if($lesson->video_url)
                <div class="video-container bg-black">
                    <video id="lessonVideo" 
                           controls 
                           controlsList="nodownload"
                           class="w-full h-full"
                           @timeupdate="saveProgress()"
                           @ended="markComplete()">
                        <source src="{{ $lesson->video_url }}" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center bg-gray-900 text-white">
                    <div class="text-center">
                        <i class="fas fa-video-slash text-6xl text-gray-600 mb-4"></i>
                        <p class="text-xl font-medium mb-2">Vidéo non disponible</p>
                        <p class="text-gray-400">La vidéo n'a pas encore été uploadée par le formateur.</p>
                    </div>
                </div>
            @endif
        @endif
        
        {{-- ============================================ --}}
        {{-- ✅ LECTEUR PDF --}}
        {{-- ============================================ --}}
        @if($lesson->type === 'pdf')
            @if($lesson->pdf_url)
                <div class="flex-1 bg-white">
                    <embed src="{{ $lesson->pdf_url }}" 
                           type="application/pdf" 
                           class="w-full h-full rounded-none">
                </div>
            @else
                <div class="flex-1 flex items-center justify-center bg-gray-900 text-white">
                    <div class="text-center">
                        <i class="fas fa-file-pdf text-6xl text-gray-600 mb-4"></i>
                        <p class="text-xl font-medium mb-2">PDF non disponible</p>
                        <p class="text-gray-400">Le document n'a pas encore été uploadé.</p>
                    </div>
                </div>
            @endif
        @endif
        
        {{-- ============================================ --}}
        {{-- ✅ CONTENU TEXTE --}}
        {{-- ============================================ --}}
        @if($lesson->type === 'text')
            <div class="flex-1 overflow-y-auto bg-white p-6 lg:p-10">
                <div class="max-w-3xl mx-auto prose prose-lg">
                    @if($lesson->content)
                        {!! nl2br(e($lesson->content)) !!}
                    @else
                        <p class="text-gray-400 italic">Aucun contenu texte pour cette leçon.</p>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- ============================================ --}}
        {{-- ✅ QUIZ --}}
        {{-- ============================================ --}}
        @if($lesson->type === 'quiz')
            <div class="flex-1 flex items-center justify-center bg-white">
                <div class="text-center">
                    <i class="fas fa-puzzle-piece text-6xl text-purple-400 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Quiz</h2>
                    <p class="text-gray-500 mb-6">Testez vos connaissances</p>
                    @if($lesson->quiz)
                        <a href="{{ route('student.quiz.take', [$lesson->quiz, $enrollment]) }}" 
                           class="px-8 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-bold text-lg">
                            <i class="fas fa-play mr-2"></i>Commencer le quiz
                        </a>
                    @else
                        <p class="text-gray-400">Quiz non configuré</p>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- ============================================ --}}
        {{-- ✅ RESSOURCES DE LA LEÇON --}}
        {{-- ============================================ --}}
        @php
            $lessonAttachments = $lesson->getMedia('attachments');
        @endphp
        @if($lessonAttachments->count() > 0)
            <div class="bg-white border-t border-gray-200 p-4">
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-paperclip mr-2 text-indigo-500"></i>Ressources de la leçon
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($lessonAttachments as $attachment)
                            <a href="{{ $attachment->getUrl() }}" 
                               target="_blank"
                               class="flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 transition-colors text-sm">
                                <i class="fas fa-download text-indigo-500"></i>
                                <span>{{ $attachment->file_name }}</span>
                                <span class="text-xs text-gray-400">({{ number_format($attachment->size / 1024, 1) }} KB)</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        {{-- ============================================ --}}
        {{-- ✅ RESSOURCES DU COURS --}}
        {{-- ============================================ --}}
        @php
            $courseResources = $course->getMedia('resources');
        @endphp
        @if($courseResources->count() > 0)
            <div class="bg-gray-50 border-t border-gray-200 p-4">
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-folder-download mr-2 text-indigo-500"></i>Ressources du cours
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($courseResources as $resource)
                            <a href="{{ $resource->getUrl() }}" 
                               target="_blank"
                               class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 transition-colors text-sm">
                                <i class="fas fa-download text-indigo-500"></i>
                                <span>{{ $resource->file_name }}</span>
                                <span class="text-xs text-gray-400">({{ number_format($resource->size / 1024, 1) }} KB)</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        {{-- Barre de navigation --}}
        <div class="bg-white border-t border-gray-200 p-4">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $lesson->title }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ $course->title }} • 
                        <span class="uppercase">{{ $lesson->type }}</span>
                        @if($lesson->duration)
                            • {{ gmdate('i:s', $lesson->duration) }}
                        @endif
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    @if($previousLesson)
                        <a href="{{ route('student.learn.lesson', [$course, $previousLesson]) }}" 
                           class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                            <i class="fas fa-arrow-left mr-2"></i>Précédent
                        </a>
                    @endif
                    
                    <button @click="markComplete()" 
                            class="px-5 py-2 rounded-lg font-medium text-sm transition-all"
                            :class="isCompleted ? 'bg-green-100 text-green-700' : 'bg-indigo-600 text-white hover:bg-indigo-700'">
                        <span x-show="!isCompleted"><i class="fas fa-check mr-2"></i>Marquer comme terminé</span>
                        <span x-show="isCompleted"><i class="fas fa-check-circle mr-2"></i>Terminé</span>
                    </button>
                    
                    @if($nextLesson)
                        <a href="{{ route('student.learn.lesson', [$course, $nextLesson]) }}" 
                           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                            Suivant <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    @else
                        <a href="{{ route('student.learn', $course) }}" 
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            <i class="fas fa-flag-checkered mr-2"></i>Terminer le cours
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function lessonPlayer(lessonId, courseId) {
        return {
            isCompleted: {{ $isCompleted ? 'true' : 'false' }},
            
            toggleSidebar() {
                document.querySelector('.curriculum-sidebar').classList.toggle('open');
            },
            
            saveProgress() {
                const video = document.getElementById('lessonVideo');
                if (video) {
                    localStorage.setItem(`video_progress_${lessonId}`, Math.floor(video.currentTime));
                }
            },
            
            async markComplete() {
                try {
                    const response = await fetch(`/student/progress/${courseId}/${lessonId}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.isCompleted = true;
                        
                        if (data.course_completed) {
                            alert('🎉 Félicitations ! Vous avez terminé ce cours !');
                        }
                        
                        // Mettre à jour la progression visuelle
                        document.querySelectorAll('.progress-bar').forEach(bar => {
                            bar.style.width = data.progress + '%';
                        });
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            }
        }
    }
    
    // Restaurer la position de la vidéo
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('lessonVideo');
        if (video) {
            const savedTime = localStorage.getItem(`video_progress_{{ $lesson->id }}`);
            if (savedTime) {
                video.currentTime = parseFloat(savedTime);
            }
        }
    });
</script>
@endpush