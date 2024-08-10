<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\Course;
use App\Models\StudentCourse;

use App\Http\Controllers\Others\CalculateGPAController;

class StudentController extends Controller
{
    /**
     * See logged in student's personal info
     */
    public function index()
    {
        $studId = Auth()->user()->student->student_id;

        $student = Student::findOrFail($studId);

        $calculateGPA = new CalculateGPAController();
        $calculateGPAResult = $calculateGPA->calculateGPA($studId);

        return response()->json([
            'name' => $student->student_name,
            'student_id' => $student->user->user_id_number,
            'advisor_name' => $student->advisor->advisor_name,
            'credits_total' => $calculateGPAResult[0], // $studentCourses->sum('course_credits') from returned array in calculateGPA() method
            'courses' => $calculateGPAResult[1], // $studentCourseData from returned array in calculateGPA() method
            'c_g_sum' => $calculateGPAResult[2], // $totalCGProduct from returned array in calculateGPA() method
            'gpa' => $calculateGPAResult[3], // $gpa from returned array in calculateGPA() method
            'semester' => $student->student_semester,
            'credits_limit' => $calculateGPAResult[4] // $credits_limit from returned array in calculateGPA() method
        ]);
    }

    /**
     * See all open and enrichment courses
     */
    public function availableCourses()
    {
        $dept_id = Auth()->user()->student->department->department_id;

        $courses = Course::where('department_id', $dept_id)->where('course_is_open', 1)->get();

        $enrichmentCourses = Course::where('course_is_enrichment', 1)->get();

        $studentTotal = fn($courseId) => StudentCourse::where('course_id', $courseId)
        ->where('status', 'taken')
        ->orWhere('status', 'enrolled')
        ->count('course_id');
        // Query => SELECT COUNT(course_id) AS course_total_students FROM student_courses WHERE course_id LIKE $courseId AND status LIKE 'taken' AND status LIKE 'enrolled';

        foreach ($courses as $course) {
            $courseData[] = [
                'course_name' => $course->course_name,
                'course_code' => $course->course_code,
                'course_class' => $course->course_class,
                'course_total_students' => $studentTotal($course->course_id) . " / " . $course->course_capacity,
                'course_credits' => $course->course_credits,
            ];
        }

        return response()->json([
            'status' => 1,
            'courses' => $courseData,
            'enrichment_courses' => $enrichmentCourses,
        ]);
    }

    /**
     * Take course(s)
     */
    public function takeCourse(Request $request)
    {
        $courseData = $request->validate([
            'course_id' => 'required'
        ]);

        $course = Course::find($courseData['course_id']);


        $courseData['student_id'] = Auth()->user()->student->student_id;
        $courseData['course_semester_taken'] = Auth()->user()->student->student_semester;
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

        /** Calculate GPA Credits Limit */
        $calculateGPA = new CalculateGPAController();
        $calculateGPAResult = $calculateGPA->calculateGPA($courseData['student_id']);
        $credits_limit = $calculateGPAResult[4]; // $credits_limit from returned array in calculateGPA() method
        /** END */

        $courseIsAccepted = StudentCourse::find($courseData['student_id'])
            ->where('course_semester_taken', $courseData['course_semester_taken'])
            ->firstWhere('status', 'enrolled');

        $courseIsTaken = $studentCourses->where('course_code', $course->course_code)->isNotEmpty();

        $studentDepartmentId = Student::findOrFail($courseData['student_id'])->department_id;

        $courseIsFull = $course->course_capacity <= StudentCourse::where('course_id', $courseData['course_id'])
            ->whereIn('status', ['taken', 'enrolled'])
            ->count();

        if ($courseIsAccepted) { // If the selected courses has been accepted by the advisor
            return response()->json([
                'status' => 0,
                'message' => "Your selected courses has already been accepted by your advisor. Ask your advisor to cancel enrolment"
            ], 409);
        } elseif ($courseIsTaken) { // If course has already taken by the logged in student
            return response()->json([
                'status' => 0,
                'message' => "You are already taken this course",
                'course_code' => $course->course_code,
            ], 409);
        } elseif ($course->course_is_enrichment === 0 &&  $studentDepartmentId->department_id !== $course->department_id) { // If course is not an enrichment course
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
        } elseif ($studentCurrentCourses->sum('course_credits') + $course->course_credits > $credits_limit) { // If a student trying to take course more than the given limit
            return response()->json([
                'status' => 0,
                'message' => "You have reached your credit limit"
            ], 409);
        }

        $takenCourse = StudentCourse::create($courseData);
        return response()->json([
            'status' => 1,
            'course' => $takenCourse,
            'cap' => $course->course_capacity
        ], 201);
    }

    /**
     * See currently taken or enrolled courses
     */
    public function showCurrentCourses()
    {
        $studId = Auth()->user()->student->student_id;
        $studCurrentSemester = Auth()->user()->student->student_semester;

        $currentCourses = Student::findOrFail($studId)
        ->course()
        ->where('course_semester_taken', $studCurrentSemester)
        ->get();

        $coursesEnrolmentPending = Student::findOrFail($studId)->course()->firstWhere('status', 'taken');
        $coursesEnrolmentAccepted = Student::findOrFail($studId)->course()->firstWhere('status', 'enrolled');

        foreach ($currentCourses as $currentCourse) {
            $currentCourseData[] = [
                'course_name' => $currentCourse->course_name,
                'course_code' => $currentCourse->course_code,
                'course_class' => $currentCourse->course_class,
                'course_credits' => $currentCourse->course_credits,
            ];
        }

        if ($coursesEnrolmentPending) {
            $enrolmentStatus = 'pending';
        } elseif ($coursesEnrolmentAccepted) {
            $enrolmentStatus = 'enrolled';
        }

        return response()->json([
            'status' => 1,
            'courses' => $currentCourseData,
            'credits_total' => $currentCourses->sum('course_credits'),
            'enrolment_status' => $enrolmentStatus
        ]);
    }

    /**
     * See course detail
     */
    public function showCourseDetail($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Find students who are currently taken or enrolled in the selected course
        $courseStudent = $course->student()
            ->where('course_id', $courseId)
            ->whereIn('status', ['taken', 'enrolled']);

        $totalEnrolledStudents = $courseStudent->count();

        $enrolledStudents = $courseStudent->get();

        foreach ($enrolledStudents as $enrolledStudent) {
            $enrolledStudentData[] = [
                'student_name' => $enrolledStudent->student_name,
                'student_id_number' => $enrolledStudent->user->user_id_number,
            ];
        }

        // Check whether the course is open, closed, or full
        if ($course->course_is_open === 0) {
            $courseStatus = "Closed";
        } elseif ($totalEnrolledStudents === $course->course_capacity) {
            $courseStatus = "Full";
        } else {
            $courseStatus = "Open";
        }

        return response()->json([
            'course_name' => $course->course_name,
            'course_class' => $course->course_class,
            'total_enrolled_students' => $totalEnrolledStudents . " / " . $course->course_capacity,
            'course_status' => $courseStatus,
            'enrolled_students' => $enrolledStudentData,
        ]);
    }

    /**
     * Drop taken courses
     */
    public function dropCourse($courseId)
    {
        $studId = Auth()->user()->student->student_id;
        $course = StudentCourse::where('course_id', $courseId)
            ->where('student_id', $studId)
            ->where('status', 'taken') // Can only drop course that the status is 'taken'
            ->delete();

        if ($course == null) {
            return response()->json([
                'status' => $course, // will return true or false
                'message' => "Course not found or may have been accepted by your advisor"
            ], 409);
        }

        return response()->json([
            'status' => $course, // will return true or false
            'message' => "Course dropped successfully"
        ]);
    }
}
