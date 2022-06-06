<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Degree;
use App\Models\Degreeh;
use App\Models\Semester;
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

    
    public function getDegrees2(Request $request)
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
    public function getDegrees(Request $request)
    {
        $deg = Degree::whereHas('courses', function($q) {
            $semester = Semester::select('*')->get()->last();
            $id = $semester->id;
            $q->where('semester_id', '=', $id);
        })->with("courses")->with("student")->get();
         return $deg;
    }

    public function countDegree(Request $request){
        $deg = Degree::whereHas('courses', function($q) {
            $semester = Semester::where('isEnded', '=', false)->first();
            $id = $semester->id;
            $q->where('semester_id', '=', $id);
        })->with("courses")->with("student")->get();
        foreach ($deg as $deg) {
           $course = Course::select('*')->where('id','=',"$deg->course_id")->first(); 
           if($deg->sixty3 != null ){
            $total = $deg->fourty + $deg->sixty3;
            if($total>$course->success){
                $deg->final3=$total;
                $deg -> sts = "pass";
                $deg->approx= "B";
             }
            else {
                $deg->final3=$total;
                $deg -> sts = "fail";
                $deg->approx= "F";
            }}
            if($deg->sixty2 != null ){
                $total = $deg->fourty + $deg->sixty2;
                if($total>$course->success){
                    $deg->final2=$total;
                    $deg -> sts = "pass";
                    $deg->approx= $this->getApprox($total,$course->success);
                }
                else {
                    $deg->final2=$total;
                    $deg -> sts = "fail";
                    $deg->approx= $this->getApprox($total,$course->success);
                }}
                if($deg->sixty1 != null ){
                    $total = $deg->fourty + $deg->sixty1;
                    if($total>$course->success){
         
                        $deg->final1=$total;
                        $deg -> sts = "pass";
                        $deg->approx= $this->getApprox($total,$course->success);
                    }
                    else {
                        $deg->final1=$total;
                        $deg -> sts = 'fail';
                        $deg->approx= $this->getApprox($total,$course->success);
                    }}
                    $deg->save();
        }
    }
    public function getApprox($deg,$success){
        if($deg<$success) return "F";
        if($deg<=100 && $deg>=90) return "A";
        if($deg<90 && $deg>=80) return "B";
        if($deg<80 && $deg>=70) return "C";
        if($deg<70 && $deg>=60) return "D";
        if($deg<60 && $deg>=50) return "E";
    }
    public function getForty(Request $request)
    {

        $request->validate([
            'course_id'=>"required"
        ]);
            $deg = Degree::whereHas('courses', function($q) use ($request){
            $semester = Semester::where('isEnded', '=', false)->first();
            $id = $semester->id;
            $q->where('semester_id', '=', $id)->where('id',"=","$request->course_id");
        })->with("courses")->with("student")->get();
         return $deg;
      
    }
    public function getStudentDegrees(Request $request){
        $request->validate([
            'course_id'=> "required",
            'student_id'=>"required"
        ]);
        $results=Degree::where('student_id',"=","$request->student_id")->where('course_id','=',"$request->course_id")->first();
        return response($results,200);
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
                    'course_id'=>$course->id,
                    'fourty'=>$request->fourty,
                    'sixty1'=>$request->sixty1,
                    'sixty2'=>$request->sixty2,
                    'sixty3'=>$request->sixty3,
                ]);
                return response('تم الاضافة',200);
            }
            else if($results !=null){
               $deg = Degree::select('*')->where('student_id',"=","$student->id")->where("course_id","=","$course->id")->first();
               $deg->fourty = $request->fourty;
               $deg->sixty1 = $request->sixty1;
               $deg->sixty2 = $request->sixty2;
               $deg->sixty3 = $request->sixty3;
               $deg->save();
               return response('تم التحديث',200);

            };

        

      
    }
}