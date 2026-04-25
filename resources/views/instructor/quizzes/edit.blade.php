@extends('layouts.instructor')

@section('title', 'Éditer le quiz - ' . $quiz->title)
@section('page-title', 'Éditer le quiz')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.quizzes.index') }}" class="text-gray-400 hover:text-gray-500">Mes Quiz</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">{{ Str::limit($quiz->title, 40) }}</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .question-card {
        transition: all 0.2s ease;
    }
    .question-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .option-item {
        transition: all 0.15s ease;
    }
    .option-item:hover {
        background-color: #f9fafb;
    }
    .drag-handle {
        cursor: grab;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .quiz-type-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .quiz-type-card.selected {
        border-color: #4f46e5;
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    }
    .section-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .toggle-switch { appearance: none; width: 48px; height: 26px; background: #d1d5db; border-radius: 13px; position: relative; cursor: pointer; transition: all 0.3s; }
    .toggle-switch:checked { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
    .toggle-switch::before { content: ''; position: absolute; width: 22px; height: 22px; background: white; border-radius: 50%; top: 2px; left: 2px; transition: transform 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .toggle-switch:checked::before { transform: translateX(22px); }
</style>
@endpush

@section('content')
<div x-data="quizEditor({{ $quiz->id }})" x-init="init()">
    
    @php
        $course = $quiz->lesson->course ?? null;
        $questions = $quiz->questions()->with('options')->orderBy('order')->get();
        $stats = [
            'total_questions' => $questions->count(),
            'total_points' => $questions->sum('points'),
            'attempts_count' => $quiz->attempts()->count(),
        ];
    @endphp

    <!-- En-tête du quiz -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $quiz->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $quiz->is_published ? 'Publié' : 'Brouillon' }}
                        </span>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                            {{ $quiz->quiz_type === 'qcm' ? 'QCM' : ($quiz->quiz_type === 'true_false' ? 'Vrai/Faux' : 'Questions ouvertes') }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $quiz->title }}</h1>
                    <p class="text-gray-600 text-sm">{{ $quiz->description ?: 'Aucune description' }}</p>
                    @if($course)
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-book-open mr-1"></i>Cours : {{ $course->title }} • Leçon : {{ $quiz->lesson->title ?? 'N/A' }}
                        </p>
                    @endif
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('instructor.quizzes.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                    <button @click="openSettingsModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        <i class="fas fa-cog mr-2"></i>Paramètres
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-3 md:grid-cols-5 gap-4 p-6 bg-gray-50 border-t border-gray-200">
            <div class="text-center"><p class="text-2xl font-bold text-gray-900">{{ $stats['total_questions'] }}</p><p class="text-xs text-gray-500">Questions</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-gray-900">{{ $stats['total_points'] }}</p><p class="text-xs text-gray-500">Points totaux</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-gray-900">{{ $quiz->passing_score }}%</p><p class="text-xs text-gray-500">Score minimum</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-gray-900">{{ $quiz->time_limit ?: '∞' }}</p><p class="text-xs text-gray-500">Minutes</p></div>
            <div class="text-center"><p class="text-2xl font-bold text-gray-900">{{ number_format($stats['attempts_count']) }}</p><p class="text-xs text-gray-500">Tentatives</p></div>
        </div>
    </div>

    <!-- Liste des questions -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-list-ul text-indigo-600"></i>Questions
            </h2>
            <button @click="openQuestionModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm shadow-sm">
                <i class="fas fa-plus mr-2"></i>Ajouter une question
            </button>
        </div>
        
        <div id="questions-container" class="space-y-4">
            <template x-for="(question, index) in questions" :key="question.id">
                <div class="question-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" :data-question-id="question.id">
                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <div class="drag-handle p-2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="text-sm font-medium text-gray-500">Q<span x-text="index + 1"></span></span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700"><span x-text="question.points"></span> pts</span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full" 
                                          :class="question.difficulty === 'easy' ? 'bg-green-100 text-green-700' : (question.difficulty === 'hard' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')">
                                        <span x-text="question.difficulty === 'easy' ? 'Facile' : (question.difficulty === 'hard' ? 'Difficile' : 'Moyen')"></span>
                                    </span>
                                </div>
                                <p class="text-gray-900 font-medium mb-3" x-text="question.question_text"></p>
                                <div class="space-y-2 mb-3">
                                    <template x-for="option in question.options" :key="option.id">
                                        <div class="option-item flex items-center p-2 rounded-lg">
                                            <span class="w-5 h-5 mr-3" :class="option.is_correct ? 'text-green-500' : 'text-gray-400'">
                                                <i class="fas" :class="option.is_correct ? 'fa-check-circle' : 'fa-circle'"></i>
                                            </span>
                                            <span class="text-gray-700" x-text="option.option_text"></span>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="question.explanation" class="text-xs text-gray-500 bg-gray-50 p-3 rounded-lg">
                                    <i class="fas fa-info-circle mr-1 text-indigo-500"></i><span x-text="question.explanation"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="editQuestion(question)" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50" title="Modifier"><i class="fas fa-edit"></i></button>
                                <button @click="duplicateQuestion(question)" class="p-2 text-gray-400 hover:text-green-600 rounded-lg hover:bg-green-50" title="Dupliquer"><i class="fas fa-copy"></i></button>
                                <button @click="confirmDeleteQuestion(question.id)" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" title="Supprimer"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            <div x-show="questions.length === 0" class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <div class="w-20 h-20 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-puzzle-piece text-purple-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune question</h3>
                <button @click="openQuestionModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Ajouter une question
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Paramètres -->
    <div x-show="settingsModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="settingsModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold">Paramètres du quiz</h3></div>
                <form @submit.prevent="saveSettings">
                    <div class="p-6 space-y-5">
                        <div><label class="block text-sm font-medium mb-2">Titre</label><input type="text" x-model="settingsForm.title" required class="w-full px-4 py-2.5 border rounded-lg"></div>
                        <div><label class="block text-sm font-medium mb-2">Description</label><textarea x-model="settingsForm.description" rows="3" class="w-full px-4 py-2.5 border rounded-lg"></textarea></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium mb-2">Score minimum (%)</label><input type="number" x-model="settingsForm.passing_score" min="0" max="100" class="w-full px-4 py-2.5 border rounded-lg"></div>
                            <div><label class="block text-sm font-medium mb-2">Temps limite (min)</label><input type="number" x-model="settingsForm.time_limit" min="0" class="w-full px-4 py-2.5 border rounded-lg"></div>
                            <div><label class="block text-sm font-medium mb-2">Tentatives max</label><input type="number" x-model="settingsForm.max_attempts" min="1" class="w-full px-4 py-2.5 border rounded-lg"></div>
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer"><input type="checkbox" x-model="settingsForm.shuffle_questions" class="rounded border-gray-300 text-indigo-600"><span class="ml-2 text-sm">Mélanger les questions</span></label>
                            <label class="flex items-center cursor-pointer"><input type="checkbox" x-model="settingsForm.is_published" class="rounded border-gray-300 text-indigo-600"><span class="ml-2 text-sm">Publier le quiz</span></label>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="settingsModalOpen = false" class="px-4 py-2 text-gray-700 bg-white border rounded-lg">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Question -->
    <div x-show="questionModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="questionModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b sticky top-0 bg-white"><h3 class="text-lg font-semibold" x-text="editingQuestion ? 'Modifier la question' : 'Nouvelle question'"></h3></div>
                <form @submit.prevent="saveQuestion">
                    <div class="p-6 space-y-5">
                        <div class="flex gap-3">
                            <select x-model="questionForm.difficulty" class="text-sm border rounded-lg px-3 py-2">
                                <option value="easy">🟢 Facile</option><option value="medium">🟡 Moyen</option><option value="hard">🔴 Difficile</option>
                            </select>
                            <input type="number" x-model="questionForm.points" min="1" class="w-24 text-sm border rounded-lg px-3 py-2" placeholder="Points">
                        </div>
                        <div><label class="block text-sm font-medium mb-2">Question</label><textarea x-model="questionForm.question_text" rows="3" required class="w-full px-4 py-2.5 border rounded-lg"></textarea></div>
                        <div>
                            <div class="flex items-center justify-between mb-2"><label class="text-sm font-medium">Options</label><button type="button" @click="addOption" class="text-sm text-indigo-600"><i class="fas fa-plus mr-1"></i>Ajouter</button></div>
                            <div class="space-y-2">
                                <template x-for="(option, index) in questionForm.options" :key="index">
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="toggleOptionCorrect(index)" class="w-8 h-8 rounded-lg flex items-center justify-center" :class="option.is_correct ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500'"><i class="fas fa-check text-sm"></i></button>
                                        <input type="text" x-model="option.option_text" required class="flex-1 px-4 py-2 border rounded-lg">
                                        <button type="button" @click="removeOption(index)" class="p-2 text-red-400 hover:text-red-600" x-show="questionForm.options.length > 2"><i class="fas fa-times"></i></button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div><label class="block text-sm font-medium mb-2">Explication</label><textarea x-model="questionForm.explanation" rows="2" class="w-full px-4 py-2.5 border rounded-lg"></textarea></div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="questionModalOpen = false" class="px-4 py-2 text-gray-700 bg-white border rounded-lg">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg"><span x-text="editingQuestion ? 'Mettre à jour' : 'Ajouter'"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i></div>
                    <h3 class="text-lg font-semibold mb-2">Supprimer la question</h3>
                    <p class="text-gray-500 mb-6">Êtes-vous sûr ?</p>
                    <div class="flex justify-center space-x-3">
                        <button @click="deleteModalOpen = false" class="px-4 py-2 text-gray-700 bg-white border rounded-lg">Annuler</button>
                        <button @click="confirmDeleteQuestion()" class="px-4 py-2 bg-red-600 text-white rounded-lg">Supprimer</button>
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
    function quizEditor(quizId) {
        return {
            questions: @json($questions),
            settingsModalOpen: false, questionModalOpen: false, deleteModalOpen: false,
            questionToDelete: null, editingQuestion: null,
            
            settingsForm: {
                title: @json($quiz->title), description: @json($quiz->description),
                passing_score: @json($quiz->passing_score), time_limit: @json($quiz->time_limit),
                max_attempts: @json($quiz->max_attempts), shuffle_questions: @json($quiz->shuffle_questions),
                is_published: @json($quiz->is_published)
            },
            
            questionForm: { question_text: '', difficulty: 'medium', points: 10, explanation: '', options: [] },
            
            init() {
                this.initSortable();
                this.resetQuestionForm();
            },
            
            initSortable() {
                const container = document.getElementById('questions-container');
                if (container) new Sortable(container, { handle: '.drag-handle', animation: 150, onEnd: () => this.reorderQuestions() });
            },
            
            async reorderQuestions() {
                const ids = [];
                document.querySelectorAll('#questions-container > div').forEach(el => ids.push(el.dataset.questionId));
                await fetch(`/instructor/quizzes/${quizId}/questions/reorder`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ questions: ids.map((id, i) => ({ id, order: i })) })
                });
            },
            
            resetQuestionForm() { this.questionForm = { question_text: '', difficulty: 'medium', points: 10, explanation: '', options: [{ option_text: '', is_correct: false }, { option_text: '', is_correct: false }] }; },
            
            openSettingsModal() { this.settingsModalOpen = true; },
            
            async saveSettings() {
                await fetch(`/instructor/quizzes/${quizId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.settingsForm)
                });
                this.settingsModalOpen = false;
                this.showToast('Paramètres mis à jour');
                setTimeout(() => window.location.reload(), 500);
            },
            
            openQuestionModal(question = null) {
                this.editingQuestion = question;
                if (question) {
                    this.questionForm = {
                        question_text: question.question_text, difficulty: question.difficulty || 'medium',
                        points: question.points, explanation: question.explanation || '',
                        options: [...question.options]
                    };
                } else { this.resetQuestionForm(); }
                this.questionModalOpen = true;
            },
            
            editQuestion(q) { this.openQuestionModal(q); },
            
            async saveQuestion() {
                const url = this.editingQuestion 
                    ? `/instructor/questions/${this.editingQuestion.id}`
                    : `/instructor/quizzes/${quizId}/questions`;
                const method = this.editingQuestion ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.questionForm)
                });
                
                if (response.ok) { this.questionModalOpen = false; this.editingQuestion = null; setTimeout(() => window.location.reload(), 300); }
            },
            
            duplicateQuestion(q) {
                const newQ = { ...q, id: Date.now(), question_text: q.question_text + ' (copie)', options: q.options.map(o => ({...o, id: Date.now() + Math.random()})) };
                this.questions.push(newQ);
                this.showToast('Question dupliquée');
            },
            
            confirmDeleteQuestion(id) { this.questionToDelete = id; this.deleteModalOpen = true; },
            
            async confirmDeleteQuestion() {
                if (this.questionToDelete) {
                    await fetch(`/instructor/questions/${this.questionToDelete}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                    this.questions = this.questions.filter(q => q.id !== this.questionToDelete);
                    this.showToast('Question supprimée');
                }
                this.deleteModalOpen = false; this.questionToDelete = null;
            },
            
            addOption() { this.questionForm.options.push({ option_text: '', is_correct: false }); },
            removeOption(i) { if (this.questionForm.options.length > 2) this.questionForm.options.splice(i, 1); },
            toggleOptionCorrect(i) {
                this.questionForm.options[i].is_correct = !this.questionForm.options[i].is_correct;
            },
            
            showToast(message) { alert(message); }
        }
    }
</script>
@endpush