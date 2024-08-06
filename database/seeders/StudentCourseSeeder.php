<?php

namespace Database\Seeders;

use App\Models\StudentCourse;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StudentCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentCourse::create(
            [
                'student_id' => 1,
                'course_id' => 1,
                'course_semester_taken' => 4,
                'grade' => 3.5,
                'status' => 'finished',
            ]
        );

        StudentCourse::create(
            [
                'student_id' => 1,
                'course_id' => 2,
                'course_semester_taken' => 4,
                'grade' => 4,
                'status' => 'finished',
            ]
        );

        StudentCourse::create(
            [
                'student_id' => 1,
                'course_id' => 3,
                'course_semester_taken' => 4,
                'grade' => 3,
                'status' => 'finished',
            ]
        );

        StudentCourse::create(
            [
                'student_id' => 2,
                'course_id' => 2,
                'course_semester_taken' => 2,
                'grade' => 3,
                'status' => 'finished',
            ]
        );

        StudentCourse::create(
            [
                'student_id' => 2,
                'course_id' => 3,
                'course_semester_taken' => 2,
                'grade' => 3,
                'status' => 'finished',
            ]
        );

        StudentCourse::create(
            [
                'student_id' => 3,
                'course_id' => 2,
                'course_semester_taken' => 4,
                'grade' => 3,
                'status' => 'finished',
            ]
        );
    }
}
