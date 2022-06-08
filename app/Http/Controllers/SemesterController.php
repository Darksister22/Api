<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SemesterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['destroy', 'create', 'update']);
    }
    public function create(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'year' => 'required',
        ]);
        $semester = Semester::all()->last();
        $isEnded = $semester->isEnded;
        if (!Gate::allows('is-super')) {
            return response('انت غير مخول', 403);
        }
        if ($isEnded==1)
        {
            Semester::create([
                'isEnded'=>false,
                'number'=> $request->number,
                'year' =>  $request->year,
                
            ]); 
            }  
        else return response('غير مسموح',409);
      }
    public function show(){
        $sem = Semester::select("*")->get();
     //   $sem = Semester::select("*")->where("number","=","first")->get();

        return $sem; 
    }


        public function end()
        {

            $semester = Semester::all()->last();
            $isEnded = $semester->isEnded;
            if ($isEnded==0)
            {
                $semester->isEnded = 1; 
                $semester -> save();
                }  
            else return response('غير مسموح',409); 
         }
}
