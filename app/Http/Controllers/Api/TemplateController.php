<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use malikad778\NotificationCenter\Models\NotificationTemplate;

class TemplateController extends Controller
{
    public function index(): JsonResponse
    {
        $templates = NotificationTemplate::paginate(20);
        return response()->json($templates);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $template = NotificationTemplate::create($validated);

        return response()->json($template, 201);
    }

    public function show(string $id): JsonResponse
    {
        $template = NotificationTemplate::findOrFail($id);
        return response()->json($template);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $template = NotificationTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'channel' => 'sometimes|string|max:50',
            'subject' => 'nullable|string|max:255',
            'content' => 'sometimes|string',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return response()->json($template);
    }

    public function destroy(string $id): JsonResponse
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->delete();

        return response()->json(null, 204);
    }
}
