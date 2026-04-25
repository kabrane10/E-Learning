<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Course::class, 'course');
    }
    
    public function index(Request $request)
    {
        $query = Course::where('instructor_id', Auth::id())
            ->withCount(['students', 'lessons', 'reviews'])
            ->withAvg('reviews', 'rating');
        
        // Filtres
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        
        if ($request->filled('status')) {
            $isPublished = $request->status === 'published';
            $query->where('is_published', $isPublished);
        }
        
        $courses = $query->latest()->paginate(12);
        
        // Ajouter des attributs calculés
        $courses->getCollection()->transform(function ($course) {
            // Calculer le taux de complétion
            if ($course->students_count > 0) {
                $completed = $course->students()
                    ->whereNotNull('enrollments.completed_at')
                    ->count();
                $course->completion_rate = round(($completed / $course->students_count) * 100);
            } else {
                $course->completion_rate = 0;
            }
            
            // Ajouter l'URL du thumbnail
            $course->thumbnail_url = $course->getFirstMediaUrl('thumbnail') ?: null;
            
            return $course;
        });
        
        return view('instructor.courses.index', compact('courses'));
    }
    
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('instructor.courses.create', compact('categories'));
    }
    
    /**
     * Store a newly created course.
     */
     public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'category' => 'required|string|max:100',
                'level' => 'required|in:beginner,intermediate,advanced',
                'is_free' => 'nullable|boolean',
                'price' => 'nullable|numeric|min:0',
                'short_description' => 'required|string|max:200',
                'description' => 'required|string',
                'learning_outcomes' => 'nullable|string',
                'prerequisites' => 'nullable|string',
                'target_audience' => 'nullable|string',
                'thumbnail' => 'nullable|image|max:5120',
                'promo_video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:512000',
                'resources.*' => 'nullable|file|max:51200',
                'publish_action' => 'nullable|in:publish,draft,schedule',
            ]);
            
            // Décoder les JSON
            $learningOutcomes = json_decode($validated['learning_outcomes'] ?? '[]', true) ?: [];
            $prerequisites = json_decode($validated['prerequisites'] ?? '[]', true) ?: [];
            $learningOutcomes = array_values(array_filter($learningOutcomes, fn($item) => !empty(trim($item))));
            $prerequisites = array_values(array_filter($prerequisites, fn($item) => !empty(trim($item))));
            
            // ✅ CRÉATION DU COURS
            $course = Course::create([
                'instructor_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
                'category' => $validated['category'],
                'level' => $validated['level'],
                'is_free' => $request->boolean('is_free', true),
                'price' => $request->boolean('is_free') ? 0 : ($validated['price'] ?? 0),
                'short_description' => $validated['short_description'],
                'description' => $validated['description'],
                'learning_outcomes' => $learningOutcomes,
                'prerequisites' => $prerequisites,
                'target_audience' => $validated['target_audience'] ?? null,
                'is_published' => $validated['publish_action'] === 'publish',
            ]);
            
            // Gestion des médias
            if ($request->hasFile('thumbnail')) {
                $course->addMedia($request->file('thumbnail'))->toMediaCollection('thumbnail');
            }
            
            if ($request->hasFile('promo_video')) {
                $course->addMedia($request->file('promo_video'))->toMediaCollection('promo_video');
            }
            
            if ($request->hasFile('resources')) {
                foreach ($request->file('resources') as $resource) {
                    $course->addMedia($resource)->toMediaCollection('resources');
                }
            }
            
            $message = $course->is_published 
                ? 'Cours publié avec succès !' 
                : 'Cours enregistré comme brouillon !';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('instructor.courses.show', $course),
                    'course_id' => $course->id
                ]);
            }
            
            return redirect()->route('instructor.courses.show', $course)
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur serveur : ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage())
                ->withInput();
        }
    }


    public function show(Course $course)
    {
        $this->authorize('view', $course);
        
        $course->load(['chapters.lessons']);

        
        
        return view('instructor.courses.show', compact('course'));
    }
    
    
public function edit(Course $course)
{
    $this->authorize('update', $course);
    
    // ✅ S'assurer que les champs JSON sont des tableaux
    $course->learning_outcomes = $course->learning_outcomes ?? [];
    $course->prerequisites = $course->prerequisites ?? [];
    
    return view('instructor.courses.edit', compact('course'));
}
    
     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        
        \Log::info('Update course - Données reçues:', $request->all());
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'category' => 'required|string|max:100',
                'level' => 'required|in:beginner,intermediate,advanced',
                'is_free' => 'nullable|boolean',
                'price' => 'nullable|numeric|min:0',
                'short_description' => 'required|string|max:200',
                'description' => 'required|string',
                'learning_outcomes' => 'nullable|string',
                'prerequisites' => 'nullable|string',
                'target_audience' => 'nullable|string',
                'is_published' => 'nullable|boolean',
            ]);
            
            \Log::info('Données validées:', $validated);
            
            // Décoder les JSON
            $learningOutcomes = json_decode($validated['learning_outcomes'] ?? '[]', true) ?: [];
            $prerequisites = json_decode($validated['prerequisites'] ?? '[]', true) ?: [];
            
            // Filtrer les valeurs vides
            $learningOutcomes = array_filter($learningOutcomes, fn($item) => !empty(trim($item)));
            $prerequisites = array_filter($prerequisites, fn($item) => !empty(trim($item)));
            
            $course->update([
                'title' => $validated['title'],
                'category' => $validated['category'],
                'level' => $validated['level'],
                'is_free' => $request->boolean('is_free', true),
                'price' => $request->boolean('is_free') ? 0 : ($validated['price'] ?? 0),
                'short_description' => $validated['short_description'],
                'description' => $validated['description'],
                'learning_outcomes' => $learningOutcomes,
                'prerequisites' => $prerequisites,
                'target_audience' => $validated['target_audience'] ?? null,
                'is_published' => $request->boolean('is_published', false),
            ]);
            
            \Log::info('Cours mis à jour:', ['id' => $course->id]);
            
            // Gestion de l'image
            if ($request->hasFile('thumbnail')) {
                $course->clearMediaCollection('thumbnail');
                $course->addMedia($request->file('thumbnail'))->toMediaCollection('thumbnail');
                \Log::info('Image mise à jour');
            }
            
            // Gestion de la vidéo
            if ($request->hasFile('promo_video')) {
                $course->clearMediaCollection('promo_video');
                $course->addMedia($request->file('promo_video'))->toMediaCollection('promo_video');
                \Log::info('Vidéo mise à jour');
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cours mis à jour avec succès !',
                    'redirect' => route('instructor.courses.show', $course)
                ]);
            }
            
            return redirect()->route('instructor.courses.show', $course)
                ->with('success', 'Cours mis à jour avec succès !');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation:', $e->errors());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $e->errors()
                ], 422);
            }
            
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du cours: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue : ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }
    
     /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        
        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Cours supprimé avec succès !');
    }
    
    public function togglePublish(Course $course)
    {
        $this->authorize('update', $course);
        
        $course->update(['is_published' => !$course->is_published]);
        
        return back()->with('success', $course->is_published ? 'Cours publié !' : 'Cours dépublié !');
    }
    
    public function duplicate(Course $course)
    {
        $this->authorize('update', $course);
        
        $newCourse = $course->replicate();
        $newCourse->title = $course->title . ' (Copie)';
        $newCourse->slug = Str::slug($newCourse->title) . '-' . Str::random(6);
        $newCourse->is_published = false;
        $newCourse->created_at = now();
        $newCourse->updated_at = now();
        $newCourse->save();
        
        // Dupliquer les chapitres et leçons
        foreach ($course->chapters as $chapter) {
            $newChapter = $chapter->replicate();
            $newChapter->course_id = $newCourse->id;
            $newChapter->save();
            
            foreach ($chapter->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->course_id = $newCourse->id;
                $newLesson->chapter_id = $newChapter->id;
                $newLesson->save();
            }
        }
        
        return redirect()->route('instructor.courses.show', $newCourse)
            ->with('success', 'Cours dupliqué avec succès !');
    }
    
     /**
     * Display course analytics.
     * Cette méthode gère à la fois la vue et l'API.
     */
    public function analytics(Course $course, Request $request)
    {
        $this->authorize('view', $course);
        
        // ✅ Si c'est une requête API (JSON), retourner les données
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->getAnalyticsData($course, $request);
        }
        
        // ✅ Sinon, retourner la vue normale
        return view('instructor.courses.analytics', compact('course'));
    }
    
    /**
     * Get analytics data for API.
     */
    private function getAnalyticsData(Course $course, Request $request)
    {
        $period = $request->get('period', '30');
        $days = $period === 'all' ? 365 : (int) $period;
        
        // Statistiques générales
        $totalStudents = $course->students()->count();
        $newThisMonth = $course->students()
            ->where('enrollments.created_at', '>=', now()->startOfMonth())
            ->count();
        
        $completionRate = $this->calculateCourseCompletionRate($course);
        $avgRating = round($course->reviews()->avg('rating') ?? 0, 1);
        $reviewsCount = $course->reviews()->count();
        
        // Revenus
        $revenue = 0;
        if (!$course->is_free) {
            $revenue = $course->enrollments()->count() * $course->price * 0.8;
        }
        
        // Taux d'engagement
        $activeStudents = $course->students()
            ->where('enrollments.last_activity_at', '>=', now()->subDays(7))
            ->count();
        $engagementRate = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100) : 0;
        
        // Données des leçons
        $lessons = $course->lessons()
            ->withCount(['completions as completed_count'])
            ->get()
            ->map(function ($lesson) use ($totalStudents) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'type' => $lesson->type ?? 'video',
                    'completion_rate' => $totalStudents > 0 
                        ? round(($lesson->completed_count / $totalStudents) * 100) 
                        : 0,
                    'completed_count' => $lesson->completed_count ?? 0,
                    'total_students' => $totalStudents,
                    'avg_watch_time' => $lesson->completions()->avg('watched_duration') ?? 0,
                ];
            });
        
        // Données pour les graphiques
        $enrollmentsData = $this->getEnrollmentsChartData($course, $days);
        
        // ✅ S'assurer que les données ne sont pas vides
        if (empty($enrollmentsData['labels'])) {
            $enrollmentsData = [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [0, 0, 0, 0, 0, 0, 0],
            ];
        }
        
        return response()->json([
            'success' => true,
            'currency_symbol' => 'FCFA',
            'stats' => [
                'total_students' => $totalStudents,
                'new_this_month' => $newThisMonth,
                'completion_rate' => $completionRate,
                'avg_rating' => $avgRating,
                'reviews' => $reviewsCount,
                'revenue' => $revenue,
                'engagement_rate' => $engagementRate,
            ],
            'lessons' => $lessons,
            'charts' => [
                'enrollments' => $enrollmentsData,
            ],
        ]);
    }
    
    /**
     * Calculate course completion rate.
     */
    private function calculateCourseCompletionRate(Course $course): float
    {
        $total = $course->students()->count();
        if ($total === 0) return 0;
        
        $completed = $course->students()
            ->whereNotNull('enrollments.completed_at')
            ->count();
            
        return round(($completed / $total) * 100);
    }
    
    /**
     * Get enrollments chart data.
     */
    private function getEnrollmentsChartData(Course $course, int $days): array
    {
        $data = $course->enrollments()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $labels = [];
        $values = [];
        
        // Remplir les dates manquantes avec 0
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');
            $values[] = $data->firstWhere('date', $date) instanceof \stdClass 
                ? (int) $data->firstWhere('date', $date)->count 
                : 0;
        }
        
        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

   /**
     * Display students enrolled in the course.
     */
    public function students(Request $request, Course $course)
    {
        $this->authorize('view', $course);
        
        // Requête de base
        $query = $course->students()
            ->withPivot('progress_percentage', 'enrolled_at', 'completed_at', 'last_activity_at');
        
        // Filtre de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('enrollments.completed_at');
            } elseif ($request->status === 'in_progress') {
                $query->whereNull('enrollments.completed_at')
                      ->where('enrollments.progress_percentage', '>', 0);
            } elseif ($request->status === 'not_started') {
                $query->whereNull('enrollments.completed_at')
                      ->where('enrollments.progress_percentage', 0);
            }
        }
        
        // Tri
        switch ($request->get('sort', 'recent')) {
            case 'progress_desc':
                $query->orderBy('enrollments.progress_percentage', 'desc');
                break;
            case 'progress_asc':
                $query->orderBy('enrollments.progress_percentage', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('enrollments.created_at', 'desc');
        }
        
        $students = $query->paginate(15);
        
        // Statistiques
        $totalStudents = $course->students()->count();
        $completedStudents = $course->students()->whereNotNull('enrollments.completed_at')->count();
        $activeStudents = $course->students()
            ->whereNull('enrollments.completed_at')
            ->where('enrollments.progress_percentage', '>', 0)
            ->count();
        $averageProgress = $course->students()->avg('enrollments.progress_percentage') ?? 0;
        
        $stats = [
            'total_students' => $totalStudents,
            'completed_students' => $completedStudents,
            'active_students' => $activeStudents,
            'average_progress' => $averageProgress,
        ];
        
        // Export si demandé
        if ($request->has('export')) {
            return $this->exportStudents($course, $request->get('export'));
        }
        
        return view('instructor.courses.students', compact('course', 'students', 'stats'));
    }
    
    /**
     * Remove a student from the course.
     */
    public function removeStudent(Course $course, $studentId)
    {
        $this->authorize('update', $course);
        
        $course->students()->detach($studentId);
        
        return back()->with('success', 'Étudiant retiré du cours avec succès.');
    }
    
    /**
     * Export students list.
     */
    private function exportStudents(Course $course, string $format)
    {
        $students = $course->students()
            ->withPivot('progress_percentage', 'enrolled_at', 'completed_at')
            ->get();
        
        $filename = 'etudiants-' . Str::slug($course->title) . '-' . date('Y-m-d');
        
        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ];
            
            $callback = function() use ($students) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Nom', 'Email', 'Progression (%)', 'Statut', 'Inscrit le', 'Terminé le']);
                
                foreach ($students as $student) {
                    fputcsv($file, [
                        $student->name,
                        $student->email,
                        $student->pivot->progress_percentage ?? 0,
                        $student->pivot->completed_at ? 'Terminé' : 'En cours',
                        $student->pivot->enrolled_at ? $student->pivot->enrolled_at->format('d/m/Y') : '-',
                        $student->pivot->completed_at ? $student->pivot->completed_at->format('d/m/Y') : '-',
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        // Export Excel (simplifié)
        return back()->with('info', 'Export Excel à implémenter');
    }
   
    
}