<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use App\Models\Advisor;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentCourse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Others\CalculateGPAController;

class AdvisorController extends Controller
{
    /**
     * See advisor's personal info
     */
    public function index()
    {
        // Get advisor_id
        $advisorId = Auth()->user()->advisor->advisor_id;

        // Get logged in advisor's data
        $advisorInfo = Advisor::findOrFail($advisorId);

        // Get logged in advisor's students
        $students = Student::where('advisor_id', $advisorId)->get();

        foreach ($students as $student) {
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

    /**
     * Enroll student's taken courses
     */
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

    /**
     * Unenroll student's enrolled courses
     */
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
     * See detailed info of a student
     */
    public function showStudentDetail($studId)
    {
        // Get advisor_id
        $advisorId = Auth()->user()->advisor->advisor_id;

        // Find searched student in param
        $student = Student::findOrFail($studId);

        // student's advisor_id
        $studentAdvisorId = $student->advisor->advisor_id;

        // Check if the advisor has the searched student
        if ($studentAdvisorId !== $advisorId) {
            return response()->json([
                'message' => 'No student found',
            ], 404);
        }

        // Calculate GPA
        $calculateGPA = new CalculateGPAController();
        $calculateGPAResult = $calculateGPA->calculateGPA($studId);

        return response()->json([
            'student_name' => $student->student_name,
            'student_id_number' => $student->user->user_id_number,
            'gpa' => $calculateGPAResult[3], // $gpa from returned array in calculateGPA() method
            'semester' => $student->student_semester,
            'credits_limit' => $calculateGPAResult[4], // $credits_limit from returned array in calculateGPA() method
        ]);
    }

    /**
     * Take course(s) for a student
     */
    public function takeCourse(Request $request, $studId)
    {
        // Get advisor_id
        $advisorId = Auth()->user()->advisor->advisor_id;

        // Get advisor_id of the student
        $studentCourseAdvisorId = StudentCourse::findOrFail($studId)->student->advisor->advisor_id;

        $courseData = $request->validate([
            'course_id' => 'required'
        ]);

        $course = Course::find($courseData['course_id']);


        $courseData['student_id'] = $studId;
        $courseData['course_semester_taken'] = Student::findOrFail($courseData['student_id'])->student_semester;
        $courseData['status'] = 'taken';

        $student = Student::where('student_id', $courseData['student_id'])->first();
        $studentCourses = $student->course;

        /** Current Semester Courses */
        $studentCurrent = Student::findOrFail($courseData['student_id']);
        $studentCurrentCourses = $studentCurrent->findOrFail($courseData['student_id'])
            ->course()
            ->where('course_semester_taken', $student->student_semester)
            ->whereIn('status', ['taken', 'enrolled'])
            ->get();
        /** END */

        $courseIsAccepted = StudentCourse::find($courseData['student_id'])
            ->where('course_semester_taken', $courseData['course_semester_taken'])
            ->firstWhere('status', 'enrolled');

        $courseIsTaken = $studentCourses->where('course_code', $course->course_code)->isNotEmpty();

        $studentDepartmentId = Student::findOrFail($courseData['student_id'])->department_id;

        $courseIsFull = $course->course_capacity <= StudentCourse::where('course_id', $courseData['course_id'])
            ->whereIn('status', ['taken', 'enrolled'])
            ->count();

        if ($advisorId !== $studentCourseAdvisorId) { // Check if the advisor has the selected student
            return response()->json([
                'status' => 0,
                'message' => 'No student found',
            ]);
        } elseif ($courseIsAccepted) { // If the selected courses has been accepted by the advisor
            return response()->json([
                'status' => 0,
                'message' => "The selected courses have already been accepted by you. Cancel first"
            ], 409);
        } elseif ($courseIsTaken) { // If course has already taken by the logged in student
            return response()->json([
                'status' => 0,
                'message' => "This student is already taken this course",
                'course_code' => $course->course_code,
            ], 409);
        } elseif ($course->course_is_enrichment === 0 && $studentDepartmentId !== $course->department_id) { // If course is not an enrichment course
            return response()->json([
                'status' => 0,
                'message' => "This course is not an enrichment course",
                'course_code' => $course->course_code,
            ], 409);
        } elseif ($course->course_is_open === 0) { // If course is not open
            return response()->json([
                'status' => 0,
                'message' => "This course is not available",
                'course_code' => $course->course_code,
            ], 409);
        } elseif ($courseIsFull) { // If course is full
            return response()->json([
                'status' => 0,
                'message' => "This course is full",
                'course_code' => $course->course_code,
            ], 409);
        } elseif ($studentCurrentCourses->sum('course_credits') + $course->course_credits > 24) { // If an advisor trying to take course more than 24 credits for the student
            return response()->json([
                'status' => 0,
                'message' => "This student has reached the allowed maximum credits"
            ], 409);
        }

        $takenCourse = StudentCourse::create($courseData);
        return response()->json([
            'status' => 1,
            'course' => $takenCourse,
            'student_name' => Student::findOrFail($courseData['student_id'])->student_name,
            'cap' => $course->course_capacity
        ]);
    }

    /**
     * Drop course(s) for a student
     */
    public function dropCourse($studId, $courseId)
    {
        $advisorId = Auth()->user()->advisor->advisor_id;

        $studentCourseAdvisorId = StudentCourse::findOrFail($studId)->student->advisor->advisor_id;

        $course = StudentCourse::where('course_id', $courseId)
            ->where('student_id', $studId)
            ->where('status', 'taken')
            ->delete();

        if ($advisorId !== $studentCourseAdvisorId) {
            return response()->json([
                'status' => $course,
                'message' => "You are not the advisor of this student"
            ]);
        } elseif ($course == null) {
            return response()->json([
                'status' => $course, // will return true or false
                'message' => "Course not found or may have been accepted by you"
            ], 409);
        }

        return response()->json([
            'status' => $course,
            'message' => "Course dropped successfully",
            'advisor_id' => $advisorId,
        ]);
    }
}
