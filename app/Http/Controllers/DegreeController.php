<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Degree;
use App\Models\Degreeh;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DegreeController extends Controller
{
    public function createhelp(Request $request)
    {        $request->validate([
        'source' => 'required',
        'amt' => 'required',
    ]);
    if (!Gate::allows('is-super')) {  
        return response('انت غير مخول', 403);
    }
    Degreeh::create([
        'source' => $request->source,
        'amt' =>  $request->amt,
    ]); 
    }

    
    public function getStudentDegrees(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'course_id' => 'required',
        ]);
        //get student with degrees 
        $s = Student::with(['degrees' => function ($q) use ($request) {
            $q->where('course_id', '=', $request->course_id);
        }])->where('id', '=', $request->student_id)->get();

        //get degrees of a student
        $d = Degree::select('*')->where('course_id', '=', $request->course_id)->where('student_id', '=', $request->student_id)->get();


        return [
            's' => $s,
            'd' => $d,

        ];
    }
    public function createStudentDegrees(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'course_id' => 'required',
        ]);
        $course = Course::select('*')->where('id','=',"$request->course_id")->first();
        $student = Student::select('*')->where('id','=',"$request->student_id")->first();
        
        
            $results=Degree::where('student_id',"=","$student->id")->where('course_id','=',"$course->id")->first();
            //code...
            if ($results == null ) {
                
                Degree::create([
                    'student_id'=>$student->id,
                    'course_id'=>$course->id
                ]);
                return($results);
            }
            else return ($results);

        

      
    }
}