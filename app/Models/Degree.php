<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\Student;

class Degree extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'fourty',
        'sixty1',
        'sixty2',
        'sixty3',
        'final1',
        'final2',
        'final3',
        'approx',
        'avg',
        'sts'
    ];
    public function courses()
    {
        return $this->belongsTo('App\Models\Course','course_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
}
