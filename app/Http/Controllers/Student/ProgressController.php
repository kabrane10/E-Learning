<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    protected GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Marquer une leçon comme complétée (endpoint API)
     */
    public function markLessonComplete(Request $request, Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();
        
        $lessonCompletion = LessonCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ], [
            'enrollment_id' => $enrollment->id,
            'watched_duration' => $request->input('watched_duration', $lesson->duration),
        ]);

        // 🎮 GAMIFICATION
        if ($lessonCompletion->wasRecentlyCreated) {
            $this->gamificationService->addPoints($user, 'lesson_completed', $lesson);
            $this->gamificationService->updateStreak($user);
        }
        
        $this->updateCourseProgress($course, $user, $enrollment);
        
        return response()->json([
            'success' => true,
            'progress' => $enrollment->progress_percentage
        ]);
    }

    /**
     * Mettre à jour la progression vidéo
     */
    public function updateVideoProgress(Request $request, Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        $watchedDuration = $request->input('watched_duration', 0);
        
        $lessonCompletion = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();
            
        if ($lessonCompletion) {
            $lessonCompletion->update(['watched_duration' => $watchedDuration]);
        }
        
        // 🎮 GAMIFICATION : Mise à jour de l'activité
        $this->gamificationService->updateStreak($user);
        
        return response()->json(['success' => true]);
    }

    /**
     * Soumettre un quiz
     */
    public function submitQuiz(Request $request, Course $course, Quiz $quiz)
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();
            
        $validated = $request->validate([
            'answers' => 'required|array',
            'time_spent' => 'nullable|integer',
        ]);

        // Calculer le score
        $totalQuestions = $quiz->questions()->count();
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;
        
        foreach ($quiz->questions as $question) {
            $userAnswer = $validated['answers'][$question->id] ?? null;
            $isCorrect = $question->checkAnswer($userAnswer);
            
            if ($isCorrect) {
                $correctAnswers++;
                $earnedPoints += $question->points;
            }
            $totalPoints += $question->points;
        }
        
        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $isPassed = $score >= $quiz->passing_score;
        
        // Créer la tentative
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'time_spent' => $validated['time_spent'] ?? 0,
            'is_passed' => $isPassed,
            'started_at' => now()->subSeconds($validated['time_spent'] ?? 0),
            'completed_at' => now(),
        ]);

        // Sauvegarder les réponses
        foreach ($quiz->questions as $question) {
            $userAnswer = $validated['answers'][$question->id] ?? null;
            $isCorrect = $question->checkAnswer($userAnswer);
            
            $attempt->answers()->create([
                'question_id' => $question->id,
                'answer_data' => json_encode($userAnswer),
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ]);
        }

        // 🎮 GAMIFICATION : Points pour quiz réussi
        if ($isPassed) {
            $this->gamificationService->addPoints($user, 'quiz_passed', $quiz);
            
            // Bonus pour quiz parfait
            if ($score === 100) {
                $this->gamificationService->addPoints($user, 'perfect_quiz', $quiz);
            }
            
            // Vérifier si c'est le premier quiz
            $passedQuizzesCount = $user->quizAttempts()
                ->where('is_passed', true)
                ->count();
                
            if ($passedQuizzesCount === 1) {
                $this->gamificationService->addPoints($user, 'first_quiz', $quiz);
            }
            
            // Marquer la leçon comme complétée
            $lesson = $quiz->lesson;
            if ($lesson) {
                LessonCompletion::firstOrCreate([
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                ], [
                    'enrollment_id' => $enrollment->id,
                ]);
                
                $this->updateCourseProgress($course, $user, $enrollment);
            }
            
            // Vérifier les badges et achievements
            $this->gamificationService->checkBadges($user);
            $this->gamificationService->checkAchievements($user);
        }
        
        // Mise à jour du streak
        $this->gamificationService->updateStreak($user);
        
        return response()->json([
            'success' => true,
            'score' => $score,
            'is_passed' => $isPassed,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'points_earned' => $earnedPoints,
            'total_points' => $totalPoints,
        ]);
    }

    /**
     * Récupérer la progression d'un cours
     */
    public function getProgress(Course $course)
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if (!$enrollment) {
            return response()->json(['progress' => 0]);
        }
        
        $completedLessons = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->pluck('lesson_id');
            
        return response()->json([
            'progress' => $enrollment->progress_percentage,
            'completed_at' => $enrollment->completed_at,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $course->lessons()->count(),
        ]);
    }

    /**
     * Mettre à jour la progression globale d'un cours
     */
    private function updateCourseProgress(Course $course, $user, Enrollment $enrollment): void
    {
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();
        
        $progressPercentage = $totalLessons > 0 
            ? round(($completedLessons / $totalLessons) * 100) 
            : 0;
        
        $wasCompleted = $enrollment->completed_at !== null;
        
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'completed_at' => $progressPercentage === 100 ? now() : null,
        ]);

        // 🎮 GAMIFICATION : Cours complété
        if (!$wasCompleted && $progressPercentage === 100) {
            $this->gamificationService->addPoints($user, 'course_completed', $course);
            
            $completedCoursesCount = $user->enrolledCourses()
                ->whereNotNull('enrollments.completed_at')
                ->count();
                
            if ($completedCoursesCount === 1) {
                $this->gamificationService->addPoints($user, 'first_course', $course);
            }
        }
    }
}