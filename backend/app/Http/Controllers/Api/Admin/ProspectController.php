<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Prospect;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'email' => ['sometimes', 'email'],
        ]);

        $query = Prospect::query()->orderByDesc('id');

        if (!empty($validated['email'])) {
            $query->where('email', $validated['email']);
        }

        $prospects = $query->paginate(25);

        return response()->json([
            'data' => $prospects->items(),
            'meta' => [
                'current_page' => $prospects->currentPage(),
                'last_page' => $prospects->lastPage(),
                'per_page' => $prospects->perPage(),
                'total' => $prospects->total(),
            ],
        ]);
    }
}
