<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
   
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $instructorId = Auth::id();
        
        $query = Quiz::whereHas('lesson.course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })
        ->with(['lesson.course'])
        ->withCount(['questions', 'attempts'])
        ->withAvg('attempts', 'score');
        
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('course_id')) {
            $query->whereHas('lesson.course', function ($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }
        
        if ($request->filled('status')) {
            $isPublished = $request->status === 'published';
            $query->where('is_published', $isPublished);
        }
        
        $quizzes = $query->latest()->paginate(15);
        
        $stats = [
            'total_quizzes' => Quiz::whereHas('lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->count(),
            'published_quizzes' => Quiz::whereHas('lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->where('is_published', true)->count(),
            'total_attempts' => Quiz::whereHas('lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->withCount('attempts')->get()->sum('attempts_count'),
            'total_questions' => Quiz::whereHas('lesson.course', fn($q) => $q->where('instructor_id', $instructorId))->withCount('questions')->get()->sum('questions_count'),
        ];
        
        $courses = Course::where('instructor_id', $instructorId)->get();
        
        return view('instructor.quizzes.index', compact('quizzes', 'stats', 'courses'));
    }
    
    /**
     * Show the form for creating a new quiz.
     * ✅ Paramètres optionnels : lesson_id, course_id
     */
    public function create(Request $request)
    {
        $instructorId = Auth::id();
        $courses = Course::where('instructor_id', $instructorId)->get();
        
        $selectedCourse = null;
        $selectedLesson = null;
        
        // Si un lesson_id est fourni
        if ($request->has('lesson_id')) {
            $selectedLesson = Lesson::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
                ->find($request->lesson_id);
            
            if ($selectedLesson) {
                $selectedCourse = $selectedLesson->course;
            }
        }
        
        // Si un course_id est fourni (sans leçon)
        if ($request->has('course_id') && !$selectedLesson) {
            $selectedCourse = Course::where('instructor_id', $instructorId)
                ->find($request->course_id);
        }
        
        return view('instructor.quizzes.create', compact('courses', 'selectedCourse', 'selectedLesson'));
    }
    
    /**
     * Store a newly created quiz.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lesson_id' => 'nullable|exists:lessons,id',
            'course_id' => 'nullable|exists:courses,id',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'show_results' => 'boolean',
        ]);
        
        // Si une leçon est spécifiée, l'utiliser
        if (!empty($validated['lesson_id'])) {
            $lesson = Lesson::findOrFail($validated['lesson_id']);
            $this->authorize('update', $lesson->course);
        }
        // Sinon, si un cours est spécifié, créer une leçon automatiquement
        elseif (!empty($validated['course_id'])) {
            $course = Course::findOrFail($validated['course_id']);
            $this->authorize('update', $course);
            
            // Créer une leçon de type quiz automatiquement
            $lesson = $course->lessons()->create([
                'title' => 'Quiz : ' . $validated['title'],
                'type' => 'quiz',
                'order' => $course->lessons()->max('order') + 1,
            ]);
        } else {
            return back()->with('error', 'Veuillez sélectionner un cours ou une leçon.');
        }
        
        // Créer le quiz
        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'passing_score' => $validated['passing_score'],
            'time_limit' => $validated['time_limit'] ?? null,
            'max_attempts' => $validated['max_attempts'] ?? null,
            'shuffle_questions' => $request->boolean('shuffle_questions', true),
            'show_results' => $request->boolean('show_results', true),
            'is_published' => false,
        ]);
        
        return redirect()->route('instructor.quizzes.edit', $quiz)
            ->with('success', 'Quiz créé avec succès ! Ajoutez maintenant des questions.');
    }

     /**
     * Create a quiz directly from a course (without an existing lesson).
     */
    public function createFromCourse(Course $course)
    {
        $this->authorize('update', $course);
        
        try {
            // Créer automatiquement une leçon de type quiz
            $lesson = $course->lessons()->create([
                'title' => 'Quiz - ' . now()->format('d/m/Y H:i'),
                'content_type' => 'quiz', // ✅ Obligatoire
                'order' => $course->lessons()->max('order') + 1,
            ]);
            
            
            // Rediriger vers la création du quiz pour cette leçon
            return redirect()->route('instructor.quizzes.create', ['lesson' => $lesson->id]);
            
        } catch (\Exception $e) {
            Log::error('Erreur création leçon automatique:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Erreur lors de la création de la leçon : ' . $e->getMessage());
        }
    }
    
    
    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->course);
        
        $quiz->load(['questions.options', 'lesson.course']);
        
        return view('instructor.quizzes.edit', compact('quiz'));
    }
    
    public function update(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'is_published' => 'boolean',
        ]);
        
        $quiz->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Quiz mis à jour'
        ]);
    }
    
    public function destroy(Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->course);
        
        $quiz->delete();
        
        return redirect()->route('instructor.quizzes.index')
            ->with('success', 'Quiz supprimé avec succès !');
    }
    
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->course);
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:single,multiple,true_false',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);
        
        $order = $quiz->questions()->max('order') + 1;
        
        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'explanation' => $validated['explanation'] ?? null,
            'order' => $order,
        ]);
        
        foreach ($validated['options'] as $index => $optionData) {
            $question->options()->create([
                'option_text' => $optionData['option_text'],
                'is_correct' => $optionData['is_correct'],
                'order' => $index,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'question' => $question->load('options'),
            'message' => 'Question ajoutée avec succès'
        ]);
    }
    
    public function updateQuestion(Request $request, Question $question)
    {
        $quiz = $question->quiz;
        $this->authorize('update', $quiz->lesson->course);
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:single,multiple,true_false',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.id' => 'nullable|exists:question_options,id',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);
        
        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'explanation' => $validated['explanation'] ?? null,
        ]);
        
        $keptOptionIds = collect($validated['options'])->pluck('id')->filter()->toArray();
        $question->options()->whereNotIn('id', $keptOptionIds)->delete();
        
        foreach ($validated['options'] as $index => $optionData) {
            if (isset($optionData['id'])) {
                $question->options()->where('id', $optionData['id'])->update([
                    'option_text' => $optionData['option_text'],
                    'is_correct' => $optionData['is_correct'],
                    'order' => $index,
                ]);
            } else {
                $question->options()->create([
                    'option_text' => $optionData['option_text'],
                    'is_correct' => $optionData['is_correct'],
                    'order' => $index,
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Question mise à jour'
        ]);
    }
    
    public function destroyQuestion(Question $question)
    {
        $quiz = $question->quiz;
        $this->authorize('update', $quiz->lesson->course);
        
        $question->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Question supprimée'
        ]);
    }
    
    public function reorderQuestions(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz->lesson->course);
        
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:questions,id',
            'questions.*.order' => 'required|integer',
        ]);
        
        foreach ($validated['questions'] as $questionData) {
            Question::where('id', $questionData['id'])
                ->where('quiz_id', $quiz->id)
                ->update(['order' => $questionData['order']]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function attempts(Quiz $quiz)
    {
        $this->authorize('view', $quiz->lesson->course);
        
        $attempts = $quiz->attempts()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total' => $quiz->attempts()->count(),
            'passed' => $quiz->attempts()->where('is_passed', true)->count(),
            'failed' => $quiz->attempts()->where('is_passed', false)->count(),
            'avg_score' => round($quiz->attempts()->avg('score') ?? 0),
        ];
        
        return view('instructor.quizzes.attempts', compact('quiz', 'attempts', 'stats'));
    }
    
   /**
 * Display statistics for a quiz.
 */
public function statistics(Quiz $quiz)
{
    $this->authorize('view', $quiz->lesson->course);
    
    // Charger les relations nécessaires
    $quiz->loadCount(['questions', 'attempts']);
    $quiz->load(['lesson.course']);
    
    // Statistiques générales
    $totalAttempts = $quiz->attempts()->count();
    $passedCount = $quiz->attempts()->where('is_passed', true)->count();
    $failedCount = $totalAttempts - $passedCount;
    $averageScore = $quiz->attempts()->avg('score') ?? 0;
    $successRate = $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100) : 0;
    
    // Distribution des scores
    $scoreDistribution = $this->getScoreDistribution($quiz);
    
    // Questions les plus échouées
    $mostFailedQuestions = $this->getMostFailedQuestions($quiz);
    
    // Détail par question
    $questions = $this->getQuestionDetails($quiz);
    
    return view('instructor.quizzes.statistics', compact(
        'quiz',
        'totalAttempts',
        'passedCount',
        'failedCount',
        'averageScore',
        'successRate',
        'scoreDistribution',
        'mostFailedQuestions',
        'questions'
    ));
}

/**
 * Get score distribution.
 */
private function getScoreDistribution(Quiz $quiz): array
{
    $ranges = [
        ['min' => 0, 'max' => 20, 'label' => '0-20%'],
        ['min' => 21, 'max' => 40, 'label' => '21-40%'],
        ['min' => 41, 'max' => 60, 'label' => '41-60%'],
        ['min' => 61, 'max' => 80, 'label' => '61-80%'],
        ['min' => 81, 'max' => 100, 'label' => '81-100%'],
    ];
    
    $labels = [];
    $data = [];
    
    foreach ($ranges as $range) {
        $labels[] = $range['label'];
        $data[] = $quiz->attempts()
            ->where('score', '>=', $range['min'])
            ->where('score', '<=', $range['max'])
            ->count();
    }
    
    return ['labels' => $labels, 'data' => $data];
}

/**
 * Get most failed questions.
 */
private function getMostFailedQuestions(Quiz $quiz)
{
    return $quiz->questions()
        ->withCount(['answers as total_answers'])
        ->withCount(['answers as correct_answers' => function ($q) {
            $q->where('is_correct', true);
        }])
        ->get()
        ->map(function ($question) {
            $question->fail_rate = $question->total_answers > 0 
                ? round((($question->total_answers - $question->correct_answers) / $question->total_answers) * 100) 
                : 0;
            return $question;
        })
        ->sortByDesc('fail_rate')
        ->take(5);
}

/**
 * Get detailed stats for each question.
 */
private function getQuestionDetails(Quiz $quiz)
{
    return $quiz->questions()
        ->withCount(['answers as total_answers'])
        ->withCount(['answers as correct_answers' => function ($q) {
            $q->where('is_correct', true);
        }])
        ->withAvg('answers', 'time_spent')
        ->get()
        ->map(function ($question) {
            $question->success_rate = $question->total_answers > 0 
                ? round(($question->correct_answers / $question->total_answers) * 100) 
                : 0;
            $question->avg_time = round($question->answers_avg_time_spent ?? 0);
            return $question;
        });
}

    
}