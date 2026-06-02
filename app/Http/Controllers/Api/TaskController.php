<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\StatusService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private StatusService $statusService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statuses = $this->statusService->getStatuses()->implode(',');

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'status' => "nullable|in:$statuses",
        ]);

        $tasks = $request->user()->tasks();

        if (isset($validated['name'])) {
            $tasks->where('name', 'like', '%' . $validated['name'] . '%');
        }

        if (isset($validated['status'])) {
            $tasks->where('status', $validated['status']);
        }

        return response()->json(
            $tasks->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $statuses = $this->statusService->getStatuses()->implode(',');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => "required|in:$statuses",
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            abort(403);
        }

        $statuses = $this->statusService->getStatuses()->implode(',');

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => "sometimes|required|in:$statuses",
            'priority' => 'sometimes|required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            abort(403);
        }

        $task->delete();

        return response()->json(null, 204);
    }
}
