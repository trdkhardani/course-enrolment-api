<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Course;

class AdminController extends Controller
{
    /**
     * See all courses of respective department
     */
    public function index()
    {
        $dept_id = Auth()->user()->admin->department->department_id;
        $courses = Course::all()->where('department_id', $dept_id);

        return response()->json([
            'status' => 1,
            'courses_data' => $courses,
        ]);
    }

    /**
     * Add course for respective department
     */
    public function addCourse(Request $request)
    {
        $courseData = $request->validate([
            'course_name' => 'required',
            'course_code' => 'required',
            'course_class' => 'required',
            'course_capacity' => 'required',
            'course_credits' => 'required',
            'course_is_enrichment' => 'required'
        ]);

        $courseData['department_id'] = Auth()->user()->admin->department->department_id;

        $course = Course::create($courseData);

        return response()->json([
            'status' => 1,
            'course' => $course
        ]);
    }

    /**
     * Change course availability (open or closed)
     */
    public function changeCourseAvailability($courseCode, $courseClass, $status)
    {
        $dept_id = Auth()->user()->admin->department->department_id;
        $course = Course::where('course_code', $courseCode)->firstWhere('course_class', $courseClass);

        if ($course === null) {
            return response()->json([
                'status' => 0,
                'message' => "The " . $courseCode . " course or the class does not exist"
            ], 404);
        } elseif ($dept_id !== $course->department->department_id) {
            return response()->json([
                'status' => 0,
                'message' => "The " . $course->course_name . " course belongs to " . $course->department->department_name . " department"
            ], 403);
        }

        $courseData['course_is_open'] = $status;

        $course->update($courseData);

        return response()->json([
            'status' => 1,
            'data' => $course
        ], 200);
    }
}
