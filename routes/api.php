<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\IsAdmin;

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

Route::middleware('auth:sanctum')->group(function(){
    Route::middleware(IsAdmin::class)->group(function(){
        Route::post('admin/create-course', [AdminController::class, 'store']);
        Route::get('admin/courses-list', [AdminController::class, 'index']);
        Route::post('admin/change-course-availability/{course_code}/{status}', [AdminController::class, 'changeCourseAvailability']);
    });
});
