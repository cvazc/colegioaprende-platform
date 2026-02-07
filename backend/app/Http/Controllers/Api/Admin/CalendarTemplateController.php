<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCalendarTemplateStoreRequest;
use App\Http\Requests\AdminCalendarTemplateUpdateRequest;
use App\Models\CalendarTemplate;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarTemplateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $templates = CalendarTemplate::query()
            ->with(['events' => function ($query) {
                $query->orderBy('due_date');
            }])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $templates,
        ]);
    }

    public function store(AdminCalendarTemplateStoreRequest $request)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validated();

        $template = DB::transaction(function () use ($validated, $user) {
            $template = CalendarTemplate::query()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'created_by_employee_id' => $user->id,
            ]);

            foreach ($validated['events'] ?? [] as $event) {
                $template->events()->create([
                    'event_code' => $event['event_code'] ?? null,
                    'title' => $event['title'],
                    'description' => $event['description'] ?? null,
                    'due_date' => $event['due_date'],
                    'is_payment' => $event['is_payment'] ?? true,
                    'is_active' => $event['is_active'] ?? true,
                ]);
            }

            return $template->load(['events' => function ($query) {
                $query->orderBy('due_date');
            }]);
        });

        return response()->json([
            'data' => $template,
        ], 201);
    }

    public function update(AdminCalendarTemplateUpdateRequest $request, int $templateId)
    {
        $user = $request->user();

        if (!($user instanceof Employee)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $template = CalendarTemplate::query()->find($templateId);
        if (!$template) {
            return response()->json(['message' => 'Calendar template not found.'], 404);
        }

        $validated = $request->validated();

        DB::transaction(function () use ($template, $validated) {
            $template->fill([
                'name' => $validated['name'] ?? $template->name,
                'description' => array_key_exists('description', $validated) ? $validated['description'] : $template->description,
                'is_active' => $validated['is_active'] ?? $template->is_active,
            ]);
            $template->save();

            if (array_key_exists('events', $validated)) {
                $template->events()->delete();
                foreach ($validated['events'] as $event) {
                    $template->events()->create([
                        'event_code' => $event['event_code'] ?? null,
                        'title' => $event['title'],
                        'description' => $event['description'] ?? null,
                        'due_date' => $event['due_date'],
                        'is_payment' => $event['is_payment'] ?? true,
                        'is_active' => $event['is_active'] ?? true,
                    ]);
                }
            }
        });

        return response()->json([
            'data' => $template->fresh(['events' => function ($query) {
                $query->orderBy('due_date');
            }]),
        ]);
    }
}
