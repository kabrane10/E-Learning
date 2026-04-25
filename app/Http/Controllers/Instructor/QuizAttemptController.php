<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    /**
     * Display attempts for a specific quiz.
     */
    public function index(Request $request, Quiz $quiz)
    {
        // Vérifier que le formateur est bien propriétaire du cours
        $this->authorizeAccess($quiz);
        
        $query = $quiz->attempts()
            ->with('user');
        
        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('result')) {
            $isPassed = $request->result === 'passed';
            $query->where('is_passed', $isPassed);
        }
        
        $attempts = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Charger le comptage des questions
        $quiz->loadCount('questions');
        
        // Statistiques
        $stats = [
            'total' => $quiz->attempts()->count(),
            'passed' => $quiz->attempts()->where('is_passed', true)->count(),
            'failed' => $quiz->attempts()->where('is_passed', false)->count(),
            'avg_score' => round($quiz->attempts()->avg('score') ?? 0),
        ];
        
        return view('instructor.quizzes.attempts', compact('quiz', 'attempts', 'stats'));
    }
    
    /**
     * Display all attempts across all quizzes for the instructor.
     */
    public function allAttempts(Request $request)
    {
        $instructorId = Auth::id();
        
        $query = QuizAttempt::whereHas('quiz.lesson.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })
        ->with(['user', 'quiz']);
        
        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }
        
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }
        
        if ($request->filled('result')) {
            $isPassed = $request->result === 'passed';
            $query->where('is_passed', $isPassed);
        }
        
        $attempts = $query->orderBy('created_at', 'desc')->paginate(25);
        
        // Statistiques globales
        $stats = [
            'total' => QuizAttempt::whereHas('quiz.lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->count(),
            'passed' => QuizAttempt::whereHas('quiz.lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->where('is_passed', true)->count(),
            'failed' => QuizAttempt::whereHas('quiz.lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->where('is_passed', false)->count(),
            'avg_score' => round(QuizAttempt::whereHas('quiz.lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->avg('score') ?? 0),
        ];
        
        $quizzes = Quiz::whereHas('lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->get();
        
        return view('instructor.quizzes.all-attempts', compact('attempts', 'stats', 'quizzes'));
    }
    
    /**
     * Display details of a specific attempt (AJAX).
     */
    public function show(QuizAttempt $attempt)
    {
        $this->authorizeAccess($attempt->quiz);
        
        $attempt->load(['user', 'quiz', 'answers.question.options']);
        
        // Si la requête est AJAX, retourner la vue partielle
        if (request()->ajax() || request()->wantsJson()) {
            return view('instructor.quizzes.partials.attempt-details', compact('attempt'));
        }
        
        return view('instructor.quizzes.attempt-show', compact('attempt'));
    }
    
    /**
     * Get attempt details as JSON (API).
     */
    public function details(QuizAttempt $attempt)
    {
        $this->authorizeAccess($attempt->quiz);
        
        $attempt->load(['user', 'answers.question.options']);
        
        $details = [
            'id' => $attempt->id,
            'student' => [
                'name' => $attempt->user->name,
                'email' => $attempt->user->email,
                'avatar' => $attempt->user->avatar,
            ],
            'score' => $attempt->score,
            'is_passed' => $attempt->is_passed,
            'time_spent' => $attempt->time_spent,
            'correct_answers' => $attempt->correct_answers,
            'total_questions' => $attempt->total_questions,
            'created_at' => $attempt->created_at->format('d/m/Y H:i'),
            'answers' => $attempt->answers->map(function ($answer) {
                return [
                    'question_text' => $answer->question->question_text,
                    'is_correct' => $answer->is_correct,
                    'points_earned' => $answer->points_earned,
                    'student_answer' => $answer->answer_text,
                    'correct_answer' => $answer->question->options->where('is_correct', true)->pluck('option_text')->implode(', '),
                    'explanation' => $answer->question->explanation,
                ];
            }),
        ];
        
        return response()->json($details);
    }
    
    /**
     * Delete an attempt.
     */
    public function destroy(QuizAttempt $attempt)
    {
        $this->authorizeAccess($attempt->quiz);
        
        $attempt->answers()->delete();
        $attempt->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tentative supprimée avec succès.'
        ]);
    }
    
    /**
     * Authorize access to the quiz.
     */
    private function authorizeAccess(Quiz $quiz): void
    {
        $instructorId = Auth::id();
        $courseInstructorId = $quiz->lesson->course->instructor_id;
        
        if ($courseInstructorId !== $instructorId && !Auth::user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé.');
        }
    }
}