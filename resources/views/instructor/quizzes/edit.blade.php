@extends('layouts.instructor')

@section('title', 'Éditer le quiz - ' . $quiz->title)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.css">
@endpush

@section('content')
<div x-data="quizEditor({{ $quiz->id }})" class="max-w-5xl mx-auto">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Leçon : {{ $quiz->lesson->title }}
                </p>
            </div>
            <a href="{{ route('instructor.courses.show', $quiz->lesson->course) }}" 
               class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Retour au cours
            </a>
        </div>
    </div>
    
    <!-- Paramètres du quiz -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Paramètres du quiz</h2>
        
        <form action="{{ route('instructor.quizzes.update', $quiz) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
                    <input type="text" name="title" value="{{ $quiz->title }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Score minimum (%)</label>
                    <input type="number" name="passing_score" value="{{ $quiz->passing_score }}" min="0" max="100" required
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Temps limite (minutes)</label>
                    <input type="number" name="time_limit" value="{{ $quiz->time_limit }}" min="1"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour aucune limite</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tentatives maximum</label>
                    <input type="number" name="max_attempts" value="{{ $quiz->max_attempts }}" min="1"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour illimité</p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ $quiz->description }}</textarea>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="shuffle_questions" value="1" {{ $quiz->shuffle_questions ? 'checked' : '' }}
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label class="ml-2 text-sm text-gray-700">Mélanger les questions</label>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>
    
    <!-- Questions -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Questions ({{ $quiz->questions->count() }})</h2>
            <button @click="openQuestionModal()" 
                    class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>Ajouter une question
            </button>
        </div>
        
        <div class="p-6">
            <div id="questions-container" class="space-y-4">
                @foreach($quiz->questions as $question)
                    <div class="question-item bg-gray-50 rounded-lg border border-gray-200 p-4" data-question-id="{{ $question->id }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $question->question_text }}</span>
                                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded">
                                        {{ $question->question_type === 'single' ? 'Choix unique' : ($question->question_type === 'multiple' ? 'Choix multiple' : 'Vrai/Faux') }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach($question->options as $option)
                                        <div class="flex items-center text-sm">
                                            <span class="w-4 h-4 mr-2 {{ $option->is_correct ? 'text-green-600' : 'text-gray-400' }}">
                                                @if($option->is_correct)
                                                    <i class="fas fa-check-circle"></i>
                                                @else
                                                    <i class="far fa-circle"></i>
                                                @endif
                                            </span>
                                            <span class="{{ $option->is_correct ? 'text-green-700 font-medium' : 'text-gray-600' }}">
                                                {{ $option->option_text }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                @if($question->explanation)
                                    <div class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>{{ $question->explanation }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <button @click="editQuestion({{ $question->id }})" 
                                        class="p-2 text-gray-400 hover:text-indigo-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deleteQuestion({{ $question->id }})" 
                                        class="p-2 text-gray-400 hover:text-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <div class="cursor-move p-2 text-gray-400 handle">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($quiz->questions->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-question-circle text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Aucune question pour le moment.</p>
                    <button @click="openQuestionModal()" 
                            class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Ajouter une question
                    </button>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal Question -->
    <div x-show="questionModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="questionModalOpen = false"></div>
            <div class="relative bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" x-text="editingQuestion ? 'Modifier la question' : 'Nouvelle question'"></h3>
                </div>
                
                <form @submit.prevent="saveQuestion">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                            <textarea x-model="questionForm.question_text" rows="2" required
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                <select x-model="questionForm.question_type" required
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="single">Choix unique</option>
                                    <option value="multiple">Choix multiple</option>
                                    <option value="true_false">Vrai/Faux</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                                <input type="number" x-model="questionForm.points" min="1" required
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Explication (optionnelle)</label>
                            <textarea x-model="questionForm.explanation" rows="2"
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Options</label>
                                <button type="button" @click="addOption" 
                                        class="text-sm text-indigo-600 hover:text-indigo-700">
                                    <i class="fas fa-plus mr-1"></i>Ajouter
                                </button>
                            </div>
                            
                            <div class="space-y-2">
                                <template x-for="(option, index) in questionForm.options" :key="index">
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" x-model="option.is_correct" 
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <input type="text" x-model="option.text" placeholder="Option text" required
                                               class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" @click="removeOption(index)" 
                                                class="p-2 text-gray-400 hover:text-red-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                        <button type="button" @click="questionModalOpen = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    function quizEditor(quizId) {
        return {
            questionModalOpen: false,
            editingQuestion: null,
            questionForm: {
                question_text: '',
                question_type: 'single',
                points: 1,
                explanation: '',
                options: [
                    { text: '', is_correct: false },
                    { text: '', is_correct: false }
                ]
            },
            
            init() {
                this.initSortable();
            },
            
            initSortable() {
                new Sortable(document.getElementById('questions-container'), {
                    handle: '.handle',
                    animation: 150,
                    onEnd: () => {
                        this.reorderQuestions();
                    }
                });
            },
            
            async reorderQuestions() {
                const questions = [];
                document.querySelectorAll('.question-item').forEach((el, index) => {
                    questions.push({
                        id: el.dataset.questionId,
                        order: index
                    });
                });
                
                await fetch(`/instructor/quizzes/${quizId}/questions/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ questions })
                });
            },
            
            openQuestionModal(question = null) {
                this.editingQuestion = question;
                
                if (question) {
                    // Charger les données de la question
                    fetch(`/instructor/questions/${question.id}`)
                        .then(r => r.json())
                        .then(data => {
                            this.questionForm = {
                                question_text: data.question_text,
                                question_type: data.question_type,
                                points: data.points,
                                explanation: data.explanation,
                                options: data.options
                            };
                        });
                } else {
                    this.questionForm = {
                        question_text: '',
                        question_type: 'single',
                        points: 1,
                        explanation: '',
                        options: [
                            { text: '', is_correct: false },
                            { text: '', is_correct: false }
                        ]
                    };
                }
                
                this.questionModalOpen = true;
            },
            
            addOption() {
                this.questionForm.options.push({ text: '', is_correct: false });
            },
            
            removeOption(index) {
                if (this.questionForm.options.length > 2) {
                    this.questionForm.options.splice(index, 1);
                }
            },
            
            async saveQuestion() {
                const url = this.editingQuestion 
                    ? `/instructor/questions/${this.editingQuestion.id}`
                    : `/instructor/quizzes/${quizId}/questions`;
                
                const method = this.editingQuestion ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.questionForm)
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            },
            
            async deleteQuestion(id) {
                if (!confirm('Supprimer cette question ?')) return;
                
                await fetch(`/instructor/questions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                window.location.reload();
            }
        }
    }
</script>
@endpush