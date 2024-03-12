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
        //
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
    public function show(string $id)
    {
        //
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
