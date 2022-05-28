<?php

namespace App\Http\Controllers;

use App\Models\Carry;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['destroy', 'create', 'update']);
    }
    public function showAll()
    {
        $semester = Semester::where('isEnded', '=', false)->first();
        $id = $semester->id;
        $courses = Course::with('studentsCarry')->select('*')->where('semester_id', "=", $id)->get();
        $newList = [];
        foreach ($courses as $course) {
            $students = Student::select('*')->where('level', '=', $course->level)
                ->where('year', '=', $course->year)
                ->get();
            $course['students'] = $students;
            array_push($newList, $course);
        }
        return $newList;
    }
    public function create(Request $request)
    {
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'level' => 'required',
            'code' => 'required',
            'unit' => 'required',
            'year' => 'required'
        ]);
        $semester = Semester::where('isEnded', '=', false)->first();
        Course::create([
            'name_ar' => $request->name_ar,
            'name_en' =>  $request->name_en,
            'level' => $request->level,
            'code' => $request->code,
            'semester_id' => $semester->id,
            'unit' => $request->unit,
            'year' => $request->year,
        ]);
    }
    public function destroy($id)
    {
        Course::destroy($id);
        return response('تم حذف الكورس بنجاح', 200);
    }
}