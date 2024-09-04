<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;

Route::get('/travels', [TravelController::class, 'index'])->name('api_v1.travel.index');
Route::get('/travels/{travel:slug}/tours', [TourController::class, 'index'])->name('api_v1.tour.index');
