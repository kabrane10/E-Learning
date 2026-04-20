<?php

namespace App\Livewire\Student;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TakeQuiz extends Component
{
    public Quiz $quiz;
    public $enrollment;
    public $attempt;
    public $questions = [];
    public $currentQuestionIndex = 0;
    public $answers = [];
    public $timeLeft = null;
    public $quizStarted = false;
    public $quizCompleted = false;
    public $quizResult = null;
    public $showResults = false;

    protected $listeners = ['saveAnswer', 'nextQuestion', 'previousQuestion', 'submitQuiz'];

    public function mount(Quiz $quiz, Enrollment $enrollment)
    {
        $this->quiz = $quiz;
        $this->enrollment = $enrollment;
        
        // Vérifier si l'utilisateur peut tenter le quiz
        if (!$this->quiz->canUserAttempt(Auth::id())) {
            return redirect()->route('student.learn', $quiz->lesson->course)
                ->with('error', 'Vous avez dépassé le nombre maximum de tentatives pour ce quiz.');
        }
        
        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        $query = $this->quiz->questions()->with('options');
        
        if ($this->quiz->shuffle_questions) {
            $query->inRandomOrder();
        }
        
        $this->questions = $query->get();
        
        // Initialiser les réponses
        foreach ($this->questions as $question) {
            $this->answers[$question->id] = $question->question_type === 'multiple' ? [] : null;
        }
    }

    public function startQuiz()
    {
        $this->quizStarted = true;
        
        // Créer une tentative
        $this->attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $this->quiz->id,
            'enrollment_id' => $this->enrollment->id,
            'total_questions' => count($this->questions),
            'started_at' => now(),
        ]);
        
        // Initialiser le timer si nécessaire
        if ($this->quiz->time_limit) {
            $this->timeLeft = $this->quiz->time_limit * 60;
            $this->dispatch('startTimer', ['timeLeft' => $this->timeLeft]);
        }
    }

    public function saveAnswer($questionId, $answer)
    {
        $this->answers[$questionId] = $answer;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function submitQuiz()
    {
        if (!$this->attempt) {
            return;
        }
        
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;
        
        foreach ($this->questions as $question) {
            $userAnswer = $this->answers[$question->id] ?? null;
            $isCorrect = false;
            
            if ($question->question_type === 'single' || $question->question_type === 'true_false') {
                $correctOption = $question->options->where('is_correct', true)->first();
                $isCorrect = $correctOption && $userAnswer == $correctOption->id;
            } elseif ($question->question_type === 'multiple') {
                $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
                $userAnswers = is_array($userAnswer) ? $userAnswer : [];
                sort($correctOptions);
                sort($userAnswers);
                $isCorrect = $correctOptions == $userAnswers;
            }
            
            if ($isCorrect) {
                $correctAnswers++;
                $earnedPoints += $question->points;
            }
            
            $totalPoints += $question->points;
            
            // Sauvegarder la réponse
            QuizAnswer::create([
                'quiz_attempt_id' => $this->attempt->id,
                'question_id' => $question->id,
                'answer_data' => json_encode($userAnswer),
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ]);
        }
        
        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $isPassed = $score >= $this->quiz->passing_score;
        
        // Calculer le temps passé
        $timeSpent = $this->attempt->started_at->diffInSeconds(now());
        
        $this->attempt->update([
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'time_spent' => $timeSpent,
            'is_passed' => $isPassed,
            'completed_at' => now(),
        ]);
        
        $this->quizResult = [
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => count($this->questions),
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
            'is_passed' => $isPassed,
            'time_spent' => $timeSpent,
        ];
        
        $this->quizCompleted = true;
        $this->showResults = true;
        
        // Si réussi, marquer la leçon comme complétée
        if ($isPassed) {
            $this->markLessonAsCompleted();
        }
    }

    public function timeOut()
    {
        $this->submitQuiz();
    }

    private function markLessonAsCompleted()
    {
        LessonCompletion::firstOrCreate([
            'user_id' => Auth::id(),
            'lesson_id' => $this->quiz->lesson_id,
        ], [
            'enrollment_id' => $this->enrollment->id,
        ]);
    }

    public function render()
    {
        return view('livewire.student.take-quiz')
            ->layout('layouts.public');
    }
}