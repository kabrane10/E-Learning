@extends('layouts.instructor')

@section('title', $course->title . ' - Curriculum')

@push('styles')
<style>
    .sortable-ghost {
        opacity: 0.4;
        background: #c8ebfb;
    }
    .sortable-drag {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: rotate(2deg);
    }
    .lesson-item {
        transition: all 0.2s ease;
    }
    .lesson-item:hover {
        background-color: #f9fafb;
    }
</style>
@endpush

@section('content')
<div x-data="curriculumManager({{ $course->id }})" x-init="init()" class="max-w-5xl mx-auto">
    
    <!-- En-tête avec progression -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $course->title }}</h1>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $course->is_published ? 'Publié' : 'Brouillon' }}
                    </span>
                    <span class="text-sm text-gray-500">
                        <span x-text="totalLessons"></span> leçons • <span x-text="totalDuration"></span> min
                    </span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('instructor.courses.edit', $course) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Modifier les détails
                </a>
                <form action="{{ route('instructor.courses.toggle-publish', $course) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white rounded-lg {{ $course->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}">
                        {{ $course->is_published ? 'Dépublier' : 'Publier le cours' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Zone Curriculum -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Curriculum du cours</h2>
            <p class="text-sm text-gray-500 mt-0.5">Organisez vos chapitres et leçons par glisser-déposer</p>
        </div>

        <div class="p-6">
            <!-- Liste des chapitres (Drag & Drop) -->
            <div id="chapters-container" class="space-y-4">
                @foreach($course->chapters as $chapter)
                <div class="chapter-item bg-gray-50 rounded-lg border border-gray-200" data-chapter-id="{{ $chapter->id }}">
                    <!-- En-tête du chapitre -->
                    <div class="flex items-center justify-between px-4 py-3 bg-white rounded-t-lg border-b border-gray-200 cursor-move chapter-header">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400 handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                            <span class="font-medium text-gray-900 chapter-title">{{ $chapter->title }}</span>
                            <span class="text-sm text-gray-500">({{ $chapter->lessons->count() }} leçons)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="editChapter({{ $chapter->id }}, '{{ $chapter->title }}')" 
                                    class="p-1 text-gray-400 hover:text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="deleteChapter({{ $chapter->id }})" 
                                    class="p-1 text-gray-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Leçons du chapitre -->
                    <div class="lessons-container p-3 space-y-2" data-chapter-id="{{ $chapter->id }}">
                        @foreach($chapter->lessons as $lesson)
                        <div class="lesson-item flex items-center justify-between p-3 bg-white rounded border border-gray-200 cursor-move hover:shadow-sm transition-shadow" 
                             data-lesson-id="{{ $lesson->id }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-gray-400 handle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $lesson->title }}</span>
                                    <div class="flex items-center space-x-2 mt-0.5">
                                        <span class="text-xs text-gray-500 uppercase">{{ $lesson->content_type }}</span>
                                        @if($lesson->is_free_preview)
                                        <span class="text-xs text-green-600 bg-green-50 px-1.5 py-0.5 rounded">Aperçu gratuit</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="editLesson({{ $lesson->id }}, '{{ $lesson->title }}', '{{ $lesson->content_type }}', {{ $lesson->is_free_preview }})" 
                                        class="p-1 text-gray-400 hover:text-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteLesson({{ $lesson->id }})" 
                                        class="p-1 text-gray-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Bouton Ajouter une leçon -->
                    <div class="px-3 pb-3">
                        <button @click="openLessonModal(null, {{ $chapter->id }})" 
                                class="w-full py-2 text-sm text-indigo-600 hover:text-indigo-700 font-medium border border-dashed border-gray-300 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                            + Ajouter une leçon
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Bouton Ajouter un chapitre -->
            <div class="mt-6">
                <button @click="openChapterModal()" 
                        class="w-full py-3 text-indigo-600 hover:text-indigo-700 font-medium border-2 border-dashed border-gray-300 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                    + Ajouter un chapitre
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Chapitre -->
    <div x-show="chapterModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="chapterModalOpen = false"></div>
            <div class="relative bg-white rounded-lg max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" x-text="editingChapter ? 'Modifier le chapitre' : 'Nouveau chapitre'"></h3>
                </div>
                <form @submit.prevent="saveChapter">
                    <div class="px-6 py-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titre du chapitre</label>
                        <input type="text" x-model="chapterForm.title" required
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                        <button type="button" @click="chapterModalOpen = false" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Leçon -->
    <div x-show="lessonModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="lessonModalOpen = false"></div>
            <div class="relative bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" x-text="editingLesson ? 'Modifier la leçon' : 'Nouvelle leçon'"></h3>
                </div>
                <form @submit.prevent="saveLesson" enctype="multipart/form-data">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Titre de la leçon</label>
                            <input type="text" x-model="lessonForm.title" required
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de contenu</label>
                            <select x-model="lessonForm.content_type" required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="video">Vidéo</option>
                                <option value="pdf">PDF</option>
                                <option value="quiz">Quiz</option>
                                <option value="text">Texte</option>
                            </select>
                        </div>

                        <div x-show="lessonForm.content_type === 'video'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fichier vidéo</label>
                            <input type="file" name="video_file" accept="video/*"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">MP4, MOV, AVI, WebM • Max 2GB</p>
                        </div>

                        <div x-show="lessonForm.content_type === 'pdf'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fichier PDF</label>
                            <input type="file" name="pdf_file" accept=".pdf"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">PDF • Max 50MB</p>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="lessonForm.is_free_preview" 
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Aperçu gratuit (accessible sans inscription)</span>
                            </label>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                        <button type="button" @click="lessonModalOpen = false" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function curriculumManager(courseId) {
        return {
            // State
            courseId: courseId,
            chapterModalOpen: false,
            lessonModalOpen: false,
            editingChapter: null,
            editingLesson: null,
            selectedChapterId: null,
            totalLessons: {{ $course->lessons->count() }},
            totalDuration: {{ $course->lessons->sum('duration') }},
            
            chapterForm: {
                title: ''
            },
            
            lessonForm: {
                title: '',
                content_type: 'video',
                is_free_preview: false
            },

            // Initialisation
            init() {
                this.initSortable();
            },

            // Drag & Drop
            initSortable() {
                // Drag & Drop des chapitres
                new Sortable(document.getElementById('chapters-container'), {
                    handle: '.chapter-header',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onEnd: (evt) => {
                        this.reorderChapters();
                    }
                });

                // Drag & Drop des leçons dans chaque chapitre
                document.querySelectorAll('.lessons-container').forEach(container => {
                    new Sortable(container, {
                        group: 'lessons',
                        handle: '.handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: (evt) => {
                            this.reorderLessons();
                        }
                    });
                });
            },

            async reorderChapters() {
                const chapters = [];
                document.querySelectorAll('.chapter-item').forEach((el, index) => {
                    chapters.push({
                        id: el.dataset.chapterId,
                        order: index
                    });
                });

                await fetch(`/instructor/courses/${this.courseId}/chapters/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ chapters })
                });
            },

            async reorderLessons() {
                const lessons = [];
                document.querySelectorAll('.lesson-item').forEach((el, index) => {
                    const chapterContainer = el.closest('.lessons-container');
                    lessons.push({
                        id: el.dataset.lessonId,
                        order: index,
                        chapter_id: chapterContainer ? chapterContainer.dataset.chapterId : null
                    });
                });

                await fetch(`/instructor/courses/${this.courseId}/lessons/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ lessons })
                });

                this.updateStats();
            },

            // Chapitres
            openChapterModal(chapter = null) {
                this.editingChapter = chapter;
                this.chapterForm.title = chapter ? chapter.title : '';
                this.chapterModalOpen = true;
            },

            editChapter(id, title) {
                this.editingChapter = { id, title };
                this.chapterForm.title = title;
                this.chapterModalOpen = true;
            },

            async saveChapter() {
                const url = this.editingChapter 
                    ? `/instructor/courses/${this.courseId}/chapters/${this.editingChapter.id}`
                    : `/instructor/courses/${this.courseId}/chapters`;
                
                const method = this.editingChapter ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.chapterForm)
                });

                if (response.ok) {
                    window.location.reload();
                }
            },

            async deleteChapter(id) {
                if (!confirm('Supprimer ce chapitre et toutes ses leçons ?')) return;

                await fetch(`/instructor/courses/${this.courseId}/chapters/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                window.location.reload();
            },

            // Leçons
            openLessonModal(lesson = null, chapterId = null) {
                this.editingLesson = lesson;
                this.selectedChapterId = chapterId;
                
                if (lesson) {
                    this.lessonForm = {
                        title: lesson.title,
                        content_type: lesson.content_type,
                        is_free_preview: lesson.is_free_preview
                    };
                } else {
                    this.lessonForm = {
                        title: '',
                        content_type: 'video',
                        is_free_preview: false
                    };
                }
                
                this.lessonModalOpen = true;
            },

            editLesson(id, title, type, isFree) {
                this.editingLesson = { id, title, content_type: type, is_free_preview: isFree };
                this.lessonForm = { title, content_type: type, is_free_preview: isFree };
                this.lessonModalOpen = true;
            },

            async saveLesson(event) {
                const formData = new FormData(event.target);
                formData.append('title', this.lessonForm.title);
                formData.append('content_type', this.lessonForm.content_type);
                formData.append('is_free_preview', this.lessonForm.is_free_preview ? '1' : '0');
                
                if (this.selectedChapterId) {
                    formData.append('chapter_id', this.selectedChapterId);
                }

                const url = this.editingLesson
                    ? `/instructor/courses/${this.courseId}/lessons/${this.editingLesson.id}`
                    : `/instructor/courses/${this.courseId}/lessons/${this.selectedChapterId || ''}`;
                
                const method = this.editingLesson ? 'PUT' : 'POST';
                
                if (!this.editingLesson) {
                    formData.append('_method', 'POST');
                } else {
                    formData.append('_method', 'PUT');
                }
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                }
            },

            async deleteLesson(id) {
                if (!confirm('Supprimer cette leçon ?')) return;

                await fetch(`/instructor/courses/${this.courseId}/lessons/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                window.location.reload();
            },

            updateStats() {
                this.totalLessons = document.querySelectorAll('.lesson-item').length;
                // Mettre à jour la durée totale si nécessaire
            }
        }
    }
</script>
@endpush