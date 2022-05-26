<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Gate;

class StudentController extends Controller
{

            /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['destroy', 'create', 'update']);
    }
    public function showAll(){
        $students = Student::select('*')->get();
        return $students;
    }
    public function create(Request $request){
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'level'=>'required',
            'year' => 'required',
        ]);
        
            Student::create([
                'name_ar' => $request->name_ar,
                'name_en' =>  $request->name_en,
                'level'=> $request->level,
                'year' =>  $request->year,
            ]); 
    }
    public function destroy($id)
    {
        Student::destroy($id);
        return response('تم حذف الطالب بنجاح', 200);
    }

}
