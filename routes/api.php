<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlateController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\AdminStatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/me', [ProfileController::class, 'show'])->middleware('auth:sanctum');
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth:sanctum');

Route::get('/categories', [CategoryController::class, 'index'])->middleware('auth:sanctum');
Route::post('/categories', [CategoryController::class, 'store'])->middleware('auth:sanctum', 'admin');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->middleware('auth:sanctum');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware('auth:sanctum', 'admin');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum', 'admin');
Route::get('/categories/{id}/plates', [CategoryController::class, 'plates'])->middleware('auth:sanctum');

Route::get('/plates', [PlateController::class, 'index'])->middleware('auth:sanctum');
Route::post('/plates', [PlateController::class, 'store'])->middleware('auth:sanctum', 'admin');
Route::get('/plates/{id}', [PlateController::class, 'show'])->middleware('auth:sanctum');
Route::put('/plates/{id}', [PlateController::class, 'update'])->middleware('auth:sanctum', 'admin');
Route::delete('/plates/{id}', [PlateController::class, 'destroy'])->middleware('auth:sanctum', 'admin');

Route::get('/ingredients', [IngredientController::class, 'index'])->middleware('auth:sanctum', 'admin');
Route::post('/ingredients', [IngredientController::class, 'store'])->middleware('auth:sanctum', 'admin');
Route::put('/ingredients/{id}', [IngredientController::class, 'update'])->middleware('auth:sanctum', 'admin');
Route::delete('/ingredients/{id}', [IngredientController::class, 'destroy'])->middleware('auth:sanctum', 'admin');

Route::get('/recommendations', [RecommendationController::class, 'index'])->middleware('auth:sanctum');
Route::post('/recommendations/analyze/{plate_id}', [RecommendationController::class, 'analyze'])->middleware('auth:sanctum');
Route::get('/recommendations/{plate_id}', [RecommendationController::class, 'show'])->middleware('auth:sanctum');

Route::get('/admin/stats', [AdminStatsController::class, 'index'])->middleware('auth:sanctum', 'admin');