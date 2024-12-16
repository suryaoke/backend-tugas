<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function index(Request $request)
    {

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);

        // Admin
        if (auth()->user()->hasRole('admin')) {
            $query = Task::with('user');

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }
            if ($status !== null) {
                $query->where('status', $status);
            }
            $tasks = $query->paginate($perPage);
        } else {

            $query = Task::with('user')
                ->where('user_id', auth()->id());

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            if ($status !== null) {
                $query->where('status', $status);
            }

            $tasks = $query->paginate($perPage);
        }


        return response()->json($tasks);
    }



    public function store(TaskCreateRequest $request)
    {
        // Hanya admin yang bisa membuat task
        if (auth()->user()->hasRole('user')) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $validated = $request->validated();


        $tasks = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => $validated['user_id'],
            'status' => "Belum Selesai",
        ]);




        return response()->json($tasks, 201);
    } // end me



    public function show(Task $task)
    {
        // admin //
        if (auth()->user()->hasRole('admin')) {

            return response()->json($task->load('user'));
        } else {

            if ($task->user_id === auth()->id()) {

                return response()->json($task->load('user'));
            } else {

                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
    } // end method




    public function update(TaskUpdateRequest $request, Task $task)
    {
        //  hanya admin //
        if (auth()->user()->hasRole('user')) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $validated = $request->validated();

        $task->update($validated);
        return response()->json($task);
    } // end method



    public function destroy(Task $task)
    {
        //  hanya admin //
        if (auth()->user()->hasRole('user')) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $task->delete();
        return response()->json(null, 204);
    } // end method
}
