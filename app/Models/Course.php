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
        return $this->belongsToMany(Course::class, 'student_courses', 'course_id', 'student_id')
        ->withPivot('course_semester_taken', 'grade');
    }
}
