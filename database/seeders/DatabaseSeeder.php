<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Student;
use App\Models\Advisor;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Course;
use App\Models\StudentCourse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create( // id 1
            [
                // 'name' => 'The Student',
                'user_id_number' => '5027211049',
                'email' => 'student@mail.com',
                'user_role' => 'student',
            ]
        );

        User::factory()->create( // id 2
            [
                // 'name' => 'The Advisor',
                'user_id_number' => '5258778128',
                'email' => 'advisor@mail.com',
                'user_role' => 'advisor',
            ]
        );

        User::factory()->create( // id 3
            [
                // 'name' => 'The Admin',
                'user_id_number' => '85712364821',
                'email' => 'admin_it@mail.com',
                'user_role' => 'admin',
            ]
        );

        User::factory()->create( // id 4
            [
                // 'name' => 'The Admin',
                'user_id_number' => '5027221099',
                'email' => 'student2@mail.com',
                'user_role' => 'student',
            ]
        );

        User::factory()->create( // id 5
            [
                // 'name' => 'The Admin',
                'user_id_number' => '85513412512',
                'email' => 'admin_cs@mail.com',
                'user_role' => 'admin',
            ]
        );

        Department::factory()->create(
            [
                'department_name' => "Information Technology"
            ]
        );

        Department::factory()->create(
            [
                'department_name' => "Computer Science"
            ]
        );

        Advisor::factory()->create(
            [
                'department_id' => 1,
                'user_id' => 2,
                'advisor_name' => "The Advisor",
            ]
        );

        Student::factory()->create(
            [
                'department_id' => 1,
                'user_id' => 1,
                'advisor_id' => 1,
                'student_name' => "The Student",
                'student_semester' => 5,
            ],
        );

        Student::factory()->create(
            [
                'department_id' => 1,
                'user_id' => 4,
                'advisor_id' => 1,
                'student_name' => "The Student 2",
                'student_semester' => 3,
            ],
        );

        Admin::factory()->create(
            [
                'department_id' => 1,
                'user_id' => 3,
            ]
        );

        Admin::factory()->create(
            [
                'department_id' => 2,
                'user_id' => 5,
            ]
        );

        Course::factory()->create(
            [
                'department_id' => 1,
                'course_name' => "Algorithms and Programming 101",
                'course_code' => 'IT101',
                'course_class' => 'A',
                'course_capacity' => 40,
                'course_credits' => 4,
            ]
        );

        Course::factory()->create(
            [
                'department_id' => 1,
                'course_name' => "Web Development",
                'course_code' => 'IT205',
                'course_class' => 'A',
                'course_capacity' => 35,
                'course_credits' => 3,
            ]
        );

        Course::factory()->create(
            [
                'department_id' => 1,
                'course_name' => "Software Design",
                'course_code' => 'IT203',
                'course_class' => 'A',
                'course_capacity' => 40,
                'course_credits' => 3,
            ]
        );

        StudentCourse::factory()->create(
            [
                'student_id' => 1,
                'course_id' => 1,
                'course_semester_taken' => 4,
                'grade' => 3.5,
                'status' => 'finished',
            ]
        );

        StudentCourse::factory()->create(
            [
                'student_id' => 1,
                'course_id' => 2,
                'course_semester_taken' => 4,
                'grade' => 4,
                'status' => 'finished',
            ]
        );

        StudentCourse::factory()->create(
            [
                'student_id' => 1,
                'course_id' => 3,
                'course_semester_taken' => 4,
                'grade' => 3,
                'status' => 'finished',
            ]
        );

        StudentCourse::factory()->create(
            [
                'student_id' => 2,
                'course_id' => 2,
                'course_semester_taken' => 2,
                'grade' => 3,
                'status' => 'finished',
            ]
        );

        StudentCourse::factory()->create(
            [
                'student_id' => 2,
                'course_id' => 3,
                'course_semester_taken' => 2,
                'grade' => 3,
                'status' => 'finished',
            ]
        );
    }
}
