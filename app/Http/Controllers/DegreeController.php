<?php

namespace App\Http\Controllers;

use App\Models\Carry;
use App\Models\Course;
use App\Models\Degree;
use App\Models\Degreeh;
use App\Models\Graduate;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;

class DegreeController extends Controller
{
    public function createhelp(Request $request)
    {$request->validate([
        'source' => 'required',
        'amt' => 'required',
    ]);

        Degreeh::create([
            'source' => $request->source,
            'amt' => $request->amt,
        ]);
    }
    public function showhelp(Request $request)
    {
        return Degreeh::select('*')->get();
    }

    public function addhelp(Request $request)
    {
        $request->validate([
            "source" => "required",
            "deg_id" => "required",
        ]);
        $deg = Degree::select("*")->where("id", "=", "$request->deg_id")->first();
        $course = Course::select("*")->where("id", "=", "$deg->course_id")->first();
        $help = Degreeh::select("*")->where("source", "=", "$request->source")->first();
        $final = 0;
        if ($deg->sixty3 != null) {
            $final = $deg->final3 + $help->amt;
            if ($final >= $course->success) {
                $dif = $course->success - $deg->sixty3;
                $deg->sixty1 = $deg->sixty3 + $dif;
                $deg->save();
            }
        } else if ($deg->sixty2 != null) {
            $final = $deg->final2 + $help->amt;
            if ($final >= $course->success) {
                $dif = $course->success - $deg->sixty2;
                $deg->sixty2 = $deg->sixty2 + $dif;
                $deg->save();
            }
        } else if ($deg->sixty1 != null) {
            $final = $deg->final1 + $help->amt;
            if ($final >= $course->success) {
                $dif = $course->success - $deg->sixty1;
                $deg->sixty1 = $deg->sixty1 + $dif;
                $deg->save();
            }
        }

    }

    public function getDegrees(Request $request)
    {
        $deg = Degree::whereHas('courses', function ($q) {
            $semester = Semester::select('*')->get()->last();
            $id = $semester->id;
            $q->where('semester_id', '=', $id);
        })->with("courses")->with("student")->get();
        return $deg;
    }
    public function getAllDegrees(Request $request)
    {
        $request->validate([
            "year" => "required",
        ]);
        $deg = Degree::with("courses")->with("student")->get();
        return $deg;
    }

    public function countDegree()
    {
        $deg = Degree::whereHas('courses', function ($q) {
            $semester = Semester::where('isEnded', '=', false)->first();
            $id = $semester->id;
            $q->where('semester_id', '=', $id);
        })->with("courses")->with("student")->get();
        foreach ($deg as $deg) {
            $course = Course::select('*')->where('id', '=', "$deg->course_id")->first();
            if ($deg->sixty3 != null) {
                $total = $deg->fourty + $deg->sixty3;
                if ($total >= $course->success) {
                    $deg->final3 = $total;
                    $deg->sts = "pass";
                    $deg->approx = $this->getApprox($total, $course->success);
                } else {
                    $deg->final3 = $total;
                    $deg->sts = "fail";
                    $deg->approx = $this->getApprox($total, $course->success);
                }} else if ($deg->sixty2 != null) {
                $total = $deg->fourty + $deg->sixty2;
                if ($total >= $course->success) {
                    $deg->final2 = $total;
                    $deg->sts = "pass";
                    $deg->approx = $this->getApprox($total, $course->success);
                } else {
                    $deg->final2 = $total;
                    $deg->sts = "fail";
                    $deg->approx = $this->getApprox($total, $course->success);
                }} else if ($deg->sixty1 != null) {
                $total = $deg->fourty + $deg->sixty1;
                if ($total >= $course->success) {

                    $deg->final1 = $total;
                    $deg->sts = "pass";
                    $deg->approx = $this->getApprox($total, $course->success);
                } else {
                    $deg->final1 = $total;
                    $deg->sts = 'fail';
                    $deg->approx = $this->getApprox($total, $course->success);
                }}
            $deg->save();
        }
    }

    public function pass(Request $request)
    {
        $request->validate([
            "grad_year" => "required",
            "level" => "required",
        ]);
        $semester = Semester::select('*')->where("isEnded", "=", "0")->first();
        if ($semester == null) {
            return response('الكورس الدراسي منتهي و لا يمكن عبور الطلبة منه', 409);
        } else if ($semester->number == 'first') {
            return response('لا يمكن عبور الطلبة من الكورس الاول', 410);
        } else {
            $stu = Student::select('*')->where("level", "=", "$request->level")->get();
            foreach ($stu as $stu) {
                //get average of student for courses only INSIDE their year. (no carries or attends)
                $degrees = Degree::select("*")->whereHas('courses', function ($q) use ($semester) {
                    $year = $semester->year;
                    $q->whereHas('semester', function ($q) use ($year) {
                        $q->where("year", "=", "$year");
                    });
                })->where("student_id", "=", "$stu->id")->get();

                $sum = 0;
                $final = 0;
                $unit = 0;
                foreach ($degrees as $degrees) {
                    $units = Course::select("unit")->where("id", "=", "$degrees->course_id")->first();
                    $unit = $unit + $units->unit;
                    if ($degrees->final3 != null) {
                        $final = $degrees->final3;
                    } else if ($degrees->final2 != null) {
                        $final = $degrees->final2;
                    } else if ($degrees->final1 != null) {
                        $final = $degrees->final1;
                    }
                    $final = $final * $units->unit;
                    $sum = $final + $sum;
                }
                //save average of student
                $avg = $sum / $unit;
                $this->setAvg($stu, $avg);

                //Determine the average of the courses the student carries
                $carries = Carry::select("*")->where("student_id", "=", "$stu->id")->get();
                if ($carries == '[]') {
                } else {
                    foreach ($carries as $carries) {
                        $year = Course::select("year")->where("id", "=", "$carries->course_id")->first();
                        $degrees = Degree::whereHas("courses", function ($q) use ($year) {
                            $q->where("year", "=", "$year->year");
                        })->where("student_id", "=", "$stu->id")->get();
                        $sum = 0;
                        $final = 0;
                        $unit = 0;
                        foreach ($degrees as $degrees) {
                            $units = Course::select("*")->where("id", "=", "$degrees->course_id")->first();
                            $unit = $unit + $units->unit;
                            if ($degrees->final3 != null) {
                                $final = $degrees->final3;
                            } else if ($degrees->final2 != null) {
                                $final = $degrees->final2;
                            } else if ($degrees->final1 != null) {
                                $final = $degrees->final1;
                            }
                            $final = $final * $units->unit;
                            $sum = $final + $sum;

                        }
                        //calculate new average of the carried/attended year
                        $avg = $sum / $unit;
                        $this->setAvgYear($stu, $year->year, $avg);
                    }

                }

                //count failed courses for first semester
                $fails1 = Degree::select("*")->whereHas('courses', function ($q) use ($semester) {
                    $year = $semester->year;
                    $q->whereHas('semester', function ($q) use ($year) {
                        $q->where("year", "=", "$year")->where("number", "=", "first");
                    });
                })->where("student_id", "=", "$stu->id")->where("sts", "=", "fail")->get();
                $failed1 = count($fails1);

                //Determine what courses the student will carry
                if ($failed1 != 0) {
                    foreach ($fails1 as $fails1) {
                        $failedcourse = $fails1->course_id;
                        Carry::create([
                            'course_id' => $failedcourse,
                            'student_id' => $stu->id,
                            'attend_carry' => 'carry',
                        ]);}

                }

                //count failed courses for second semester
                $fails2 = Degree::select("*")->whereHas('courses', function ($q) use ($semester) {
                    $year = $semester->year;
                    $q->whereHas('semester', function ($q) use ($year) {
                        $q->where("year", "=", "$year")->where("number", "=", "seconds");
                    });
                })->where("student_id", "=", "$stu->id")->where("sts", "=", "fail")->get();
                $failed2 = count($fails1);

                //Determine what courses the student will carry
                if ($failed2 != 0) {
                    foreach ($fails2 as $fails2) {
                        $failedcourse = $fails2->course_id;
                        Carry::create([
                            'course_id' => $failedcourse,
                            'student_id' => $stu->id,
                            'attend_carry' => 'carry',
                        ]);}

                }

                //Pass and graduate students
                if ($stu->year == $request->grad_year && $failed1 == 0 && $failed2 == 0) {
                    $this->grad($stu, $request->grad_year);} else if (($failed1 <= 2 && $failed2 <= 2) && $stu->year != $request->grad_year) {
                    $this->nextyear($stu);
                }
            }

        }

    }

    public function grad($stu, $gradyear)
    {
        if ($stu->year == $gradyear) {
            $stu->isGrad = true;
            $stu->save();
            Student::query()
                ->where('id', '=', "$stu->id")->each(function ($oldRecord) {
                $newRecord = $oldRecord->replicate();
                $newRecord->setTable('graduates');
                $newRecord->save();
            });
            $curgrad = Graduate::select('*')->where('name_ar', '=', "$stu->name_ar")->first();
            $finalavg = $this->getFinalAvg($curgrad);
            $curgrad->avg_final = $finalavg;
            $curgrad->save();
        }
    }
    public function getFinalAvg($stu)
    {
        $avg1 = $stu->avg1;
        $avg2 = $stu->avg2;
        $avg3 = $stu->avg3;
        $avg4 = $stu->avg4;
        $avg5 = $stu->avg5;
        if ($stu->year == 'fifth') {
            return ((0.05 * $avg1) + (0.1 * $avg2) + (0.15 * $avg3) + (0.3 * $avg4) + (0.4 * $avg5));
        } else if ($stu->year == 'fourth') {
            return ((0.1 * $avg1) + (0.2 * $avg2) + (0.3 * $avg3) + (0.4 * $avg4));
        }
    }

    public function nextyear($stu)
    {

        $year = $stu->year;
        if ($year == 'first') {
            $stu->year = 'second';
            $stu->save();
        } else if ($year == 'second') {
            $stu->year = 'third';
            $stu->save();
        } else if ($year == 'third') {
            $stu->year = 'fourth';
            $stu->save();
        } else if ($year == 'fourth') {
            $stu->year = 'fifth';
            $stu->save();
        } else if ($year == 'fifth') {
            $stu->year = 'sixth';
            $stu->save();
        } else if ($year == 'sixth') {
            $stu->year = 'seventh';
            $stu->save();
        } else if ($year == 'seventh') {
            $stu->year = 'eigth';
            $stu->save();
        } else if ($year == 'eigth') {
            $stu->year = 'ninth';
            $stu->save();
        } else if ($year == 'ninth') {
            $stu->year = 'tenth';
            $stu->save();
        }

    }

    public function setAvg($stu, $avg)
    {
        $year = $stu->year;
        if ($year == "first") {
            $stu->avg1 = $avg;
        } else if ($year == "second") {
            $stu->avg2 = $avg;
        } else if ($year == "third") {
            $stu->avg3 = $avg;
        } else if ($year == "fourth") {
            $stu->avg4 = $avg;
        } else if ($year == "fifth") {
            $stu->avg5 = $avg;
        } else if ($year == "sixth") {
            $stu->avg6 = $avg;
        } else if ($year == "seventh") {
            $stu->avg7 = $avg;
        } else if ($year == "eigth") {
            $stu->avg8 = $avg;
        } else if ($year == "ninth") {
            $stu->avg9 = $avg;
        } else if ($year == "tenth") {
            $stu->avg10 = $avg;
        }
        $stu->save();
    }
    public function setAvgYear($stu, $year, $avg)
    {
        if ($year == "first") {
            $stu->avg1 = $avg;
        } else if ($year == "second") {
            $stu->avg2 = $avg;
        } else if ($year == "third") {
            $stu->avg3 = $avg;
        } else if ($year == "fourth") {
            $stu->avg4 = $avg;
        } else if ($year == "fifth") {
            $stu->avg5 = $avg;
        } else if ($year == "sixth") {
            $stu->avg6 = $avg;
        } else if ($year == "seventh") {
            $stu->avg7 = $avg;
        } else if ($year == "eigth") {
            $stu->avg8 = $avg;
        } else if ($year == "ninth") {
            $stu->avg9 = $avg;
        } else if ($year == "tenth") {
            $stu->avg10 = $avg;
        }
        $stu->save();
    }
    public function getApprox($deg, $success)
    {
        if ($deg < $success) {
            return "F";
        }

        if ($deg <= 100 && $deg >= 90) {
            return "A";
        }

        if ($deg < 90 && $deg >= 80) {
            return "B";
        }

        if ($deg < 80 && $deg >= 70) {
            return "C";
        }

        if ($deg < 70 && $deg >= 60) {
            return "D";
        }

        if ($deg < 60 && $deg >= 50) {
            return "E";
        }

    }

    public function getForty(Request $request)
    {

        $request->validate([
            'course_id' => "required",
        ]);
        $deg = Degree::whereHas('courses', function ($q) use ($request) {
            $semester = Semester::where('isEnded', '=', false)->first();
            $id = $semester->id;
            $q->where('semester_id', '=', $id)->where('id', "=", "$request->course_id");
        })->with("courses")->with(["student" => function ($q) {
            $q->where('isGrad', "=", false);
        }])->get();
        return $deg;

    }
    public function getStudentDegrees(Request $request)
    {
        $request->validate([
            'course_id' => "required",
            'student_id' => "required",
        ]);
        $results = Degree::where('student_id', "=", "$request->student_id")->where('course_id', '=', "$request->course_id")->first();
        return response($results, 200);
    }
    public function createStudentDegrees(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'course_id' => 'required',

        ]);
        $course = Course::select('*')->where('id', '=', "$request->course_id")->first();
        $student = Student::select('*')->where('id', '=', "$request->student_id")->first();

        $results = Degree::where('student_id', "=", "$student->id")->where('course_id', '=', "$course->id")->first();

        //code...
        if ($results == null) {

            Degree::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'fourty' => $request->fourty,
                'sixty1' => $request->sixty1,
                'sixty2' => $request->sixty2,
                'sixty3' => $request->sixty3,
            ]);
            return response('تم الاضافة', 200);
        } else if ($results != null) {
            $deg = Degree::select('*')->where('student_id', "=", "$student->id")->where("course_id", "=", "$course->id")->first();
            $deg->fourty = $request->fourty;
            $deg->sixty1 = $request->sixty1;
            $deg->sixty2 = $request->sixty2;
            $deg->sixty3 = $request->sixty3;
            $deg->save();
            return response('تم التحديث', 200);

        };

    }
}
