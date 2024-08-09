<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\AdvisorController;
use App\Http\Controllers\Api\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsAdvisor;

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

// Route::apiResource('student', StudentController::class);

Route::middleware('auth:sanctum')->group(function(){
    Route::middleware(IsAdmin::class)->group(function(){
        Route::prefix('admin')->group(function () {
            Route::get('/courses-list', [AdminController::class, 'index']);
            Route::post('/create-course', [AdminController::class, 'store']);
            Route::post('/change-course-availability/{course_code}/{course_class}/{status}', [AdminController::class, 'changeCourseAvailability']);
        });
    });

    Route::middleware(IsAdvisor::class)->group(function(){
        Route::prefix('advisor')->group(function () {
            Route::get('/', [AdvisorController::class, 'index']);
            Route::get('/student-detail/{student_id}', [AdvisorController::class, 'showStudentDetail']);
            Route::patch('/accept-student-courses/{student_id}', [AdvisorController::class, 'acceptCourses']);
            Route::patch('/cancel-student-courses/{student_id}', [AdvisorController::class, 'cancelAcceptCourses']);
            Route::post('/take-student-course/{student_id}', [AdvisorController::class, 'takeCourse']);
            Route::delete('/drop-student-course/{student_id}/{course_id}', [AdvisorController::class, 'dropCourse']);
        });
    });

    Route::prefix('student')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('/courses', [StudentController::class, 'availableCourses']);
        Route::post('/take-course', [StudentController::class, 'takeCourse']);
        // Route::get('/drop-course/{course_id}', [StudentController::class, 'dropCourse']);
        Route::delete('/drop-course/{course_id}', [StudentController::class, 'dropCourse']);
        Route::get('/current-courses', [StudentController::class, 'showCurrentCourses']);
        Route::get('/course-detail/{course_id}', [StudentController::class, 'showCourseDetail']);
    });

});
