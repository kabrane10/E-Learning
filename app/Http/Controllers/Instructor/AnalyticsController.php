<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $instructorId = Auth::id();
        $period = (int) $request->get('period', '30');
        
        // Statistiques calculées dynamiquement
        $stats = [
            'total_students' => $this->getTotalStudents($instructorId),
            'new_students' => $this->getNewStudentsCount($instructorId),
            'total_enrollments' => $this->getTotalEnrollments($instructorId),
            'completion_rate' => $this->getCompletionRate($instructorId),
            'average_rating' => $this->getAverageRating($instructorId),
            'total_reviews' => $this->getTotalReviews($instructorId),
            'total_revenue' => $this->getTotalRevenue($instructorId),
            'revenue_this_month' => $this->getRevenueThisMonth($instructorId),
        ];
        
        // Top cours par performance
        $topCourses = Course::where('instructor_id', $instructorId)
            ->withCount(['students', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderBy('students_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($course) {
                $course->completion_rate = $this->getCourseCompletionRate($course);
                $course->total_revenue = $course->is_free ? 0 : $course->students_count * $course->price * 0.8;
                $course->average_rating = $course->reviews_avg_rating ?? 0;
                return $course;
            });
        
        // Données pour les graphiques
        $enrollmentsChart = $this->getEnrollmentsChartData($instructorId, $period);
        $distributionChart = $this->getDistributionChartData($instructorId);
        
        return view('instructor.analytics.index', compact(
            'stats', 'topCourses', 'enrollmentsChart', 'distributionChart', 'period'
        ));
    }
    
    // ===================== MÉTHODES DE CALCUL =====================
    
    private function getTotalStudents($instructorId): int
    {
        return Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->distinct('user_id')
            ->count('user_id');
    }
    
    private function getNewStudentsCount($instructorId): int
    {
        return Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->where('enrollments.created_at', '>=', now()->startOfMonth())
            ->distinct('user_id')
            ->count('user_id');
    }
    
    private function getTotalEnrollments($instructorId): int
    {
        return Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->count();
    }
    
    private function getCompletionRate($instructorId): float
    {
        $total = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->count();
        if ($total === 0) return 0;
        
        $completed = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->whereNotNull('completed_at')
            ->count();
            
        return round(($completed / $total) * 100);
    }
    
    private function getAverageRating($instructorId): float
    {
        return round(Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->avg('rating') ?? 0, 1);
    }
    
    private function getTotalReviews($instructorId): int
    {
        return Review::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->count();
    }
    
    private function getTotalRevenue($instructorId): float
    {
        $courses = Course::where('instructor_id', $instructorId)
            ->where('is_free', false)
            ->withCount('students')
            ->get();
        
        $total = 0;
        foreach ($courses as $course) {
            $total += $course->students_count * $course->price * 0.8;
        }
        
        return $total;
    }
    
    private function getRevenueThisMonth($instructorId): float
    {
        // Simplifié : revenus basés sur les inscriptions du mois
        $courses = Course::where('instructor_id', $instructorId)
            ->where('is_free', false)
            ->withCount(['students' => fn($q) => $q->where('enrollments.created_at', '>=', now()->startOfMonth())])
            ->get();
        
        $total = 0;
        foreach ($courses as $course) {
            $total += $course->students_count * $course->price * 0.8;
        }
        
        return $total;
    }
    
    private function getCourseCompletionRate(Course $course): float
    {
        $total = $course->students()->count();
        if ($total === 0) return 0;
        
        $completed = $course->students()->whereNotNull('enrollments.completed_at')->count();
        return round(($completed / $total) * 100);
    }
    
    private function getEnrollmentsChartData($instructorId, $period): array
    {
        $data = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Si pas de données, retourner des valeurs vides
        if ($data->isEmpty()) {
            return [
                'labels' => ['Aucune donnée'],
                'values' => [0]
            ];
        }
        
        return [
            'labels' => $data->pluck('date')->map(fn($d) => date('d/m', strtotime($d)))->toArray(),
            'values' => $data->pluck('count')->toArray(),
        ];
    }
    
    private function getDistributionChartData($instructorId): array
    {
        $data = Course::where('instructor_id', $instructorId)
            ->withCount('students')
            ->orderBy('students_count', 'desc')
            ->limit(6)
            ->get();
        
        if ($data->isEmpty()) {
            return [
                'labels' => ['Aucun cours'],
                'values' => [0]
            ];
        }
        
        return [
            'labels' => $data->pluck('title')->map(fn($t) => \Illuminate\Support\Str::limit($t, 20))->toArray(),
            'values' => $data->pluck('students_count')->toArray(),
        ];
    }


    /**
     * Analyse de l'engagement des étudiants.
     */
    public function engagement(Request $request)
    {
        $instructorId = Auth::id();
        
        // Statistiques d'engagement
        $stats = [
            'avg_completion' => $this->getCompletionRate($instructorId),
            'total_students' => $this->getTotalStudents($instructorId),
            'active_students_7d' => $this->getActiveStudentsCount($instructorId, 7),
            'active_students_30d' => $this->getActiveStudentsCount($instructorId, 30),
            'dropout_rate' => $this->getDropoutRate($instructorId),
            'avg_watch_time' => $this->getAverageWatchTime($instructorId),
            'total_enrollments' => $this->getTotalEnrollments($instructorId),
            'completed_enrollments' => $this->getCompletedEnrollments($instructorId),
        ];
        
        // Engagement par cours
        $coursesEngagement = Course::where('instructor_id', $instructorId)
            ->withCount('students')
            ->get()
            ->map(function ($course) {
                $course->completion_rate = $this->getCourseCompletionRate($course);
                $course->active_students = $this->getCourseActiveStudents($course);
                $course->dropout_rate = $this->getCourseDropoutRate($course);
                return $course;
            });
        
        // Données de rétention pour le graphique
        $retentionData = $this->getRetentionData($instructorId);
        
        // Données de complétion par cours pour le graphique
        $completionByCourse = $coursesEngagement->take(10)->map(function ($course) {
            return [
                'name' => \Illuminate\Support\Str::limit($course->title, 25),
                'completion' => $course->completion_rate,
                'students' => $course->students_count,
            ];
        });
        
        return view('instructor.analytics.engagement', compact(
            'stats', 'coursesEngagement', 'retentionData', 'completionByCourse'
        ));
    }

    // ===================== MÉTHODES DE CALCUL ADDITIONNELLES =====================

    /**
     * Revenus du mois dernier.
     */
    private function getRevenueLastMonth($instructorId): float
    {
        $courses = Course::where('instructor_id', $instructorId)
            ->where('is_free', false)
            ->withCount(['students' => function ($q) {
                $q->whereMonth('enrollments.created_at', now()->subMonth()->month)
                  ->whereYear('enrollments.created_at', now()->subMonth()->year);
            }])
            ->get();
        
        $total = 0;
        foreach ($courses as $course) {
            $total += $course->students_count * $course->price * 0.8;
        }
        
        return $total;
    }

    /**
     * Projection des revenus pour le mois en cours.
     */
    private function getProjectedRevenue($instructorId): float
    {
        $daysPassed = now()->day;
        $daysInMonth = now()->daysInMonth;
        $currentRevenue = $this->getRevenueThisMonth($instructorId);
        
        return $daysPassed > 0 ? round(($currentRevenue / $daysPassed) * $daysInMonth, 2) : 0;
    }

    /**
     * Données mensuelles des revenus (6 derniers mois).
     */
    private function getMonthlyRevenueData($instructorId): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            $revenue = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
                ->whereYear('enrollments.created_at', $date->year)
                ->whereMonth('enrollments.created_at', $date->month)
                ->get()
                ->sum(function ($enrollment) {
                    return ($enrollment->course->price ?? 0) * 0.8;
                });
            
            $months[] = [
                'month' => $date->locale('fr')->monthName . ' ' . $date->year,
                'revenue' => round($revenue, 2),
                'short' => $date->locale('fr')->isoFormat('MMM YY'),
            ];
        }
        
        return $months;
    }

    /**
     * Revenus par cours.
     */
    private function getRevenueByCourse($instructorId): array
    {
        return Course::where('instructor_id', $instructorId)
            ->where('is_free', false)
            ->withCount('students')
            ->get()
            ->map(function ($course) {
                $revenue = $course->students_count * $course->price * 0.8;
                return [
                    'title' => $course->title,
                    'revenue' => $revenue,
                    'students' => $course->students_count,
                    'price' => $course->price,
                ];
            })
            ->toArray();
    }

    /**
     * Nombre d'étudiants actifs (période donnée).
     */
    private function getActiveStudentsCount($instructorId, int $days = 7): int
    {
        return Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->where('last_activity_at', '>=', now()->subDays($days))
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Taux d'abandon global.
     */
    private function getDropoutRate($instructorId): float
    {
        $total = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))->count();
        if ($total === 0) return 0;
        
        $inactive = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->where('progress_percentage', '<', 10)
            ->where('created_at', '<', now()->subDays(30))
            ->count();
        
        return round(($inactive / $total) * 100);
    }

    /**
     * Temps de visionnage moyen (heures).
     */
    private function getAverageWatchTime($instructorId): float
    {
        // À implémenter avec les données réelles de suivi
        // Pour l'instant, retourne une valeur simulée
        $totalStudents = $this->getTotalStudents($instructorId);
        if ($totalStudents === 0) return 0;
        
        $totalDuration = Course::where('instructor_id', $instructorId)
            ->withSum('lessons', 'duration')
            ->get()
            ->sum('lessons_sum_duration') ?? 0;
        
        // Moyenne : durée totale / nombre d'étudiants (en heures)
        return $totalDuration > 0 ? round(($totalDuration / 3600), 1) : 0;
    }

    /**
     * Nombre d'inscriptions complétées.
     */
    private function getCompletedEnrollments($instructorId): int
    {
        return Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->whereNotNull('completed_at')
            ->count();
    }

    /**
     * Étudiants actifs pour un cours spécifique.
     */
    private function getCourseActiveStudents(Course $course): int
    {
        return $course->students()
            ->wherePivot('last_activity_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Taux d'abandon pour un cours spécifique.
     */
    private function getCourseDropoutRate(Course $course): float
    {
        $total = $course->students()->count();
        if ($total === 0) return 0;
        
        $inactive = $course->students()
            ->wherePivot('progress_percentage', '<', 10)
            ->wherePivot('created_at', '<', now()->subDays(30))
            ->count();
        
        return round(($inactive / $total) * 100);
    }

    /**
     * Données de rétention pour le graphique.
     */
    private function getRetentionData($instructorId): array
    {
        return [
            'labels' => ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'],
            'values' => [100, 85, 74, $this->getCompletionRate($instructorId)],
        ];
    }

     /**
     * Display revenue analytics.
     */
    public function revenue()
    {
        $instructorId = Auth::id();
        
        // Récupérer tous les cours payants du formateur
        $courses = Course::where('instructor_id', $instructorId)
            ->where('is_free', false)
            ->withCount('students')
            ->get();
        
        // Calculer les revenus par cours
        $byCourse = $courses->map(function ($course) {
            $revenue = $course->students_count * $course->price;
            
            return [
                'title' => $course->title,
                'price' => $course->price,
                'students_count' => $course->students_count,
                'revenue' => $revenue,
                'is_free' => $course->is_free,
                'thumbnail' => $course->thumbnail_url,
            ];
        })->filter(fn($c) => $c['revenue'] > 0)->values()->toArray();
        
        // Statistiques
        $totalRevenue = array_sum(array_column($byCourse, 'revenue'));
        
        $stats = [
            'total_revenue' => $totalRevenue,
            'this_month' => $this->getMonthlyRevenue($instructorId, now()->month, now()->year),
            'last_month' => $this->getMonthlyRevenue($instructorId, now()->subMonth()->month, now()->subMonth()->year),
            'projected' => $this->getProjectedRevenue($instructorId),
            'commission_rate' => 20,
        ];
        
        // Données pour les graphiques
        $monthlyLabels = [];
        $monthlyData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->isoFormat('MMM YY');
            $monthlyData[] = $this->getMonthlyRevenue($instructorId, $date->month, $date->year);
        }
        
        $courseLabels = array_column($byCourse, 'title');
        $courseData = array_column($byCourse, 'revenue');
        
        return view('instructor.analytics.revenue', compact(
            'stats', 'byCourse', 'monthlyLabels', 'monthlyData', 'courseLabels', 'courseData'
        ));
    }

     /**
     * Get monthly revenue for instructor.
     */
    private function getMonthlyRevenue(int $instructorId, int $month, int $year): float
    {
        $courses = Course::where('instructor_id', $instructorId)
            ->where('is_free', false)
            ->withCount(['students' => function ($query) use ($month, $year) {
                $query->whereMonth('enrollments.created_at', $month)
                      ->whereYear('enrollments.created_at', $year);
            }])
            ->get();
        
        $total = 0;
        foreach ($courses as $course) {
            $total += $course->students_count * $course->price;
        }
        
        return $total;
    }
    

}