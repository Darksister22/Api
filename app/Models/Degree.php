<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\Student;

class Degree extends Model
{
    public function courses()
    {
        return $this->belongsTo(Course::class);
    }
    public function students()
    {
        return $this->belongsTo(Student::class);
    }
    
}