<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Advisor;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;

class Student extends Model
{
    use HasFactory;

    protected $guarded = ['student_id'];
    protected $primaryKey = 'student_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function advisor()
    {
        return $this->belongsTo(Advisor::class, 'advisor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function course()
    {
        return $this->belongsToMany(Course::class, 'student_courses', 'student_id', 'course_id')
        ->withPivot('course_semester_taken', 'grade', 'status');
    }
}
