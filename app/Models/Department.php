<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = ['department_id'];
    protected $primaryKey = 'department_id';

    public function student()
    {
        return $this->hasMany(Student::class, 'department_id');
    }

    public function advisor()
    {
        return $this->hasMany(Advisor::class, 'department_id');
    }

    public function admin()
    {
        return $this->hasMany(Admin::class, 'department_id');
    }

    public function courses()
    {
        return $this->hasMany(Courses::class, 'department_id');
    }
}
