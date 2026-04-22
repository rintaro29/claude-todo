<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Todo::orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $todo = Todo::create($validated);

        return response()->json($todo, 201);
    }

    public function update(Todo $todo): JsonResponse
    {
        $todo->update(['is_completed' => !$todo->is_completed]);

        return response()->json($todo->fresh());
    }

    public function destroy(Todo $todo): Response
    {
        $todo->delete();

        return response()->noContent();
    }
}
