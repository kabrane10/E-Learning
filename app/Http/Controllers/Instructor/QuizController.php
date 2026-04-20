<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function create(Lesson $lesson)
    {
        $this->authorizeInstructor($lesson->course);
        
        $quiz = $lesson->quiz;
        
        if ($quiz) {
            return redirect()->route('instructor.quizzes.edit', $quiz);
        }
        
        return view('instructor.quizzes.create', compact('lesson'));
    }

    public function store(Request $request, Lesson $lesson)
    {
        $this->authorizeInstructor($lesson->course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
        ]);

        $quiz = $lesson->quiz()->create($validated);
        
        return redirect()->route('instructor.quizzes.edit', $quiz)
            ->with('success', 'Quiz créé avec succès !');
    }

    public function edit(Quiz $quiz)
    {
        $this->authorizeInstructor($quiz->lesson->course);
        
        $quiz->load(['questions.options']);
        
        return view('instructor.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authorizeInstructor($quiz->lesson->course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
        ]);

        $quiz->update($validated);
        
        return back()->with('success', 'Quiz mis à jour !');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $this->authorizeInstructor($quiz->lesson->course);
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:single,multiple,true_false',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);

        $order = $quiz->questions()->max('order') + 1;
        
        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'explanation' => $validated['explanation'],
            'points' => $validated['points'],
            'order' => $order,
        ]);

        foreach ($validated['options'] as $index => $optionData) {
            $question->options()->create([
                'option_text' => $optionData['text'],
                'is_correct' => $optionData['is_correct'] ?? false,
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
        $this->authorizeInstructor($question->quiz->lesson->course);
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:single,multiple,true_false',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'options' => 'required|array|min:2',
            'options.*.id' => 'nullable|exists:quiz_options,id',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'explanation' => $validated['explanation'],
            'points' => $validated['points'],
        ]);

        // Supprimer les options qui ne sont plus présentes
        $keptOptionIds = collect($validated['options'])
            ->pluck('id')
            ->filter()
            ->toArray();
            
        $question->options()->whereNotIn('id', $keptOptionIds)->delete();

        // Mettre à jour ou créer les options
        foreach ($validated['options'] as $index => $optionData) {
            if (isset($optionData['id'])) {
                $option = QuizOption::find($optionData['id']);
                $option->update([
                    'option_text' => $optionData['text'],
                    'is_correct' => $optionData['is_correct'],
                    'order' => $index,
                ]);
            } else {
                $question->options()->create([
                    'option_text' => $optionData['text'],
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
        $this->authorizeInstructor($question->quiz->lesson->course);
        
        $question->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Question supprimée'
        ]);
    }

    public function reorderQuestions(Request $request, Quiz $quiz)
    {
        $this->authorizeInstructor($quiz->lesson->course);
        
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

    private function authorizeInstructor(Course $course)
    {
        if ($course->instructor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }
    }
}