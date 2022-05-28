<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course_instructor extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'instructor_id',
    ];

    protected $table = 'course_instructor';

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}