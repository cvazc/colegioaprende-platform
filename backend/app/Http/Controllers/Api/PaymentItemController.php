<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentItem;
use Illuminate\Http\Request;

class PaymentItemController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'course_type' => ['required', 'string'],
            'enrollment_type' => ['required', 'string'],
        ]);

        $items = PaymentItem::query()
            ->where('course_type', $validated['course_type'])
            ->where('enrollment_type', $validated['enrollment_type'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (PaymentItem $item) {
                return [
                    'code' => $item->code,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'currency' => $item->currency,
                    'credit_qty' => $item->credit_qty,
                    'auto_register' => $item->auto_register,
                    'is_billable' => $item->is_billable,
                    'unlock_all_subjects' => $item->unlock_all_subjects,
                    'sort_order' => $item->sort_order,
                ];
            });

        return response()->json([
            'data' => $items,
        ]);
    }
}
