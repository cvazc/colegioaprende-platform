<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCreditTransaction;
use App\Models\StudentCreditWallet;
use Illuminate\Http\Request;

class StudentCreditController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Student)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $wallet = StudentCreditWallet::query()
            ->firstOrCreate(['student_id' => $user->id], ['balance' => 0]);

        $transactions = StudentCreditTransaction::query()
            ->where('student_id', $user->id)
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(function (StudentCreditTransaction $tx) {
                return [
                    'id' => $tx->id,
                    'delta' => $tx->delta,
                    'reason' => $tx->reason,
                    'meta' => $tx->meta,
                    'created_at' => $tx->created_at?->toDateTimeString(),
                ];
            });

        return response()->json([
            'data' => [
                'balance' => $wallet->balance,
                'transactions' => $transactions,
            ],
        ]);
    }
}
