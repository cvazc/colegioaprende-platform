<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProspectStoreRequest;
use App\Models\Prospect;

class ProspectController extends Controller
{
    public function store(ProspectStoreRequest $request)
    {
        $prospect = Prospect::query()->create($request->validated());

        return response()->json([
            'data' => [
                'id' => $prospect->id,
                'email' => $prospect->email,
            ],
        ], 201);
    }
}
