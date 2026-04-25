@extends('layouts.instructor')

@section('title', 'Créer un quiz')
@section('page-title', 'Créer un quiz')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.quizzes.index') }}" class="text-gray-400 hover:text-gray-500">Mes Quiz</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Créer un quiz</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .quiz-type-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .quiz-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .quiz-type-card.selected {
        border-color: #4f46e5;
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    }
    
    .difficulty-badge {
        transition: all 0.2s ease;
    }
    
    .difficulty-badge:hover {
        transform: scale(1.05);
    }
    
    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .preview-question {
        border-left: 4px solid #4f46e5;
        transition: all 0.2s ease;
    }
    
    .preview-question:hover {
        background-color: #f9fafb;
    }
    
    .option-item {
        transition: all 0.15s ease;
    }
    
    .option-item:hover {
        background-color: #f3f4f6;
    }
    
    .image-upload-area {
        border: 2px dashed #d1d5db;
        transition: all 0.3s ease;
    }
    
    .image-upload-area:hover {
        border-color: #4f46e5;
        background: #f5f3ff;
    }
    
    @media (max-width: 640px) {
        .quiz-type-card {
            padding: 12px;
        }
    }
</style>
@endpush

@section('content')
<div x-data="quizCreator()" x-init="init()" class="max-w-5xl mx-auto">
    
    <!-- Indicateur d'étapes -->
    <div class="mb-8">
        <div class="flex items-center justify-center max-w-3xl mx-auto">
            <div class="flex items-center w-full">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all"
                         :class="currentStep >= 1 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-gray-200 text-gray-500'">
                        <span x-show="currentStep <= 1">1</span>
                        <i class="fas fa-check" x-show="currentStep > 1"></i>
                    </div>
                    <span class="text-xs mt-1.5 font-medium" :class="currentStep >= 1 ? 'text-indigo-600' : 'text-gray-400'">Type</span>
                </div>
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 1 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all"
                         :class="currentStep >= 2 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-gray-200 text-gray-500'">
                        <span x-show="currentStep <= 2">2</span>
                        <i class="fas fa-check" x-show="currentStep > 2"></i>
                    </div>
                    <span class="text-xs mt-1.5 font-medium" :class="currentStep >= 2 ? 'text-indigo-600' : 'text-gray-400'">Questions</span>
                </div>
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 2 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all"
                         :class="currentStep >= 3 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-gray-200 text-gray-500'">
                        <span x-show="currentStep <= 3">3</span>
                        <i class="fas fa-check" x-show="currentStep > 3"></i>
                    </div>
                    <span class="text-xs mt-1.5 font-medium" :class="currentStep >= 3 ? 'text-indigo-600' : 'text-gray-400'">Paramètres</span>
                </div>
                <div class="flex-1 h-0.5 mx-2" :class="currentStep > 3 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm transition-all"
                         :class="currentStep >= 4 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-gray-200 text-gray-500'">
                        <span>4</span>
                    </div>
                    <span class="text-xs mt-1.5 font-medium" :class="currentStep >= 4 ? 'text-indigo-600' : 'text-gray-400'">Finalisation</span>
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="submitQuiz" class="space-y-6">
        
        <!-- ============================================ -->
        <!-- ÉTAPE 1 : TYPE DE QUIZ ET CONFIGURATION       -->
        <!-- ============================================ -->
        <div x-show="currentStep === 1" x-transition>
            <!-- Choix du cours et de la leçon -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <div class="section-icon bg-gradient-to-br from-purple-500 to-indigo-600">
                            <i class="fas fa-link text-white"></i>
                        </div>
                        Association du quiz
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5 ml-12">Choisissez le cours et la leçon associés à ce quiz</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-book mr-2 text-indigo-500"></i>Cours <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.course_id" @change="filterLessons()" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner un cours</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-list mr-2 text-indigo-500"></i>Leçon <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.lesson_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner une leçon</option>
                                <template x-for="lesson in filteredLessons" :key="lesson.id">
                                    <option :value="lesson.id" x-text="lesson.title"></option>
                                </template>
                            </select>
                            <p x-show="form.course_id && filteredLessons.length === 0" class="text-xs text-amber-600 mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Aucune leçon disponible pour ce cours
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Type de quiz -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <div class="section-icon bg-gradient-to-br from-amber-500 to-orange-600">
                            <i class="fas fa-cube text-white"></i>
                        </div>
                        Type de quiz
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5 ml-12">Choisissez le format de votre quiz</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- QCM -->
                        <div class="quiz-type-card p-5 border-2 rounded-xl"
                             :class="form.quiz_type === 'qcm' ? 'selected border-indigo-500' : 'border-gray-200'"
                             @click="form.quiz_type = 'qcm'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-list-ul text-white text-xl"></i>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                     :class="form.quiz_type === 'qcm' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                    <i class="fas fa-check text-white text-xs" x-show="form.quiz_type === 'qcm'"></i>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">QCM</h3>
                            <p class="text-sm text-gray-500">Questions à choix multiples avec une ou plusieurs bonnes réponses</p>
                            <span class="inline-block mt-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Choix unique • Multiple</span>
                        </div>

                        <!-- Vrai/Faux -->
                        <div class="quiz-type-card p-5 border-2 rounded-xl"
                             :class="form.quiz_type === 'true_false' ? 'selected border-indigo-500' : 'border-gray-200'"
                             @click="form.quiz_type = 'true_false'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-check-double text-white text-xl"></i>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                     :class="form.quiz_type === 'true_false' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                    <i class="fas fa-check text-white text-xs" x-show="form.quiz_type === 'true_false'"></i>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">Vrai/Faux</h3>
                            <p class="text-sm text-gray-500">Questions binaires rapides pour tester les connaissances</p>
                            <span class="inline-block mt-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Rapide • Efficace</span>
                        </div>

                        <!-- Questions ouvertes -->
                        <div class="quiz-type-card p-5 border-2 rounded-xl"
                             :class="form.quiz_type === 'open' ? 'selected border-indigo-500' : 'border-gray-200'"
                             @click="form.quiz_type = 'open'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-pen-fancy text-white text-xl"></i>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                     :class="form.quiz_type === 'open' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                    <i class="fas fa-check text-white text-xs" x-show="form.quiz_type === 'open'"></i>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">Questions ouvertes</h3>
                            <p class="text-sm text-gray-500">Réponses libres pour évaluer la compréhension</p>
                            <span class="inline-block mt-2 text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">Réflexion • Rédaction</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de base -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <div class="section-icon bg-gradient-to-br from-indigo-500 to-blue-600">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        Informations générales
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titre du quiz <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.title" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                               placeholder="Ex: Quiz HTML - Les bases">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea x-model="form.description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 resize-none"
                                  placeholder="Décrivez brièvement ce quiz..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 2 : CRÉATION DES QUESTIONS              -->
        <!-- ============================================ -->
        <div x-show="currentStep === 2" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <div class="section-icon bg-gradient-to-br from-rose-500 to-pink-600">
                                <i class="fas fa-question-circle text-white"></i>
                            </div>
                            Questions du quiz
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5 ml-12">
                            <span x-text="form.questions.length"></span> question(s) créée(s)
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="addQuestion"
                                class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm">
                            <i class="fas fa-plus mr-2"></i>Ajouter une question
                        </button>
                    </div>
                </div>

                <!-- Liste des questions -->
                <div class="p-6 space-y-4">
                    <template x-for="(question, qIndex) in form.questions" :key="qIndex">
                        <div class="preview-question bg-gray-50 rounded-xl p-5 border border-gray-200">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold" x-text="qIndex + 1"></span>
                                    <div>
                                        <select x-model="question.difficulty"
                                                class="text-xs border-gray-300 rounded-lg px-2 py-1">
                                            <option value="easy">Facile</option>
                                            <option value="medium">Moyen</option>
                                            <option value="hard">Difficile</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="addQuestionImage(qIndex)"
                                            class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-gray-100">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button type="button" @click="removeQuestion(qIndex)"
                                            class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Image de la question -->
                            <div x-show="question.image_preview" class="mb-4">
                                <img :src="question.image_preview" class="max-h-48 rounded-lg">
                            </div>

                            <!-- Texte de la question -->
                            <div class="mb-4">
                                <input type="text" x-model="question.text" required
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                                       placeholder="Saisissez votre question...">
                            </div>

                            <!-- Options (pour QCM et Vrai/Faux) -->
                            <template x-if="form.quiz_type === 'qcm' || form.quiz_type === 'true_false'">
                                <div class="space-y-2">
                                    <template x-for="(option, oIndex) in question.options" :key="oIndex">
                                        <div class="option-item flex items-center gap-3 p-2 rounded-lg">
                                            <button type="button" 
                                                    @click="toggleOptionCorrect(qIndex, oIndex)"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0"
                                                    :class="option.is_correct ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500 hover:bg-gray-300'">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                            <input type="text" x-model="option.text" required
                                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                                   :placeholder="'Option ' + (oIndex + 1)">
                                            <button type="button" @click="removeOption(qIndex, oIndex)"
                                                    class="p-2 text-red-400 hover:text-red-600" x-show="question.options.length > 2">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addOption(qIndex)"
                                            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium mt-2">
                                        <i class="fas fa-plus-circle mr-1"></i>Ajouter une option
                                    </button>
                                </div>
                            </template>

                            <!-- Explication -->
                            <div class="mt-4">
                                <input type="text" x-model="question.explanation"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                       placeholder="Explication de la réponse (optionnelle)...">
                            </div>

                            <!-- Points -->
                            <div class="mt-3 flex items-center gap-4">
                                <label class="text-sm text-gray-600">Points :</label>
                                <input type="number" x-model="question.points" min="1"
                                       class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-center">
                            </div>
                        </div>
                    </template>

                    <!-- Message si aucune question -->
                    <div x-show="form.questions.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-question-circle text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">Aucune question pour le moment</p>
                        <button type="button" @click="addQuestion"
                                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                            <i class="fas fa-plus mr-2"></i>Ajouter une question
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 3 : PARAMÈTRES DU QUIZ                  -->
        <!-- ============================================ -->
        <div x-show="currentStep === 3" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <div class="section-icon bg-gradient-to-br from-teal-500 to-cyan-600">
                            <i class="fas fa-sliders-h text-white"></i>
                        </div>
                        Paramètres du quiz
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-bullseye mr-2 text-amber-500"></i>Score minimum (%)
                            </label>
                            <input type="number" x-model="form.passing_score" min="0" max="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <p class="text-xs text-gray-500 mt-1">Pourcentage requis pour réussir</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock mr-2 text-blue-500"></i>Temps limite (minutes)
                            </label>
                            <input type="number" x-model="form.time_limit" min="1" placeholder="Illimité"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour illimité</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-redo mr-2 text-purple-500"></i>Tentatives max
                            </label>
                            <input type="number" x-model="form.max_attempts" min="1" placeholder="Illimité"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour illimité</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="font-medium text-gray-900 mb-4">Options avancées</h3>
                        <div class="space-y-4">
                            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <span class="font-medium text-gray-800">Mélanger les questions</span>
                                    <p class="text-xs text-gray-500">Les questions apparaîtront dans un ordre aléatoire</p>
                                </div>
                                <input type="checkbox" x-model="form.shuffle_questions" class="toggle-switch">
                            </label>
                            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <span class="font-medium text-gray-800">Afficher les résultats</span>
                                    <p class="text-xs text-gray-500">Les étudiants verront leurs résultats après le quiz</p>
                                </div>
                                <input type="checkbox" x-model="form.show_results" class="toggle-switch">
                            </label>
                            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <span class="font-medium text-gray-800">Feedback immédiat</span>
                                    <p class="text-xs text-gray-500">Afficher si la réponse est correcte après chaque question</p>
                                </div>
                                <input type="checkbox" x-model="form.show_feedback" class="toggle-switch">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 4 : FINALISATION                        -->
        <!-- ============================================ -->
        <div x-show="currentStep === 4" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <div class="section-icon bg-gradient-to-br from-green-500 to-emerald-600">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        Prêt à créer le quiz
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-xl p-5 mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Récapitulatif</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Titre :</span><span class="font-medium" x-text="form.title || 'Non défini'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Type :</span><span class="font-medium" x-text="form.quiz_type === 'qcm' ? 'QCM' : form.quiz_type === 'true_false' ? 'Vrai/Faux' : 'Questions ouvertes'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Questions :</span><span class="font-medium" x-text="form.questions.length"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Score minimum :</span><span class="font-medium" x-text="form.passing_score + '%'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Temps limite :</span><span class="font-medium" x-text="form.time_limit ? form.time_limit + ' min' : 'Illimité'"></span></div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-start p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" x-model="form.publish_action" value="publish" class="mt-1 mr-3">
                            <div>
                                <span class="font-medium">Publier maintenant</span>
                                <p class="text-sm text-gray-500">Le quiz sera accessible aux étudiants</p>
                            </div>
                        </label>
                        <label class="flex items-start p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" x-model="form.publish_action" value="draft" class="mt-1 mr-3">
                            <div>
                                <span class="font-medium">Enregistrer comme brouillon</span>
                                <p class="text-sm text-gray-500">Vous pourrez le modifier plus tard</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between">
            <button type="button" @click="prevStep" x-show="currentStep > 1"
                    class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Précédent
            </button>
            <div x-show="currentStep === 1" class="flex-1"></div>
            <button type="button" @click="nextStep" x-show="currentStep < 4"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                Suivant <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="submit" x-show="currentStep === 4"
                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                <i class="fas fa-check mr-2"></i>
                <span x-text="form.publish_action === 'publish' ? 'Publier le quiz' : 'Enregistrer'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function quizCreator() {
        return {
            currentStep: 1,
            form: {
                title: '', description: '', course_id: '', lesson_id: '',
                quiz_type: 'qcm',
                questions: [],
                passing_score: 70, time_limit: '', max_attempts: '',
                shuffle_questions: true, show_results: true, show_feedback: true,
                publish_action: 'draft'
            },
            lessons: @json($lessons ?? []),
            filteredLessons: [],
            
            init() {
                this.$watch('form.quiz_type', (type) => {
                    this.form.questions = [];
                });
            },
            
            filterLessons() {
                this.filteredLessons = this.lessons.filter(l => l.course_id == this.form.course_id);
                this.form.lesson_id = '';
            },
            
            addQuestion() {
                const question = {
                    text: '', difficulty: 'medium', points: 10,
                    explanation: '', image: null, image_preview: null,
                    options: [
                        { text: '', is_correct: false },
                        { text: '', is_correct: false }
                    ]
                };
                if (this.form.quiz_type === 'true_false') {
                    question.options = [
                        { text: 'Vrai', is_correct: false },
                        { text: 'Faux', is_correct: false }
                    ];
                }
                this.form.questions.push(question);
            },
            
            removeQuestion(index) {
                this.form.questions.splice(index, 1);
            },
            
            addOption(qIndex) {
                this.form.questions[qIndex].options.push({ text: '', is_correct: false });
            },
            
            removeOption(qIndex, oIndex) {
                this.form.questions[qIndex].options.splice(oIndex, 1);
            },
            
            toggleOptionCorrect(qIndex, oIndex) {
                const question = this.form.questions[qIndex];
                if (this.form.quiz_type === 'qcm') {
                    question.options[oIndex].is_correct = !question.options[oIndex].is_correct;
                } else {
                    question.options.forEach((opt, i) => { opt.is_correct = i === oIndex; });
                }
            },
            
            addQuestionImage(qIndex) {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.onchange = (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            this.form.questions[qIndex].image_preview = ev.target.result;
                            this.form.questions[qIndex].image = file;
                        };
                        reader.readAsDataURL(file);
                    }
                };
                input.click();
            },
            
            nextStep() { if (this.validateStep()) { this.currentStep++; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
            prevStep() { this.currentStep--; window.scrollTo({ top: 0, behavior: 'smooth' }); },
            
            validateStep() {
                if (this.currentStep === 1) {
                    if (!this.form.title) { alert('Veuillez saisir un titre'); return false; }
                    if (!this.form.course_id) { alert('Veuillez sélectionner un cours'); return false; }
                    if (!this.form.lesson_id) { alert('Veuillez sélectionner une leçon'); return false; }
                }
                if (this.currentStep === 2 && this.form.questions.length === 0) {
                    alert('Ajoutez au moins une question'); return false;
                }
                return true;
            },
            
            async submitQuiz() {
                const formData = new FormData();
                formData.append('title', this.form.title);
                formData.append('description', this.form.description);
                formData.append('lesson_id', this.form.lesson_id);
                formData.append('quiz_type', this.form.quiz_type);
                formData.append('passing_score', this.form.passing_score);
                formData.append('time_limit', this.form.time_limit);
                formData.append('max_attempts', this.form.max_attempts);
                formData.append('shuffle_questions', this.form.shuffle_questions ? '1' : '0');
                formData.append('show_results', this.form.show_results ? '1' : '0');
                formData.append('show_feedback', this.form.show_feedback ? '1' : '0');
                formData.append('publish_action', this.form.publish_action);
                formData.append('questions', JSON.stringify(this.form.questions));
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                try {
                    const response = await fetch('{{ route("instructor.quizzes.store", ["lesson" => "__LESSON_ID__"]) }}'.replace('__LESSON_ID__', this.form.lesson_id), {
                        method: 'POST',
                        body: formData
                    });
                    if (response.ok) {
                        const data = await response.json();
                        window.location.href = '{{ route("instructor.quizzes.edit", ["quiz" => "__QUIZ_ID__"]) }}'.replace('__QUIZ_ID__', data.quiz_id);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la création du quiz');
                }
            }
        }
    }
</script>
<style>
    .toggle-switch { appearance: none; width: 48px; height: 26px; background: #d1d5db; border-radius: 13px; position: relative; cursor: pointer; transition: all 0.3s; }
    .toggle-switch:checked { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
    .toggle-switch::before { content: ''; position: absolute; width: 22px; height: 22px; background: white; border-radius: 50%; top: 2px; left: 2px; transition: transform 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .toggle-switch:checked::before { transform: translateX(22px); }
</style>
@endpush