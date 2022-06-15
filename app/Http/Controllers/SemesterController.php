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
            'number' => 'required',
            'year' => 'required',
        ]);
        $semester = Semester::all()->last();
        $isEnded = $semester->isEnded;
        $number = $semester->number;
        if (!Gate::allows('is-super')) {
            return response('انت غير مخول', 403);
        }
        if ($isEnded == 1 && $number == "first") {
            if ($semester->year != $request->year) {
                return response("لا يوجد فصل اول لهذه السنة الدراسية", 410);
            }
            $check = Semester::select('*')->where("number", '=', 'second')->get()->last();
            $sem = Semester::create([
                'isEnded' => false,
                'number' => $request->number,
                'year' => $request->year,

            ]);
            $id = $sem->id;
            $courses = Course::select('*')->where('semester_id', '=', "$check->id")->get();

            foreach ($courses as $courses) {

                $carries = Carry::select("*")->where('course_id', "=", "$courses->id")->get();
                if ($carries != "[]") {
                    $new = Course::create([
                        'name_ar' => $courses->name_ar,
                        'name_en' => $courses->name_en,
                        'instructor_id' => $courses->instructor_id,
                        'level' => $courses->level,
                        'code' => $courses->code,
                        'semester_id' => $id,
                        'unit' => $courses->unit,
                        'year' => $courses->year,
                        'success' => $courses->success,
                    ]);
                    $nid = $new->id;
                    foreach ($carries as $carries) {
                        $carries->course_id = $nid;
                        $carries->save();
                    }
                }

            }

        } else if ($isEnded == 1 && $number == "second") {
            $check = Semester::select('*')->where("number", '=', 'first')->get()->last();
            $sem = Semester::create([
                'isEnded' => false,
                'number' => $request->number,
                'year' => $request->year,

            ]);
            $id = $sem->id;
            $courses = Course::select('*')->where('semester_id', '=', "$check->id")->get();

            foreach ($courses as $courses) {

                $carries = Carry::select("*")->where('course_id', "=", "$courses->id")->get();
                if ($carries != "[]") {
                    $new = Course::create([
                        'name_ar' => $courses->name_ar,
                        'name_en' => $courses->name_en,
                        'instructor_id' => $courses->instructor_id,
                        'level' => $courses->level,
                        'code' => $courses->code,
                        'semester_id' => $id,
                        'unit' => $courses->unit,
                        'year' => $courses->year,
                        'success' => $courses->success,
                    ]);
                    $nid = $new->id;
                    foreach ($carries as $carries) {
                        $carries->course_id = $nid;
                        $carries->save();
                    }
                }

            }
        } else {
            return response('غير مسموح', 409);
        }

    }
    public function show()
    {
        $sem = Semester::select("*")->where("number", "=", "first")->get();

        return $sem;
    }

    public function end()
    {

        $semester = Semester::all()->last();
        $isEnded = $semester->isEnded;
        if ($isEnded == 0) {
            $semester->isEnded = 1;
            $semester->save();
        } else {
            return response('غير مسموح', 409);
        }

    }
}
