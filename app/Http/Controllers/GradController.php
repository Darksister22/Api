<?php

namespace App\Http\Controllers;

use App\Exports\GradExport;
use App\Models\Graduate;
use Illuminate\Http\Request;

class GradController extends Controller
{
    public function showall(Request $request){
        $query = Graduate::select('*');
        if ($request->has('search')) {
            $query->where('name_ar', '=', '%' . $request->input('search') . '%');
        }
        $data = $query->paginate(10);
        return $data;
    }
    public function update(Request $request){
        $request->validate([
            'id' => 'required',
            ]);
            $students = Graduate::select('*')->where('id','=',"$request->id")->first();
            $students->note = $request->note;
            $students->summer_deg = $request->summer_deg;

            $students-> save();
    }

    public function exportgrad(Request $request)
    {
        return (new GradExport($request))->download("graduate.xlsx");
    }
}
