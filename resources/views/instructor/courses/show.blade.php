@extends('layouts.instructor')

@section('title', $course->title . ' - Curriculum')
@section('page-title', $course->title)

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-500">Mes Cours</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">{{ Str::limit($course->title, 40) }}</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .curriculum-item {
        transition: all 0.2s ease;
    }
    
    .curriculum-item:hover {
        background-color: #f9fafb;
    }
    
    .curriculum-item.dragging {
        opacity: 0.5;
        transform: scale(0.98);
    }
    
    .drag-handle {
        cursor: grab;
    }
    
    .drag-handle:active {
        cursor: grabbing;
    }
    
    .lesson-type-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .chapter-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>
@endpush

@section('content')
<div x-data="curriculumManager({{ $course->id }})" x-init="init()">
    
    @php
        $stats = [
            'total_lessons' => $course->lessons->count(),
            'total_duration' => $course->lessons->sum('duration'),
            'video_lessons' => $course->lessons->where('content_type', 'video')->count(),
            'quiz_lessons' => $course->lessons->where('content_type', 'quiz')->count(),
            'pdf_lessons' => $course->lessons->where('content_type', 'pdf')->count(),
        ];
    @endphp

    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition
         x-cloak
         class="toast-notification">
        <div :class="toast.type === 'success' ? 'bg-green-50 border-green-400 text-green-700' : 'bg-red-50 border-red-400 text-red-700'"
             class="border-l-4 p-4 rounded-r-lg shadow-lg flex items-center">
            <i :class="toast.type === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500'" class="mr-3"></i>
            <span x-text="toast.message"></span>
            <button @click="toast.show = false" class="ml-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- En-tête du cours -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                <!-- Miniature -->
                <div class="flex-shrink-0">
                    <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=400' }}" 
                         alt="{{ $course->title }}" 
                         class="w-full lg:w-48 h-32 object-cover rounded-xl">
                </div>
                
                <!-- Infos -->
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $course->is_published ? 'Publié' : 'Brouillon' }}
                        </span>
                        <span class="px-3 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                            @if($course->level === 'beginner')
                                Débutant
                            @elseif($course->level === 'intermediate')
                                Intermédiaire
                            @else
                                Avancé
                            @endif
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-folder mr-1"></i>{{ $course->category ?? 'Non catégorisé' }}
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-tag mr-1"></i>{{ $course->is_free ? 'Gratuit' : number_format($course->price, 2) . ' €' }}
                        </span>
                    </div>
                    
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
                    <p class="text-gray-600 text-sm line-clamp-2">{{ $course->short_description }}</p>
                    
                    <div class="flex flex-wrap items-center gap-4 mt-4">
                        <a href="{{ route('instructor.courses.edit', $course) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-700">
                            <i class="fas fa-edit mr-1"></i>Modifier les détails
                        </a>
                        @if($course->is_published)
                            <a href="{{ route('courses.show', $course->slug) }}" 
                               target="_blank"
                               class="text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-external-link-alt mr-1"></i>Voir la page publique
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex flex-col items-end gap-2">
                    <div class="flex items-center gap-2">
                        <form action="{{ route('instructor.courses.toggle-publish', $course) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 {{ $course->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors text-sm">
                                <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-check-circle' }} mr-2"></i>
                                {{ $course->is_published ? 'Dépublier' : 'Publier' }}
                            </button>
                        </form>
                        <a href="{{ route('instructor.courses.analytics', $course) }}" 
                           class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            <i class="fas fa-chart-bar mr-2"></i>Statistiques
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 p-6 bg-gray-50 border-t border-gray-200">
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_lessons'] }}</p>
                <p class="text-xs text-gray-500">Leçons</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">
                    @if($stats['total_duration'] > 0)
                        {{ floor($stats['total_duration'] / 60) }}h{{ $stats['total_duration'] % 60 }}
                    @else
                        0h
                    @endif
                </p>
                <p class="text-xs text-gray-500">Durée totale</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['video_lessons'] }}</p>
                <p class="text-xs text-gray-500">Vidéos</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['quiz_lessons'] }}</p>
                <p class="text-xs text-gray-500">Quiz</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pdf_lessons'] }}</p>
                <p class="text-xs text-gray-500">PDF</p>
            </div>
        </div>
    </div>

    <!-- Actions rapides pour les quiz -->
     
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl shadow-sm border border-purple-200 p-5 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                <i class="fas fa-puzzle-piece text-white text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Quiz du cours</h3>
                <p class="text-sm text-gray-600">
                    @if($course->quizzes()->count() > 0)
                        {{ $course->quizzes()->count() }} quiz créé(s) pour ce cours
                    @else
                        Créez des quiz pour évaluer vos étudiants
                    @endif
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            
            {{-- ✅ Bouton 1 : Créer un quiz directement lié au cours (même sans leçon) --}}
            <a href="{{ route('instructor.quizzes.create.from.course', $course) }}" 
   class="px-5 py-2.5 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors shadow-sm font-medium">
    <i class="fas fa-plus-circle mr-2"></i>
    Créer un quiz pour ce cours
</a>
            
            {{-- ✅ Bouton 2 : Créer un quiz à partir d'une leçon existante (dropdown) --}}
            @if($course->lessons->count() > 0)
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-list mr-2"></i>
                        Créer à partir d'une leçon
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition
                         x-cloak
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50 max-h-80 overflow-y-auto">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-700">Choisir une leçon</p>
                            <p class="text-xs text-gray-500 mt-0.5">Le quiz sera lié à cette leçon</p>
                        </div>
                        
                        <div class="py-1">
                            <template x-for="chapter in chapters" :key="chapter.id">
                                <div>
                                    <p class="px-4 py-1.5 text-xs font-semibold text-gray-400 uppercase bg-gray-50" x-text="chapter.title"></p>
                                    <template x-for="lesson in chapter.lessons" :key="lesson.id">
                                        <a :href="'/instructor/lessons/' + lesson.id + '/quiz/create'"
                                           class="flex items-center justify-between px-4 py-2.5 hover:bg-indigo-50 transition-colors text-sm">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-circle text-[6px]" :class="{
                                                    'text-blue-500': lesson.content_type === 'video',
                                                    'text-green-500': lesson.content_type === 'pdf',
                                                    'text-purple-500': lesson.content_type === 'quiz',
                                                    'text-gray-400': lesson.content_type === 'text'
                                                }"></i>
                                                <span class="text-gray-700" x-text="lesson.title"></span>
                                            </div>
                                            <template x-if="lesson.quiz_id">
                                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Quiz existant</span>
                                            </template>
                                            <template x-if="!lesson.quiz_id">
                                                <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                                            </template>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                        
                        <div class="px-4 py-2 border-t border-gray-100">
                            <button @click="open = false; openLessonModal(null); lessonForm.content_type = 'quiz'"
                                    class="text-sm text-indigo-600 hover:text-indigo-700 flex items-center w-full">
                                <i class="fas fa-plus mr-1"></i>Créer une nouvelle leçon de type Quiz
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
            {{-- ✅ Bouton 3 : Voir tous les quiz du cours --}}
            @if($course->quizzes()->count() > 0)
                <a href="{{ route('instructor.quizzes.index', ['course_id' => $course->id]) }}" 
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                    <i class="fas fa-list mr-2"></i>
                    Voir tous les quiz ({{ $course->quizzes()->count() }})
                </a>
            @endif
        </div>
    </div>
    
    {{-- Message d'aide si aucune leçon n'existe --}}
    @if($course->lessons->count() === 0)
        <div class="mt-4 p-3 bg-white/60 rounded-lg border border-purple-100">
            <p class="text-sm text-purple-700">
                <i class="fas fa-info-circle mr-2"></i>
                Vous pouvez créer un quiz directement lié à ce cours sans avoir de leçon. 
                Les étudiants pourront y accéder depuis la page du cours.
            </p>
        </div>
    @endif
</div>

    <!-- Onglets -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('instructor.courses.show', $course) }}" 
               class="border-indigo-600 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-list mr-2"></i>Curriculum
            </a>
            <a href="{{ route('instructor.courses.students', $course) }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-users mr-2"></i>Étudiants
            </a>
            <a href="{{ route('instructor.courses.reviews', $course) }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-star mr-2"></i>Avis
            </a>
        </nav>
    </div>

    <!-- Zone Curriculum -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Curriculum du cours</h2>
                <p class="text-sm text-gray-500 mt-0.5">Organisez vos chapitres et leçons par glisser-déposer</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="expandAll" class="text-sm text-indigo-600 hover:text-indigo-700">
                    <i class="fas fa-expand-alt mr-1"></i>Tout déplier
                </button>
                <button @click="collapseAll" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-compress-alt mr-1"></i>Tout replier
                </button>
                <button @click="openChapterModal()" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i class="fas fa-plus mr-2"></i>Nouveau chapitre
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Liste des chapitres -->
            <div id="chapters-container" class="space-y-3">
                <template x-for="chapter in chapters" :key="chapter.id">
                    <div class="curriculum-item bg-white rounded-xl border border-gray-200 overflow-hidden"
                         :data-chapter-id="chapter.id"
                         :class="{ 'expanded': expandedChapters.includes(chapter.id) }">
                        
                        <!-- En-tête du chapitre -->
                        <div class="chapter-header flex items-center justify-between px-4 py-3 border-b border-gray-200 cursor-pointer"
                             @click="toggleChapter(chapter.id)">
                            <div class="flex items-center gap-3">
                                <div class="drag-handle p-1 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform"
                                       :class="{ 'rotate-90': expandedChapters.includes(chapter.id) }"></i>
                                    <i class="fas fa-folder-open text-indigo-500"></i>
                                    <span class="font-medium text-gray-900" x-text="chapter.title"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500" x-text="chapter.lessons.length + ' leçon(s)'"></span>
                                <button @click.stop="openLessonModal(chapter.id)" 
                                        class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors"
                                        title="Ajouter une leçon">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button @click.stop="editChapter(chapter)" 
                                        class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                        title="Modifier le chapitre">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click.stop="confirmDeleteChapter(chapter.id)" 
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                        title="Supprimer le chapitre">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Leçons du chapitre -->
                        <div x-show="expandedChapters.includes(chapter.id)" x-collapse>
                            <div class="lessons-container divide-y divide-gray-100">
                                <template x-for="lesson in chapter.lessons" :key="lesson.id">
                                    <div class="curriculum-item flex items-center justify-between px-4 py-3 hover:bg-gray-50"
                                         :data-lesson-id="lesson.id">
                                        <div class="flex items-center gap-3">
                                            <div class="drag-handle p-1 text-gray-400 hover:text-gray-600">
                                                <i class="fas fa-grip-vertical"></i>
                                            </div>
                                            
                                            <div class="lesson-type-icon"
                                                 :class="{
                                                    'bg-blue-100 text-blue-content_600': lesson.content_type === 'video',
                                                    'bg-green-100 text-green-600': lesson.content_type === 'pdf',
                                                    'bg-purple-100 text-purple-600': lesson.content_type === 'quiz',
                                                    'bg-gray-100 text-gray-600': lesson.content_type === 'text'
                                                 }">
                                                <i class="fas" :class="{
                                                    'fa-play': lesson.content_type === 'video',
                                                    'fa-file-pdf': lesson.content_type === 'pdf',
                                                    'fa-puzzle-piece': lesson.content_type === 'quiz',
                                                    'fa-file-alt': lesson.content_type === 'text'
                                                }"></i>
                                            </div>
                                            
                                            <div>
                                                <span class="font-medium text-gray-900" x-text="lesson.title"></span>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-xs text-gray-500 uppercase" x-text="lesson.content_type"></span>
                                                    <template x-if="lesson.duration">
                                                        <span class="text-xs text-gray-400">
                                                            <i class="far fa-clock mr-1"></i>
                                                            <span x-text="formatDuration(lesson.duration)"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="lesson.is_free_preview">
                                                        <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-eye mr-1"></i>Aperçu gratuit
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <template x-if="lesson.content_type === 'quiz' && lesson.quiz_id">
                                                <a :href="'/instructor/quizzes/' + lesson.quiz_id + '/edit'" 
                                                   class="p-1.5 text-purple-500 hover:text-purple-700 rounded-lg hover:bg-purple-50 transition-colors"
                                                   title="Modifier le quiz">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </template>
                                            <template x-if="lesson.content_type === 'quiz' && !lesson.quiz_id">
                                                <a :href="'/instructor/lessons/' + lesson.id + '/quiz/create'" 
                                                   class="p-1.5 text-purple-500 hover:text-purple-700 rounded-lg hover:bg-purple-50 transition-colors"
                                                   title="Créer un quiz">
                                                    <i class="fas fa-plus-circle"></i>
                                                </a>
                                            </template>
                                            <button @click="editLesson(lesson)" 
                                                    class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                                    title="Modifier la leçon">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click="confirmDeleteLesson(lesson.id)" 
                                                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                    title="Supprimer la leçon">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                
                                <div x-show="chapter.lessons.length === 0" class="px-4 py-6 text-center text-gray-500">
                                    <i class="fas fa-plus-circle text-2xl mb-2 opacity-50"></i>
                                    <p class="text-sm">Aucune leçon dans ce chapitre</p>
                                    <button @click="openLessonModal(chapter.id)" 
                                            class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                        <i class="fas fa-plus mr-1"></i>Ajouter une leçon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Message si aucun chapitre -->
            <div x-show="chapters.length === 0" class="text-center py-12">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-book-open text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun chapitre</h3>
                <p class="text-gray-500 mb-4">Commencez par créer votre premier chapitre</p>
                <button @click="openChapterModal()" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Nouveau chapitre
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Chapitre -->
    <div x-show="chapterModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="chapterModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="editingChapter ? 'Modifier le chapitre' : 'Nouveau chapitre'"></h3>
                </div>
                
                <form @submit.prevent="saveChapter">
                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titre du chapitre</label>
                        <input type="text" 
                               x-model="chapterForm.title" 
                               required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ex: Introduction">
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="chapterModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <span x-text="editingChapter ? 'Mettre à jour' : 'Créer'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Leçon -->
    <div x-show="lessonModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="lessonModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="editingLesson ? 'Modifier la leçon' : 'Nouvelle leçon'"></h3>
                </div>
                
                <form @submit.prevent="saveLesson" enctype="multipart/form-data">
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Titre de la leçon</label>
                            <input type="text" 
                                   x-model="lessonForm.title" 
                                   required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de contenu</label>
                           <select x-model="lessonForm.content_type"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="video">Vidéo</option>
                                <option value="pdf">PDF</option>
                                <option value="quiz">Quiz</option>
                                <option value="text">Texte</option>
                            </select>

                            <div x-show="lessonForm.content_type === 'quiz'" x-transition class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-info-circle text-purple-600 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-purple-800">Leçon de type Quiz</p>
                                        <p class="text-xs text-purple-700 mt-1">
                                            Après avoir créé cette leçon, vous pourrez configurer le quiz (questions, réponses, score minimum, etc.).
                                        </p>
                                        <p class="text-xs text-purple-700 mt-1">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            Astuce : Préparez vos questions à l'avance pour gagner du temps.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div x-show="lessonForm.content_type === 'video'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fichier vidéo</label>
                            <input type="file" 
                                   name="video_file"
                                   accept="video/*"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">MP4, MOV, AVI, WebM • Max 2GB</p>
                        </div>
                        
                        <div x-show="lessonForm.content_type === 'pdf'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fichier PDF</label>
                            <input type="file" 
                                   name="pdf_file"
                                   accept=".pdf"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">PDF • Max 50MB</p>
                        </div>
                        
                        <div x-show="lessonForm.content_type === 'text'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
                            <textarea x-model="lessonForm.content" 
                                       rows="5"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        
                        <div x-show="lessonForm.content_type === 'video'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Durée (en secondes)</label>
                            <input type="number" 
                                x-model="lessonForm.duration" 
                                   min="1"
                                   placeholder="Ex: 300 (5 minutes)"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       x-model="lessonForm.is_free_preview"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Aperçu gratuit (accessible sans inscription)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="lessonModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                :disabled="isSubmitting">
                            <i class="fas fa-spinner fa-spin mr-2" x-show="isSubmitting"></i>
                            <span x-text="editingLesson ? 'Mettre à jour' : 'Ajouter'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                    <h3 class="text-lg font-semibold text-gray-900 mb-2" x-text="deleteModalTitle"></h3>
                    <p class="text-gray-500 mb-6" x-text="deleteModalMessage"></p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button @click="confirmDelete()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    function curriculumManager(courseId) {
    return {
        // Données
        chapters: {!! json_encode($course->chapters->map(function($chapter) {
            return [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'order' => $chapter->order,
                'lessons' => $chapter->lessons->map(function($lesson) {
                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'type' => $lesson->content_type, // ✅ Utiliser content_type
                        'content_type' => $lesson->content_type, // ✅ Ajouter aussi content_type
                        'duration' => $lesson->duration,
                        'is_free_preview' => (bool) $lesson->is_free_preview,
                        'quiz_id' => $lesson->quiz_id ?? null,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray()) !!},
        
        expandedChapters: {!! json_encode($course->chapters->pluck('id')->toArray()) !!},
        
        // Toast
        toast: { show: false, type: 'success', message: '' },
        
        // Modals
        chapterModalOpen: false,
        lessonModalOpen: false,
        deleteModalOpen: false,
        deleteModalTitle: '',
        deleteModalMessage: '',
        deleteType: null,
        deleteId: null,
        
        // Formulaires
        editingChapter: null,
        editingLesson: null,
        selectedChapterId: null,
        isSubmitting: false,
        
        chapterForm: { title: '' },
        
        // ✅ Le formulaire utilise content_type, pas type
        lessonForm: {
            title: '',
            content_type: 'video',
            content: '',
            duration: '',
            is_free_preview: false
        },
        
        init() {
            this.initSortable();
            console.log('Curriculum manager initialisé');
        },
        
        initSortable() {
            const chaptersContainer = document.getElementById('chapters-container');
            if (chaptersContainer) {
                new Sortable(chaptersContainer, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: () => this.reorderChapters()
                });
            }
            
            document.querySelectorAll('.lessons-container').forEach(container => {
                new Sortable(container, {
                    handle: '.drag-handle',
                    group: 'lessons',
                    animation: 150,
                    onEnd: () => this.reorderLessons()
                });
            });
        },
        
        async reorderChapters() {
            const chapters = [];
            document.querySelectorAll('[data-chapter-id]').forEach((el, index) => {
                chapters.push({ id: el.dataset.chapterId, order: index });
            });
            
            try {
                await fetch(`/instructor/courses/${courseId}/chapters/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ chapters })
                });
                this.showToast('success', 'Chapitres réorganisés');
            } catch (error) {
                console.error('Erreur:', error);
            }
        },
        
        async reorderLessons() {
            const lessons = [];
            document.querySelectorAll('[data-lesson-id]').forEach((el, index) => {
                const chapterContainer = el.closest('[data-chapter-id]');
                lessons.push({
                    id: el.dataset.lessonId,
                    order: index,
                    chapter_id: chapterContainer ? chapterContainer.dataset.chapterId : null
                });
            });
            
            try {
                await fetch(`/instructor/courses/${courseId}/lessons/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ lessons })
                });
                this.showToast('success', 'Leçons réorganisées');
            } catch (error) {
                console.error('Erreur:', error);
            }
        },
        
        toggleChapter(chapterId) {
            if (this.expandedChapters.includes(chapterId)) {
                this.expandedChapters = this.expandedChapters.filter(id => id !== chapterId);
            } else {
                this.expandedChapters.push(chapterId);
            }
        },
        
        expandAll() { this.expandedChapters = this.chapters.map(c => c.id); },
        collapseAll() { this.expandedChapters = []; },
        
        formatDuration(seconds) {
            if (!seconds) return '';
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },
        
        showToast(type, message) {
            this.toast.show = true;
            this.toast.type = type;
            this.toast.message = message;
            setTimeout(() => this.toast.show = false, 4000);
        },
        
        // ========== CHAPITRES ==========
        openChapterModal(chapter = null) {
            this.editingChapter = chapter;
            this.chapterForm.title = chapter ? chapter.title : '';
            this.chapterModalOpen = true;
        },
        
        editChapter(chapter) { this.openChapterModal(chapter); },
        
        async saveChapter() {
            const url = this.editingChapter 
                ? `/instructor/courses/${courseId}/chapters/${this.editingChapter.id}`
                : `/instructor/courses/${courseId}/chapters`;
            const method = this.editingChapter ? 'PUT' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.chapterForm)
                });
                
                if (response.ok) {
                    this.showToast('success', this.editingChapter ? 'Chapitre mis à jour' : 'Chapitre créé');
                    setTimeout(() => window.location.reload(), 500);
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.showToast('error', 'Une erreur est survenue');
            }
            
            this.chapterModalOpen = false;
        },
        
        confirmDeleteChapter(id) {
            this.deleteType = 'chapter';
            this.deleteId = id;
            this.deleteModalTitle = 'Supprimer le chapitre';
            this.deleteModalMessage = 'Toutes les leçons qu\'il contient seront également supprimées.';
            this.deleteModalOpen = true;
        },
        
        // ========== LEÇONS ==========
        openLessonModal(chapterId, lesson = null) {
            this.selectedChapterId = chapterId;
            this.editingLesson = lesson;
            
            if (lesson) {
                this.lessonForm = {
                    title: lesson.title,
                    content_type: lesson.type || lesson.content_type || 'video',
                    content: lesson.content || '',
                    duration: lesson.duration || '',
                    is_free_preview: lesson.is_free_preview || false
                };
            } else {
                this.lessonForm = {
                    title: '',
                    content_type: 'video',
                    content: '',
                    duration: '',
                    is_free_preview: false
                };
            }
            
            this.lessonModalOpen = true;
        },
        
        // ✅ Fonction pour ouvrir le modal avec type Quiz pré-sélectionné
        openLessonModalForQuiz() {
            const firstChapterId = this.chapters.length > 0 ? this.chapters[0].id : null;
            
            this.selectedChapterId = firstChapterId;
            this.editingLesson = null;
            
            this.lessonForm = {
                title: 'Quiz - ' + new Date().toLocaleDateString('fr-FR'),
                content_type: 'quiz',
                content: '',
                duration: '',
                is_free_preview: false
            };
            
            this.lessonModalOpen = true;
        },
        
        editLesson(lesson) {
            const chapter = this.chapters.find(c => c.lessons.some(l => l.id === lesson.id));
            if (chapter) {
                this.openLessonModal(chapter.id, lesson);
            }
        },
        
        async saveLesson() {
            this.isSubmitting = true;
            
            console.log('📤 lessonForm:', this.lessonForm);
            
            const formData = new FormData();
            formData.append('title', this.lessonForm.title);
            formData.append('content_type', this.lessonForm.content_type); // ✅ Bon nom
            formData.append('content', this.lessonForm.content || '');
            formData.append('duration', this.lessonForm.duration || '');
            formData.append('is_free_preview', this.lessonForm.is_free_preview ? '1' : '0');
            formData.append('chapter_id', this.selectedChapterId || '');
            
            // Fichiers
            const fileInput = document.querySelector('input[type="file"]');
            if (fileInput && fileInput.files[0]) {
                formData.append(
                    this.lessonForm.content_type === 'video' ? 'video_file' : 'pdf_file', 
                    fileInput.files[0]
                );
            }
            
            // ✅ Log pour déboguer
            console.log('📦 FormData:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            const url = this.editingLesson
                ? `/instructor/courses/${courseId}/lessons/${this.editingLesson.id}`
                : `/instructor/courses/${courseId}/lessons`;
            
            if (this.editingLesson) {
                formData.append('_method', 'PUT');
            }
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                console.log('📥 Réponse:', data);
                
                if (response.ok) {
                    // ✅ Redirection si quiz créé
                    if (this.lessonForm.content_type === 'quiz' && data.lesson && !this.editingLesson) {
                        this.showToast('success', 'Leçon créée ! Redirection vers le quiz...');
                        setTimeout(() => {
                            window.location.href = `/instructor/lessons/${data.lesson.id}/quiz/create`;
                        }, 800);
                    } else {
                        this.showToast('success', this.editingLesson ? 'Leçon mise à jour' : 'Leçon ajoutée');
                        setTimeout(() => window.location.reload(), 500);
                    }
                } else {
                    this.showToast('error', data.message || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('❌ Erreur:', error);
                this.showToast('error', 'Une erreur est survenue');
            }
            
            this.isSubmitting = false;
            this.lessonModalOpen = false;
        },
        
        confirmDeleteLesson(id) {
            this.deleteType = 'lesson';
            this.deleteId = id;
            this.deleteModalTitle = 'Supprimer la leçon';
            this.deleteModalMessage = 'Êtes-vous sûr de vouloir supprimer cette leçon ?';
            this.deleteModalOpen = true;
        },
        
        async confirmDelete() {
            let url;
            if (this.deleteType === 'chapter') {
                url = `/instructor/courses/${courseId}/chapters/${this.deleteId}`;
            } else {
                url = `/instructor/courses/${courseId}/lessons/${this.deleteId}`;
            }
            
            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.showToast('success', this.deleteType === 'chapter' ? 'Chapitre supprimé' : 'Leçon supprimée');
                    setTimeout(() => window.location.reload(), 500);
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.showToast('error', 'Une erreur est survenue');
            }
            
            this.deleteModalOpen = false;
            this.deleteType = null;
            this.deleteId = null;
        },
    }
}
</script>
@endpush