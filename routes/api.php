<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\AdvisorController;
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

// Route::apiResource('student', StudentController::class);

Route::middleware('auth:sanctum')->group(function(){
    Route::middleware(IsAdmin::class)->group(function(){
        Route::get('admin/courses-list', [AdminController::class, 'index']);
        Route::post('admin/create-course', [AdminController::class, 'store']);
        Route::post('admin/change-course-availability/{course_code}/{course_class}/{status}', [AdminController::class, 'changeCourseAvailability']);
    });
    Route::get('student', [StudentController::class, 'index']);
    Route::get('student/courses', [StudentController::class, 'availableCourses']);
    Route::post('student/take-course', [StudentController::class, 'takeCourse']);
    // Route::get('student/drop-course/{course_id}', [StudentController::class, 'dropCourse']);
    Route::delete('student/drop-course/{course_id}', [StudentController::class, 'dropCourse']);
    Route::get('student/current-courses', [StudentController::class, 'showCurrentCourses']);
    Route::get('student/course-detail/{course_id}', [StudentController::class, 'showCourseDetail']);

    Route::get('advisor', [AdvisorController::class, 'index']);
    Route::get('advisor/student-detail/{student_id}', [AdvisorController::class, 'showStudentDetail']);
    Route::patch('advisor/accept-student-courses/{student_id}', [AdvisorController::class, 'acceptCourses']);
    Route::patch('advisor/cancel-student-courses/{student_id}', [AdvisorController::class, 'cancelAcceptCourses']);
    Route::post('advisor/take-student-course/{student_id}', [AdvisorController::class, 'takeCourse']);
    Route::delete('advisor/drop-student-course/{student_id}/{course_id}', [AdvisorController::class, 'dropCourse']);
});
