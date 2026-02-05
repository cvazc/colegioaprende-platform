<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $query = Payment::query()
            ->orderByDesc('id')
            ->where(function ($builder) use ($user) {
                if ($user->prospect_id) {
                    $builder->where('prospect_id', $user->prospect_id);
                }
                $builder->orWhere('student_id', $user->id);
            });

        $payments = $query->get()->map(function (Payment $payment) {
            return [
                'id' => $payment->id,
                'provider' => $payment->provider,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'item_code' => $payment->item_code,
                'approved_at' => $payment->approved_at?->toDateTimeString(),
                'created_at' => $payment->created_at?->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $payments,
        ]);
    }
}
