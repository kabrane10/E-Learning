<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!$quizStarted)
            <!-- Écran de démarrage -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-indigo-600 px-8 py-12 text-white text-center">
                    <i class="fas fa-puzzle-piece text-5xl mb-4"></i>
                    <h1 class="text-3xl font-bold mb-2">{{ $quiz->title }}</h1>
                    <p class="text-indigo-100">{{ $quiz->description }}</p>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-900">{{ count($questions) }}</div>
                            <div class="text-sm text-gray-500">Questions</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-900">{{ $quiz->passing_score }}%</div>
                            <div class="text-sm text-gray-500">Score minimum</div>
                        </div>
                        @if($quiz->time_limit)
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-900">{{ $quiz->time_limit }}</div>
                            <div class="text-sm text-gray-500">Minutes</div>
                        </div>
                        @endif
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            @php
                                $attempts = $quiz->userAttempts(Auth::id())->count();
                                $remaining = $quiz->max_attempts ? $quiz->max_attempts - $attempts : '∞';
                            @endphp
                            <div class="text-3xl font-bold text-gray-900">{{ $remaining }}</div>
                            <div class="text-sm text-gray-500">Tentatives restantes</div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button wire:click="startQuiz" 
                                class="px-8 py-4 bg-indigo-600 text-white text-lg font-medium rounded-xl hover:bg-indigo-700 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-play mr-2"></i>Commencer le quiz
                        </button>
                        
                        <p class="mt-4 text-sm text-gray-500">
                            Une fois commencé, le temps est limité. Bonne chance !
                        </p>
                    </div>
                </div>
            </div>
        @elseif(!$quizCompleted)
            <!-- Quiz en cours -->
            <div>
                <!-- En-tête avec progression et timer -->
                <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-700">
                                Question {{ $currentQuestionIndex + 1 }}/{{ count($questions) }}
                            </span>
                            <div class="w-48 h-2 bg-gray-200 rounded-full">
                                <div class="h-full bg-indigo-600 rounded-full transition-all duration-300" 
                                     style="width: {{ (($currentQuestionIndex + 1) / count($questions)) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        @if($timeLeft !== null)
                            <div class="flex items-center space-x-2" 
                                 x-data="{ timeLeft: {{ $timeLeft }} }" 
                                 x-init="setInterval(() => { if(timeLeft > 0) { timeLeft--; $wire.timeLeft = timeLeft; } else { $wire.timeOut(); } }, 1000)">
                                <i class="fas fa-clock text-indigo-600"></i>
                                <span class="font-mono text-lg font-semibold" 
                                      x-text="Math.floor(timeLeft / 60) + ':' + (timeLeft % 60).toString().padStart(2, '0')"></span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Question actuelle -->
                @php
                    $currentQuestion = $questions[$currentQuestionIndex] ?? null;
                @endphp
                
                @if($currentQuestion)
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <div class="flex items-start justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $currentQuestion->question_text }}</h2>
                            <span class="text-sm font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                                {{ $currentQuestion->points }} point{{ $currentQuestion->points > 1 ? 's' : '' }}
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            @if(in_array($currentQuestion->question_type, ['single', 'true_false']))
                                @foreach($currentQuestion->options as $option)
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" 
                                               name="question_{{ $currentQuestion->id }}" 
                                               value="{{ $option->id }}"
                                               wire:model.live="answers.{{ $currentQuestion->id }}"
                                               class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                    </label>
                                @endforeach
                            @elseif($currentQuestion->question_type === 'multiple')
                                @foreach($currentQuestion->options as $option)
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="checkbox" 
                                               value="{{ $option->id }}"
                                               wire:model.live="answers.{{ $currentQuestion->id }}"
                                               class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                        
                        <!-- Navigation -->
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                            <button wire:click="previousQuestion" 
                                    @if($currentQuestionIndex === 0) disabled @endif
                                    class="px-4 py-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-arrow-left mr-2"></i>Précédent
                            </button>
                            
                            <div class="flex space-x-2">
                                @foreach($questions as $index => $q)
                                    <button wire:click="goToQuestion({{ $index }})"
                                            class="w-8 h-8 rounded-full text-sm font-medium transition-colors
                                                   {{ $index === $currentQuestionIndex ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}
                                                   {{ isset($answers[$q->id]) && $answers[$q->id] ? 'ring-2 ring-green-400' : '' }}">
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>
                            
                            @if($currentQuestionIndex === count($questions) - 1)
                                <button wire:click="submitQuiz" 
                                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    Terminer <i class="fas fa-check ml-2"></i>
                                </button>
                            @else
                                <button wire:click="nextQuestion" 
                                        class="px-4 py-2 text-indigo-600 hover:text-indigo-700">
                                    Suivant <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Résultats du quiz -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="{{ $quizResult['is_passed'] ? 'bg-green-600' : 'bg-red-600' }} px-8 py-12 text-white text-center">
                    @if($quizResult['is_passed'])
                        <i class="fas fa-trophy text-5xl mb-4"></i>
                        <h1 class="text-3xl font-bold mb-2">Félicitations ! 🎉</h1>
                        <p class="text-green-100">Vous avez réussi le quiz !</p>
                    @else
                        <i class="fas fa-redo-alt text-5xl mb-4"></i>
                        <h1 class="text-3xl font-bold mb-2">Pas tout à fait...</h1>
                        <p class="text-red-100">Continuez à vous entraîner !</p>
                    @endif
                </div>
                
                <div class="p-8">
                    <!-- Score -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-32 h-32 rounded-full border-8 {{ $quizResult['is_passed'] ? 'border-green-100' : 'border-red-100' }} mb-4">
                            <span class="text-4xl font-bold {{ $quizResult['is_passed'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $quizResult['score'] }}%
                            </span>
                        </div>
                        <p class="text-gray-500">
                            Score minimum requis : {{ $quiz->passing_score }}%
                        </p>
                    </div>
                    
                    <!-- Statistiques -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $quizResult['correct_answers'] }}/{{ $quizResult['total_questions'] }}</div>
                            <div class="text-sm text-gray-500">Réponses correctes</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $quizResult['earned_points'] }}/{{ $quizResult['total_points'] }}</div>
                            <div class="text-sm text-gray-500">Points obtenus</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ floor($quizResult['time_spent'] / 60) }}:{{ str_pad($quizResult['time_spent'] % 60, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-sm text-gray-500">Temps passé</div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center justify-center space-x-4">
                        @if($quizResult['is_passed'])
                            <a href="{{ route('student.learn', $quiz->lesson->course) }}" 
                               class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-arrow-right mr-2"></i>Continuer le cours
                            </a>
                        @else
                            @if($quiz->canUserAttempt(Auth::id()))
                                <button wire:click="startQuiz" 
                                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-redo-alt mr-2"></i>Réessayer
                                </button>
                            @endif
                            <a href="{{ route('student.learn', $quiz->lesson->course) }}" 
                               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                Revenir au cours
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>