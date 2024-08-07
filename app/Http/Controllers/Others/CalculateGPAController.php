<?php

namespace App\Http\Controllers\Others;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;

class CalculateGPAController extends Controller
{
    public function calculateGPA($studId)
    {
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

        return array(
            $studentCourses->sum('course_credits'),
            $studentCourseData,
            $totalCGProduct,
            $gpa,
            $credits_limit
        );
    }
}
