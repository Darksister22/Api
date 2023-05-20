<?php

namespace App\Http\Controllers;

use App\Models\Carry;
use App\Models\Course;
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
            'year' => 'required',
        ]);
        if (!Gate::allows('is-super')) {
            return response(403);
        }
        $semester = Semester::select("*")->selectRaw('substr(year,1,4) as first')->orderBy("first", "DESC")->orderBy("number", "DESC")->first();
        $isEnded = $semester->isEnded;
        if ($isEnded == false) {
            return (410);
        } else {
            $current1 = Semester::select("*")->where('isEnded', '=', false)->where('number', '=', 'first')->first();
            $current2 = Semester::select("*")->where('isEnded', '=', false)->where('number', '=', 'second')->first();

            $sem1 = Semester::create([
                'isEnded' => false,
                'number' => 'first',
                'year' => $request->year,
            ]);
            $sem2 = Semester::create([
                'isEnded' => false,
                'number' => 'second',
                'year' => $request->year,
            ]);
            $id1 = $sem1->id;
            $courses1 = Course::select('*')->where('semester_id', '=', "$current1->id")->get();
            foreach ($courses1 as $course) {
                $carries = Carry::select("*")->where('course_id', "=", "$course->id")->get();
                if ($carries != "[]") {
                    $new = Course::create([
                        'name_ar' => $course->name_ar,
                        'name_en' => $course->name_en,
                        'instructor_id' => $course->instructor_id,
                        'level' => $course->level,
                        'code' => $course->code,
                        'semester_id' => $id1,
                        'unit' => $course->unit,
                        'year' => $course->year,
                        'success' => $course->success,
                    ]);
                    $nid = $new->id;
                    foreach ($carries as $carry) {
                        $carry->course_id = $nid;
                        $carry->save();
                    }
                }
            }
            $id1 = $sem2->id;
            $courses2 = Course::select('*')->where('semester_id', '=', "$current2->id")->get();
            foreach ($courses2 as $course) {
                $carries = Carry::select("*")->where('course_id', "=", "$course->id")->get();
                if ($carries != "[]") {
                    $new = Course::create([
                        'name_ar' => $course->name_ar,
                        'name_en' => $course->name_en,
                        'instructor_id' => $course->instructor_id,
                        'level' => $course->level,
                        'code' => $course->code,
                        'semester_id' => $id1,
                        'unit' => $course->unit,
                        'year' => $course->year,
                        'success' => $course->success,
                    ]);
                    $nid = $new->id;
                    foreach ($carries as $carry) {
                        $carry->course_id = $nid;
                        $carry->save();
                    }
                }
            }
            return response(200);
        }
    }
    public function show()
    {
        $sem = Semester::select("*")->selectRaw('substr(year,1,4) as first')->where("number", "=", "first")->orderBy("first", "ASC")->get();
        return $sem;
    }

    public function end()
    {

        $semesters = Semester::select("*")->where("isEnded", "=", false)->get();
        foreach ($semesters as $semester) {
            $isEnded = $semester->isEnded;
            if ($isEnded == 0) {
                $semester->isEnded = 1;
                $semester->save();
                return response(200); 
            } else {
                return response(409);
            }
        }
    }
}
