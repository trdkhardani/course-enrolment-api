<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Advisor;
use App\Models\Student;
use App\Models\StudentCourse;

class AdvisorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get advisor_id
        $advisorId = Auth()->user()->advisor->advisor_id;

        // Get logged in advisor's data
        $advisorInfo = Advisor::findOrFail($advisorId);

        // Get logged in advisor's students
        $students = Student::where('advisor_id', $advisorId)->get();

        foreach($students as $student){
            $studentData[] = [
                'student_name' => $student->student_name,
                'student_id_number' => $student->user->user_id_number,
            ];
        }

        return response()->json([
            'advisor_name' => $advisorInfo->advisor_name,
            'advisor_id_number' => Auth()->user()->user_id_number,
            'students' => $studentData,
        ]);
    }

    public function acceptCourses($studId)
    {
        $advisorId = Auth()->user()->advisor->advisor_id;

        $student = Student::where('student_id', $studId)->firstWhere('advisor_id', $advisorId);

        if ($student == null) {
            return response()->json([
                'status' => 0,
                'message' => "Student not found"
            ], 404);
        }

        $studentTakenCourses = $student->find($studId)->course()->where('student_id', $studId)->where('status', 'taken');

        $accept['status'] = 'enrolled';

        $studentTakenCourses->update($accept);

        return response()->json([
            'status' => 1,
            'message' => $student->student_name . "'s taken courses successfully accepted",
        ]);
    }

    public function cancelAcceptCourses($studId)
    {
        $advisorId = Auth()->user()->advisor->advisor_id;

        $student = Student::where('student_id', $studId)->firstWhere('advisor_id', $advisorId);

        if ($student == null) {
            return response()->json([
                'status' => 0,
                'message' => "Student not found"
            ], 404);
        }

        $studentTakenCourses = $student->find($studId)->course()->where('student_id', $studId)->where('status', 'enrolled');

        $cancel['status'] = 'taken';

        $studentTakenCourses->update($cancel);

        return response()->json([
            'status' => 1,
            'message' => $student->student_name . "'s enrolled courses successfully cancelled",
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
    public function showStudentDetail($studId)
    {
        // Get advisor_id
        $advisorId = Auth()->user()->advisor->advisor_id;

        // Find searched student in param
        $student = Student::findOrFail($studId);

        // Check if the advisor has the searched student
        if($student->advisor->advisor_id !== $advisorId){
            abort(404, 'No student found');
        }

        // Calculate GPA
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
            'student_name' => $student->student_name,
            'student_id_number' => $student->user->user_id_number,
            'gpa' => $gpa,
            'semester' => $student->student_semester,
            'credits_limit' => $credits_limit,
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
    public function dropCourse($studId, $courseId)
    {
        $advisorId = Auth()->user()->advisor->advisor_id;

        // $student = Student::where('student_id', $studId)->firstWhere('advisor_id', $advisorId)->first();

        $studentCourseAdvisorId = StudentCourse::findOrFail($studId)->student->advisor->advisor_id;
        // $studentCourseAdvisorDept = StudentCourse::findOrFail($courseId)->course->course_id;

        $course = StudentCourse::where('course_id', $courseId)
            ->where('student_id', $studId)
            ->where('status', 'taken')
            ->delete();

        if($advisorId !== $studentCourseAdvisorId){
            return response()->json([
                'status' => $course,
                'message' => "You are not the advisor of this student"
            ]);
        }

        return response()->json([
            'status' => $course,
            'message' => "Course dropped successfully",
            'advisor_id' => $advisorId,
            'student_course_advisor_id' => $studentCourseAdvisorId, // For debugging, will delete later
            // 'course_id' => $studentCourseAdvisorDept // For debugging, will delete later
        ]);
    }
}
