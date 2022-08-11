<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConnectionSentController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\ConnectionReceivedController;
use App\Http\Controllers\ConnectionCommonController;
use App\Http\Controllers\ConnectionSuggestionController;

Route::resource('requests/sent', ConnectionSentController::class, ['only' => ['index', 'store', 'update', 'destroy']]);

Route::resource('connections', ConnectionController::class, ['except' => ['show']]);
Route::get('stats',                     [ConnectionController::class, 'stats']);
Route::get('common',                     [ConnectionController::class, 'common']);

Route::resource('requests/received', ConnectionReceivedController::class, ['only' => ['index', 'destroy']]);

Route::get('connections/common', [ConnectionCommonController::class, 'index']);

Route::get('connections/suggestion', [ConnectionSuggestionController::class, 'index']);
