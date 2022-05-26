<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Degree;
use App\Models\Degreeh;
use App\Models\Student;
use Illuminate\Http\Request;

class DegreeController extends Controller
{
    public function createhelp(Request $request){
        $request->validate([
            'source' => 'required',
            'amt' => 'required',
        ]);
        Degreeh::create([
            'source' => $request->source,
            'amt' =>  $request->amt,
        ]); 
    }
    public function create(Request $request){
        $id = $request->student_id; 
        $crs = $request->course_id;
        $category = Student::find([$id]);
        $crs = Course::find([$crs]);
        $product = new Degree();
        

        $product = $id->with('students')->save($category);
        $product = $crs->courses()->save($crs);
        
        $product->save();
    }
}
