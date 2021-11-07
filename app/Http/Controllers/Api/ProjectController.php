<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::all();
    }

    public function store(Request $request)
    {
        Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user_id
        ]);
    }

    public function show(Project $user)
    {
        return $user;
    }

    public function update(Request $request, Project $user)
    {
        $user->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
    }

    public function destroy(Project $user)
    {
        $user->delete();
    }
}
