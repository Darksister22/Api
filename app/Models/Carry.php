<?php

namespace App\Models;
use App\Models\Student;
use App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carry extends Model
{
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
