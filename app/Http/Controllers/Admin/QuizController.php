<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        return view('admin.quizzes.index');
    }

    public function show(Quiz $quiz)
    {
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function statistics(Quiz $quiz)
    {
        return view('admin.quizzes.statistics', compact('quiz'));
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz supprimé');
    }

    public function bulkAction(Request $request)
    {
        return back()->with('success', 'Action groupée effectuée');
    }
}