<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class TaskController extends Controller
{
    /**
     * Create a new task for the authenticated user.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the input data with custom error messages
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'completed'])],
        ], [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either pending or completed.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the task
        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json($task, 201);
    }

    /**
     * Retrieve a single task by its ID.
     * Only the task owner or admin can access the task.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Find the task or fail if not found
            $task = Task::where('id', $id)
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('user', function ($q) {
                            $q->where('role', 'admin');
                        });
                })
                ->firstOrFail();

            return response()->json($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found or you do not have permission to view it.'], 404);
        }
    }

    /**
     * Update a task by its ID.
     * Only the task owner or admin can update the task.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validate the input data with custom error messages
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['pending', 'completed'])],
        ], [
            'title.required' => 'The title field is required when provided.',
            'title.string' => 'The title must be a string.',
            'status.in' => 'The status must be either pending or completed.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Find the task or fail if not found
            $task = Task::where('id', $id)
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('user', function ($q) {
                            $q->where('role', 'admin');
                        });
                })
                ->firstOrFail();

            // Update the task
            $task->update($request->only(['title', 'description', 'status']));

            return response()->json($task);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found or you do not have permission to update it.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the task.'], 500);
        }
    }

    /**
     * Delete a task by its ID.
     * Only the task owner or admin can delete the task.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find the task or fail if not found
            $task = Task::where('id', $id)
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('user', function ($q) {
                            $q->where('role', 'admin');
                        });
                })
                ->firstOrFail();

            // Delete the task
            $task->delete();

            // Return success message
            return response()->json(['message' => 'Task deleted successfully']);
            
        } catch (ModelNotFoundException $e) {
            // Return error message if task not found or permission issue
            return response()->json(['message' => 'Task not found or you do not have permission to delete it.'], 404);
        } catch (\Exception $e) {
            // Return error message for any other issues
            return response()->json(['message' => 'An error occurred while deleting the task.'], 500);
        }
    }

    /**
     * List tasks with optional pagination and filtering.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['sometimes', Rule::in(['pending', 'completed'])],
        ], [
            'status.in' => 'The status must be either pending or completed.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $query = Task::query();

        // If the user is not an admin, show only their tasks
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && auth()->user()->role === 'admin') {
            $query->where('user_id', $request->user_id);
        }

        // Offset-based pagination
        if ($request->has('page')) {
            return response()->json($query->paginate(5)); // Change 5 to the number of items per page
        }

        // Cursor-based pagination
        if ($request->has('cursor')) {
            return response()->json($query->cursorPaginate(5)); // Change 5 to the number of items per page
        }

        return response()->json($query->get());
    }
}
