<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;

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
        $students = Student::all();

        foreach ($students as $student) {
            $studentsData[] = [
                ''
            ];
        }

        return response()->json([]);
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
