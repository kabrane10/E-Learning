<div class="space-y-4">
    <!-- Résumé -->
    <div class="bg-gray-50 rounded-xl p-4">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-xs text-gray-500">Score</p>
                <p class="text-2xl font-bold {{ $attempt->score >= $attempt->quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                    {{ $attempt->score }}%
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Bonnes réponses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Temps</p>
                <p class="text-2xl font-bold text-gray-900">
                    @if($attempt->time_spent)
                        {{ floor($attempt->time_spent / 60) }}:{{ str_pad($attempt->time_spent % 60, 2, '0', STR_PAD_LEFT) }}
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Détail par question -->
    <h4 class="font-semibold text-gray-900">Détail par question</h4>
    
    @foreach($attempt->answers as $answer)
        <div class="border rounded-lg p-4 {{ $answer->is_correct ? 'border-green-200 bg-green-50/30' : 'border-red-200 bg-red-50/30' }}">
            <div class="flex items-start justify-between mb-2">
                <p class="font-medium text-gray-900">{{ $answer->question->question_text }}</p>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $answer->is_correct ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}
                </span>
            </div>
            
            <div class="text-sm text-gray-600">
                <p><strong>Réponse de l'étudiant :</strong> 
                    @php
                        $userAnswers = is_array($answer->answer_data) ? $answer->answer_data : [$answer->answer_data];
                        $selectedOptions = $answer->question->options->whereIn('id', $userAnswers);
                    @endphp
                    {{ $selectedOptions->pluck('option_text')->implode(', ') ?: 'Aucune réponse' }}
                </p>
                
                <p class="mt-1"><strong>Bonne(s) réponse(s) :</strong> 
                    {{ $answer->question->options->where('is_correct', true)->pluck('option_text')->implode(', ') }}
                </p>
            </div>
            
            @if($answer->question->explanation)
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>{{ $answer->question->explanation }}
                </p>
            @endif
        </div>
    @endforeach
</div>