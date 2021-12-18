<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(Project $project): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Category::class, $project]);

        return CategoryResource::collection($project->categories()->orderBy('id')->get());
    }

    public function show(Project $project, Category $category): CategoryResource
    {
        $this->authorize('view', [$category, $project]);

        return CategoryResource::make($category->load(['project', 'tasks']));
    }

    public function store(StoreCategoryRequest $request, Project $project): CategoryResource
    {
        $this->authorize('create', [Category::class, $project]);

        $category = $project->categories()->create($request->validated());

        return CategoryResource::make($category);
    }

    public function update(UpdateCategoryRequest $request, Project $project, Category $category): CategoryResource
    {
        $this->authorize('update', [$category, $project]);

        $category->update($request->validated());

        return CategoryResource::make($category);
    }

    public function destroy(Category $category, Project $project): JsonResponse
    {
        $this->authorize('delete', [$category, $project]);

        $category->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
