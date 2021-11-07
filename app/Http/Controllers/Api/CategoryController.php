<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
        Category::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $user->id,
        ]);
    }

    public function show(Category $user)
    {
        return $user;
    }

    public function update(Request $request, Category $user)
    {
        $user->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
    }

    public function destroy(Category $user)
    {
        $user->delete();
    }
}
