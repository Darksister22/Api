<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'name',
        'year',
        'isEnded',

    ];
    use HasFactory;
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
