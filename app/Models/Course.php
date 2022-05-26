<?php

namespace App\Models;
use App\Models\Semester;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'semester_id',
        'level',
        'year',
        'name_ar',
        'name_en',
        'code',
        'success',
        'unit'
    ];
    public function instructors()
    {
        return $this->hasMany(Instructor::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
