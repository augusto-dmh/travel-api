<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;

Route::get('/travels', [TravelController::class, 'index'])->name('travel.index');
