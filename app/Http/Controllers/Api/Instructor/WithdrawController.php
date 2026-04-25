<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawController extends Controller
{
    /**
     * Get user payment settings.
     */
    public function settings()
    {
        $user = Auth::user();
        $settings = $user->settings ?? [];
        
        return response()->json([
            'success' => true,
            'payment_method' => $settings['payment']['method'] ?? '',
            'mobile_money_provider' => $settings['payment']['mobile_money_provider'] ?? 'tmoney',
            'mobile_money_number' => $settings['payment']['mobile_money_number'] ?? '',
            'mobile_money_name' => $settings['payment']['mobile_money_name'] ?? '',
        ]);
    }
    
    /**
     * Save user settings.
     */
    public function saveSettings(Request $request)
    {
        $user = Auth::user();
        
        \Log::info('Données reçues pour sauvegarde:', $request->all());
        
        $validated = $request->validate([
            'notifications' => 'nullable|array',
            'preferences' => 'nullable|array',
            'payment' => 'required|array',
            'payment.method' => 'required|string|in:mobile_money,bank',
            'payment.mobile_money_provider' => 'nullable|string|in:tmoney,flooz',
            'payment.mobile_money_number' => 'nullable|string',
            'payment.mobile_money_name' => 'nullable|string|max:255',
        ]);
        
        try {
            $settings = $user->settings ?? [];
            
            if (isset($validated['notifications'])) {
                $settings['notifications'] = $validated['notifications'];
            }
            
            if (isset($validated['preferences'])) {
                $settings['preferences'] = $validated['preferences'];
            }
            
            $settings['payment'] = [
                'method' => $validated['payment']['method'],
                'mobile_money_provider' => $validated['payment']['mobile_money_provider'] ?? null,
                'mobile_money_number' => $validated['payment']['mobile_money_number'] ?? null,
                'mobile_money_name' => $validated['payment']['mobile_money_name'] ?? null,
            ];
            
            $user->settings = $settings;
            $user->save();
            
            \Log::info('Paramètres sauvegardés', ['user_id' => $user->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Paramètres enregistrés avec succès !'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur sauvegarde paramètres: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get instructor balance.
     */
    public function balance()
    {
        $user = Auth::user();
        $balance = $this->calculateAvailableBalance($user);
        
        return response()->json([
            'success' => true,
            'balance' => $balance,
            'total_earned' => $this->getTotalEarned($user->id),
            'pending_balance' => Payout::where('instructor_id', $user->id)->where('status', 'pending')->sum('amount'),
            'this_month' => 0,
            'last_month' => 0,
            'commission_rate' => 80,
        ]);
    }
    
    /**
     * Get transactions.
     */
    public function transactions()
    {
        $user = Auth::user();
        
        $transactions = Enrollment::whereHas('course', fn($q) => $q->where('instructor_id', $user->id))
            ->with('course')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($enrollment) {
                $pricePaid = $enrollment->course->price ?? 0;
                
                return [
                    'id' => $enrollment->id,
                    'course' => $enrollment->course->title ?? 'Cours inconnu',
                    'amount' => $pricePaid,
                    'commission' => $pricePaid * 0.2,
                    'net' => $pricePaid * 0.8,
                    'status' => 'pending',
                    'date' => $enrollment->created_at->format('d/m/Y'),
                ];
            });
        
        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }
    
    /**
     * Get withdraw history.
     */
    public function history()
    {
        $user = Auth::user();
        
        $payouts = Payout::where('instructor_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($payout) {
                return [
                    'id' => $payout->id,
                    'amount' => $payout->amount,
                    'method' => $payout->method === 'mobile_money' ? 'Mobile Money' : 'Virement bancaire',
                    'status' => $payout->status,
                    'status_label' => $this->getStatusLabel($payout->status),
                    'date' => $payout->created_at->format('d/m/Y H:i'),
                ];
            });
        
        return response()->json([
            'success' => true,
            'payouts' => $payouts
        ]);
    }
    
    /**
     * Store withdraw request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:5000',
            'payment_method' => 'required|in:mobile_money,bank',
            'mobile_money_provider' => 'required_if:payment_method,mobile_money|in:tmoney,flooz',
            'mobile_money_number' => 'required_if:payment_method,mobile_money|string|size:8',
            'mobile_money_name' => 'required_if:payment_method,mobile_money|string|max:255',
        ]);
        
        $availableBalance = $this->calculateAvailableBalance($user);
        
        if ($validated['amount'] > $availableBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant. Votre solde disponible est de ' . number_format($availableBalance) . ' FCFA.'
            ], 400);
        }
        
        try {
            DB::beginTransaction();
            
            $payout = Payout::create([
                'instructor_id' => $user->id,
                'amount' => $validated['amount'],
                'method' => $validated['payment_method'],
                'status' => 'pending',
                'notes' => json_encode([
                    'mobile_money_provider' => $validated['mobile_money_provider'] ?? null,
                    'mobile_money_number' => $validated['mobile_money_number'] ?? null,
                    'mobile_money_name' => $validated['mobile_money_name'] ?? null,
                ]),
            ]);
            
            Log::info('Demande de retrait créée', [
                'user_id' => $user->id,
                'payout_id' => $payout->id,
                'amount' => $validated['amount']
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Votre demande de retrait de ' . number_format($validated['amount']) . ' FCFA a été enregistrée.'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur retrait: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.'
            ], 500);
        }
    }
    
    private function calculateAvailableBalance(User $user): float
    {
        $totalEarned = $this->getTotalEarned($user->id);
        $totalWithdrawn = Payout::where('instructor_id', $user->id)
            ->whereIn('status', ['completed', 'pending'])
            ->sum('amount');
        
        return max(0, $totalEarned - $totalWithdrawn);
    }
    
    private function getTotalEarned(int $instructorId): float
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
    
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'completed' => 'Effectué',
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
            default => $status
        };
    }
}