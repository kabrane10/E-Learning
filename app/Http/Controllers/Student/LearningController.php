<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    protected GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Afficher la page d'apprentissage d'un cours
     */
    public function index(Course $course)
    {
        $user = Auth::user();
        
        // Vérifier l'inscription
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();
            
        $course->load(['chapters.lessons']);
        
        // Récupérer les leçons complétées
        $completedLessons = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();
        
        // Trouver la première leçon non complétée
        $currentLesson = null;
        foreach ($course->chapters as $chapter) {
            foreach ($chapter->lessons as $lesson) {
                if (!in_array($lesson->id, $completedLessons)) {
                    $currentLesson = $lesson;
                    break 2;
                }
            }
        }
        
        // Si toutes les leçons sont complétées, prendre la première
        if (!$currentLesson && $course->lessons->count() > 0) {
            $currentLesson = $course->lessons->first();
        }
        
        if ($currentLesson) {
            return redirect()->route('student.learn.lesson', [$course, $currentLesson]);
        }
        
        return view('student.learning.index', compact('course', 'enrollment', 'completedLessons'));
    }

    /**
     * Afficher une leçon spécifique
     */
    public function show(Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        
        // Vérifier l'inscription
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();
            
        // Charger tout le curriculum
        $course->load(['chapters.lessons']);
        
        // Récupérer les leçons complétées
        $completedLessons = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();
        
        // Trouver la leçon précédente et suivante
        $lessons = $course->lessons;
        $currentIndex = $lessons->search(function($l) use ($lesson) {
            return $l->id === $lesson->id;
        });
        
        $previousLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;
        
        // Vérifier si la leçon actuelle est complétée
        $isCompleted = in_array($lesson->id, $completedLessons);
        
        return view('student.learning.show', compact(
            'course',
            'lesson',
            'enrollment',
            'completedLessons',
            'previousLesson',
            'nextLesson',
            'isCompleted'
        ));
    }

    /**
     * Marquer une leçon comme complétée
     */
    public function markComplete(Request $request, Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        
        // Vérifier l'inscription
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();
        
        // Marquer la leçon comme complétée
        $lessonCompletion = LessonCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ], [
            'enrollment_id' => $enrollment->id,
            'watched_duration' => $request->input('watched_duration', $lesson->duration),
        ]);

        // 🎮 GAMIFICATION : Points pour leçon complétée
        if ($lessonCompletion->wasRecentlyCreated) {
            $this->gamificationService->addPoints($user, 'lesson_completed', $lesson);
            $this->gamificationService->updateStreak($user);
        }
        
        // Recalculer la progression du cours
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();
        
        $progressPercentage = $totalLessons > 0 
            ? round(($completedLessons / $totalLessons) * 100) 
            : 0;
        
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'completed_at' => $progressPercentage === 100 ? now() : null,
        ]);

        // 🎮 GAMIFICATION : Cours complété à 100%
        if ($progressPercentage === 100 && $enrollment->wasChanged('completed_at')) {
            $this->gamificationService->addPoints($user, 'course_completed', $course);
            
            // Vérifier si c'est le premier cours
            $completedCoursesCount = $user->enrolledCourses()
                ->whereNotNull('enrollments.completed_at')
                ->count();
                
            if ($completedCoursesCount === 1) {
                $this->gamificationService->addPoints($user, 'first_course', $course);
            }
            
            // Vérifier les badges et achievements
            $this->gamificationService->checkBadges($user);
            $this->gamificationService->checkAchievements($user);
        }
        
        return response()->json([
            'success' => true,
            'progress' => $progressPercentage,
            'is_completed' => $progressPercentage === 100,
            'message' => $progressPercentage === 100 
                ? 'Félicitations ! Vous avez terminé ce cours !' 
                : 'Progression mise à jour'
        ]);
    }
}