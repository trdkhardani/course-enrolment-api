<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['course_id'];
    protected $primaryKey = 'course_id';

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function student()
    {
        return $this->belongsToMany(Student::class, 'student_courses', 'course_id', 'student_id')
        ->withPivot('student_id_number', 'course_semester_taken', 'grade', 'status');
    }
}
