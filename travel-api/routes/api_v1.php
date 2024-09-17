<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
use App\Http\Controllers\CreateUserController;

Route::get('/travels', [TravelController::class, 'index'])->name('api_v1.travel.index');
Route::post('/travels', [TravelController::class, 'store'])->middleware('auth:sanctum')->name('api_v1.travel.store');
Route::get('/travels/{travel:slug}/tours', [TourController::class, 'index'])->name('api_v1.tour.index');
Route::post('/create-user', CreateUserController::class)->middleware('auth:sanctum')->name('api_v1.create_user');
Route::post('/travels/{travel}/tour', [TourController::class, 'store'])->middleware('auth:sanctum')->name('api_v1.tour.store');
Route::put('/travels/{travel}', [TravelController::class, 'update'])->middleware('auth:sanctum')->name('api_v1.travel.update');
