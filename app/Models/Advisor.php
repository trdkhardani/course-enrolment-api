<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    use HasFactory;

    protected $guarded = ['advisor_id'];
    protected $primaryKey = 'advisor_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        return $this->hasMany(Student::class, 'advisor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
