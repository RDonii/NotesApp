<?php

use App\Http\Controllers\v1\NoteController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1/')->group(function(){
    Route::get('notes/public/', [NoteController::class, 'public']);
    Route::post('login/', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'register']);
    });

Route::middleware('auth:api')->group(function(){
    Route::prefix('v1/')->group(function(){
        Route::get('notes/owned/', [NoteController::class, 'owned']);
        Route::get('notes/{note}', [NoteController::class, 'show']);
        Route::post('notes/', [NoteController::class, 'store']);
        Route::patch('notes/{note}', [NoteController::class, 'update']);
        Route::delete('notes/{note}', [NoteController::class, 'destroy']);
        Route::post('logout/', [UserController::class, 'logout']);
    });
});

