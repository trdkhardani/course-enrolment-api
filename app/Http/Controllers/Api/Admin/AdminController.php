<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Course;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $courseData = $request->validate([
            'course_name' => 'required',
            'course_code' => 'required',
            'course_class' => 'required',
            'course_capacity' => 'required',
            'course_credits' => 'required',
            // 'department_id' => 'required'
        ]);

        $courseData['department_id'] = Auth()->user()->admin->department->department_id;

        $course = Course::create($courseData);

        return response()->json([
            'status' => 1,
            'course' => $course
        ]);
    }

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
    public function destroy(string $id)
    {
        //
    }
}
