<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
use App\Http\Controllers\CreateUserController;

Route::get('/travels', [TravelController::class, 'index'])->name('api_v1.travel.index');
Route::get('/travels/{travel:slug}/tours', [TourController::class, 'index'])->name('api_v1.tour.index');
Route::post('/create-user', CreateUserController::class)->middleware('auth:sanctum')->name('api_v1.create_user');
