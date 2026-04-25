@extends('student.layouts.learning')

@section('title', $course->title)

@section('sidebar')
<div class="p-4 border-b border-gray-200">
    <a href="{{ route('student.my-courses') }}" class="text-sm text-indigo-600 hover:text-indigo-700 mb-3 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Mes cours
    </a>
    <h2 class="font-semibold text-gray-900 line-clamp-2">{{ $course->title }}</h2>
    
    {{-- Progression --}}
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
                <span class="text-xs text-gray-400">{{ $chapter->lessons->count() }} leçons</span>
            </div>
            
            <div class="space-y-1">
                @foreach($chapter->lessons as $lesson)
                    @php
                        $isCurrent = isset($currentLesson) && $lesson->id === $currentLesson->id;
                        $isCompleted = in_array($lesson->id, $completedLessons ?? []);
                    @endphp
                    
                    <a href="{{ route('student.learn.lesson', [$course, $lesson]) }}" 
                       class="lesson-item flex items-start p-2.5 rounded-lg {{ $isCurrent ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                        <div class="flex-shrink-0 mt-0.5 mr-3">
                            @if($isCompleted)
                                <i class="fas fa-check-circle text-green-500"></i>
                            @else
                                <i class="fas fa-{{ $lesson->type === 'video' ? 'play-circle' : ($lesson->type === 'pdf' ? 'file-pdf' : ($lesson->type === 'quiz' ? 'puzzle-piece' : 'file-alt')) }} text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm {{ $isCurrent ? 'text-indigo-600 font-medium' : 'text-gray-700' }} truncate">
                                {{ $lesson->title }}
                            </p>
                            @if($lesson->duration)
                                <p class="text-xs text-gray-400">{{ gmdate('i:s', $lesson->duration) }}</p>
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
<div class="flex items-center justify-center h-full">
    <div class="text-center text-white p-8 max-w-2xl">
        {{-- Image du cours --}}
        <img src="{{ $course->thumbnail_url }}" 
             alt="{{ $course->title }}" 
             class="w-64 h-40 object-cover rounded-xl mx-auto mb-6 shadow-2xl">
        
        <h1 class="text-3xl font-bold mb-4">{{ $course->title }}</h1>
        <p class="text-gray-400 mb-2">{{ $course->short_description }}</p>
        
        <div class="flex items-center justify-center gap-4 text-sm text-gray-400 mb-8">
            <span><i class="fas fa-book-open mr-1"></i>{{ $course->lessons->count() }} leçons</span>
            <span><i class="fas fa-clock mr-1"></i>{{ floor($course->lessons->sum('duration') / 3600) }}h de contenu</span>
            <span><i class="fas fa-signal mr-1"></i>{{ ucfirst($course->level) }}</span>
        </div>
        
        {{-- Progression --}}
        <div class="bg-white/10 rounded-xl p-6 mb-8 backdrop-blur-sm">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-300">Votre progression</span>
                <span class="text-white font-bold">{{ $enrollment->progress_percentage ?? 0 }}%</span>
            </div>
            <div class="w-full bg-white/20 rounded-full h-3">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all" 
                     style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
            </div>
        </div>
        
        {{-- Bouton Commencer/Continuer --}}
        @php
            $firstIncompleteLesson = null;
            foreach ($course->chapters as $chapter) {
                foreach ($chapter->lessons as $lesson) {
                    if (!in_array($lesson->id, $completedLessons ?? [])) {
                        $firstIncompleteLesson = $lesson;
                        break 2;
                    }
                }
            }
        @endphp
        
        @if($firstIncompleteLesson)
            <a href="{{ route('student.learn.lesson', [$course, $firstIncompleteLesson]) }}" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-xl transform hover:scale-105">
                <i class="fas fa-play mr-3"></i>
                {{ $enrollment->progress_percentage > 0 ? 'Continuer l\'apprentissage' : 'Commencer le cours' }}
            </a>
        @else
            <div class="text-green-400">
                <i class="fas fa-check-circle text-4xl mb-3"></i>
                <p class="text-xl font-bold">Cours terminé ! 🎉</p>
                <a href="{{ route('student.certificate', $course) }}" 
                   class="inline-block mt-4 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700">
                    <i class="fas fa-certificate mr-2"></i>Voir mon certificat
                </a>
            </div>
        @endif
    </div>
</div>
@endsection