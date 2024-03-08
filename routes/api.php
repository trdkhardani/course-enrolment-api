<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::resource('test', [TestController::class])->except('store', 'update', 'destroy');

Route::post('login', [AuthController::class, 'authLogin']);

Route::apiResource('student', StudentController::class);
// Route::get('test/{student_id}', [StudentController::class, 'show']);

// Route::get('test-show/{student_id}', [TestShowController::class, 'showBoskuh']);
