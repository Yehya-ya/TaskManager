<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => config('app.name').' server is up and running.',
    ]);
});
