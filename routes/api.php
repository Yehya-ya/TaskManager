<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\VerifyAccessToken;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('register', RegisterController::class)->name('register');
Route::post('login', LoginController::class)->name('login');
Route::post('verify_access_token', VerifyAccessToken::class)->middleware('auth:sanctum')->name('verify_access_token');
Route::post('logout', LogoutController::class)->middleware('auth:sanctum')->name('logout');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::put('projects/{project}/add-member', [ProjectController::class, 'addMember'])->name('project.add_member');
    Route::put('projects/{project}/remove-member', [ProjectController::class, 'removeMember'])->name('project.remove_member');
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('projects.tasks', TaskController::class);
    Route::apiResource('projects.categories', CategoryController::class);
});

