<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function()
{
    Route::get('me', function(Request $request)
    {
        return $request->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);


    Route::get('translations/search', [TranslationController::class, 'search'])->name('translations.search');
    Route::apiResource('translations', TranslationController::class);
});