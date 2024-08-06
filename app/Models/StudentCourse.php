<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Traits\HasCompositePrimaryKey;

class StudentCourse extends Model
{
    use HasFactory;
    // use HasCompositePrimaryKey;

    protected $primaryKey = 'student_id';
    // protected $primaryKey = 'course_id';
    public $incrementing = false;

    protected $fillable = [
        'student_id',
        'student_id_number',
        'course_id',
        'course_semester_taken',
        'grade',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
