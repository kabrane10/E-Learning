<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id();
        
        // ✅ Calculer les vraies statistiques
        $totalEarned = $this->getTotalEarned($instructorId);
        $availableBalance = $this->getAvailableBalance($instructorId);
        $pendingBalance = $this->getPendingBalance($instructorId);
        
        $stats = [
            'total_earned' => $totalEarned,
            'available_balance' => $availableBalance,
            'pending_balance' => $pendingBalance,
            'this_month' => $this->getEarningsThisMonth($instructorId),
            'last_month' => $this->getEarningsLastMonth($instructorId),
            'commission_rate' => 80,
        ];
        
        // ✅ Transactions réelles
        $transactions = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $instructorId))
            ->with('course')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn($enrollment) => [
                'id' => $enrollment->id,
                'course' => $enrollment->course->title ?? 'Cours inconnu',
                'amount' => $enrollment->course->price ?? 0,
                'commission' => ($enrollment->course->price ?? 0) * 0.2,
                'net' => ($enrollment->course->price ?? 0) * 0.8,
                'status' => 'completed',
                'date' => $enrollment->created_at->format('Y-m-d'),
            ]);
        
        // ✅ Historique des retraits réel
        $payouts = Payout::where('instructor_id', $instructorId)
            ->latest()
            ->get()
            ->map(fn($payout) => [
                'id' => $payout->id,
                'amount' => $payout->amount,
                'method' => $payout->method === 'mobile_money' ? 'Mobile Money' : 'Virement bancaire',
                'status' => $payout->status,
                'date' => $payout->created_at->format('Y-m-d'),
            ]);
        
        return view('instructor.earnings.index', compact('stats', 'transactions', 'payouts'));
    }
    
    private function getTotalEarned($instructorId): float
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
    
    private function getAvailableBalance($instructorId): float
    {
        $totalEarned = $this->getTotalEarned($instructorId);
        $totalWithdrawn = Payout::where('instructor_id', $instructorId)
            ->whereIn('status', ['completed', 'pending'])
            ->sum('amount');
        
        return max(0, $totalEarned - $totalWithdrawn);
    }
    
    private function getPendingBalance($instructorId): float
    {
        return Payout::where('instructor_id', $instructorId)
            ->where('status', 'pending')
            ->sum('amount');
    }
    
    private function getEarningsThisMonth($instructorId): float
    {
        // À implémenter avec les inscriptions du mois
        return 0;
    }
    
    private function getEarningsLastMonth($instructorId): float
    {
        return 0;
    }
}