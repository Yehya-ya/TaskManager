<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all();
    }

    public function store(Request $request)
    {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'end_at' => $request->end_at,
        ]);
    }

    public function show(Task $user)
    {
        return $user;
    }

    public function update(Request $request, Task $user)
    {
        $user->update([
            'title' => $request->title,
            'description' => $request->description,
            'end_at' => $request->end_at,
        ]);
    }

    public function destroy(Task $user)
    {
        $user->delete();
    }
}
