<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\Course;
use App\Models\StudentCourse;

class StudentController extends Controller
{
    public function calculateGPA($id)
    {
        /**
         * course_credits * grade / sum_credits
         */

        $student = Student::find($id);
        $studentGrades = $student->firstWhere('student_id', $id)->course;

        foreach ($studentGrades as $studentGrade) {
            $studentGradesData[] = [];
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studId = Auth()->user()->student->student_id;

        $student = Student::findOrFail($studId);

        $studentCourses = $student->findOrFail($studId)->course()->where('course_semester_taken', $student->student_semester - 1)->get();

        foreach ($studentCourses as $studentCourse) {
            // $studentCourse->pivot->course_semester_taken;
            $studentCourseData[] = [
                'course' => $studentCourse->course_name,
                'grade' => $studentCourse->pivot->grade,
                'credits' => $studentCourse->course_credits,
                'credits * grade' => $studentCourse->pivot->grade * $studentCourse->course_credits
            ];
        }

        $totalCGProduct = 0;

        foreach ($studentCourseData as $courseData) {
            $totalCGProduct += $courseData['credits * grade'];
        }

        $gpa = number_format((float)$totalCGProduct / $studentCourses->sum('course_credits'), 2, '.', '');

        if ($gpa < 2.5) {
            $credits_limit = 18;
        } elseif ($gpa >= 2.5 && $gpa < 3) {
            $credits_limit = 20;
        } elseif ($gpa >= 3 && $gpa < 3.5) {
            $credits_limit = 22;
        } else {
            $credits_limit = 24;
        }

        return response()->json([
            'name' => $student->student_name,
            'student_id' => $student->user->user_id_number,
            'advisor_name' => $student->advisor->advisor_name,
            'credits_total' => $studentCourses->sum('course_credits'),
            'courses' => $studentCourseData,
            'c_g_sum' => $totalCGProduct,
            'gpa' => $gpa,
            'semester' => $student->student_semester,
            'credits_limit' => $credits_limit
        ]);
    }

    public function availableCourses()
    {
        $dept_id = Auth()->user()->student->department->department_id;

        $courses = Course::where('department_id', $dept_id)->where('course_is_open', 1)->get();

        foreach($courses as $course){
            $courseData[] = [
                'course_name' => $course->course_name,
                'course_code' => $course->course_code,
                'course_class' => $course->course_class,
                'course_capacity' => $course->course_capacity,
                'course_credits' => $course->course_credits,
            ];
        }

        return response()->json([
            'status' => 1,
            'courses' => $courseData,
        ]);
    }

    public function takeCourse(Request $request)
    {
        $courseData = $request->validate([
            'course_id' => 'required',
            // 'course_class' => 'required',
        ]);

        $courseData['student_id'] = Auth()->user()->student->student_id;
        // $courseData['course_id'] = Auth()->user()->student->student_id;
        $courseData['course_semester_taken'] = Auth()->user()->student->student_semester;
        $courseData['status'] = 'taken';

        $course = StudentCourse::create($courseData);

        return response()->json([
            'status' => 1,
            'course' => $course,
        ]);
    }

    public function showCurrentCourses()
    {
        $studId = Auth()->user()->student->student_id;
        $studCurrentSemester = Auth()->user()->student->student_semester;

        $currentCourses = Student::findOrFail($studId)->course()->where('course_semester_taken', $studCurrentSemester)->get();

        foreach($currentCourses as $currentCourse){
            $currentCourseData[] = [
                'course_name' => $currentCourse->course_name,
                'course_code' => $currentCourse->course_code,
                'course_class' => $currentCourse->course_class,
                'course_credits' => $currentCourse->course_credits,
            ];
        }

        return response()->json([
            'status' => 1,
            'courses' => $currentCourseData,
            'credits_total' => $currentCourses->sum('course_credits'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($studId)
    {
        $student = Student::findOrFail($studId);

        $studentCourses = $student->findOrFail($studId)->course;

        foreach ($studentCourses as $studentCourse) {
            $studentCourseData[] = [
                'course' => $studentCourse->course_name,
                'grade' => $studentCourse->pivot->grade,
                'credits' => $studentCourse->course_credits,
                'credits * grade' => $studentCourse->pivot->grade * $studentCourse->course_credits
            ];
        }

        $totalCGProduct = 0;

        foreach ($studentCourseData as $courseData) {
            $totalCGProduct += $courseData['credits * grade'];
        }

        $gpa = $totalCGProduct / $studentCourses->sum('course_credits');

        if ($gpa < 2.5) {
            $credits_limit = 18;
        } elseif ($gpa >= 2.5 && $gpa < 3) {
            $credits_limit = 20;
        } elseif ($gpa >= 3 && $gpa < 3.5) {
            $credits_limit = 22;
        } else {
            $credits_limit = 24;
        }

        return response()->json([
            'name' => $student->student_name,
            'student_id' => $student->user->user_id_number,
            'advisor_name' => $student->advisor->advisor_name,
            'credits_total' => $studentCourses->sum('course_credits'),
            'courses' => $studentCourseData,
            'c_g_sum' => $totalCGProduct,
            'gpa' => $gpa,
            'semester' => $student->student_semester,
            'credits_limit' => $credits_limit
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
