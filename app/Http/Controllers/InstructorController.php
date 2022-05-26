<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\course_instructor;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['destroy', 'create', 'update']);
    }
    public function showAll(){
        $instructor = Instructor::select('*')->get();
        return $instructor;
    }
    public function create(Request $request){
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);
        Instructor::create([
            'name_ar' => $request->name_ar,
            'name_en' =>  $request->name_en,
        ]); 
    }

    public function setcourse(Request $request){

        // course_instructor::create([
        //     'course_id'=>$request->course_id,
        //     'instructors_id'=>$request->instructor_id,
        // ])  ;
        $ins = Instructor::with("courses")->select('*')->get();
         return $ins;
    }
    public function destroy($id)
    {
        Instructor::destroy($id);
        return response('تم حذف التدريسي بنجاح', 200);
    }


}
