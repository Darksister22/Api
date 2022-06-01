<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DegreeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\SemesterController;
use App\Models\Degree;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Attendency done for now
Route::get('/students', [StudentController::class, 'showAll']);
Route::post('/students/grad', [StudentController::class, 'grad']);
Route::post('/students/attend',[StudentController::class,'attendency']);
Route::post('/students/create', [StudentController::class, 'create']);
Route::post('/students/destroy/{id}', [StudentController::class, 'destroy']);
Route::post('/students/update',[StudentController::class,'update']);

//ALL DONE THANK GOD
Route::get('/instructors', [InstructorController::class, 'showAll']);
Route::post('/instructors/create', [InstructorController::class, 'create']);
Route::post('/instructors/destroy/{id}', [InstructorController::class, 'destroy']);
Route::post('/instructors/update', [InstructorController::class, 'update']);

//COURSES: UPDATE
Route::post('/courses/create', [CourseController::class, 'create']);
Route::get('/courses/level', [CourseController::class, 'showLevel']);
Route::get('/courses', [CourseController::class, 'showAll']);
Route::post('/courses/update', [CourseController::class, 'update']);
Route::post('/courses/destroy/{id}', [CourseController::class, 'destroy']);

//DEGREES ARE A NIGHTMARE FOR ANOTHER DAY
Route::post('/degrees/createhelp', [DegreeController::class, 'createhelp']);
Route::get('/degrees', [DegreeController::class, 'getStudentDegrees']);
Route::get('/degrees/create', [DegreeController::class, 'createStudentDegrees']);

//SEMESTERS ALL DONE
Route::post('/semesters/end', [SemesterController::class, 'end']);
Route::post('/semesters/create', [SemesterController::class, 'create']);

//USERS ALL DONE
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/create', [UserController::class, 'create']);
Route::get('users/get', [UserController::class, 'get']);
Route::post('/users/update', [UserController::class, 'update']);
Route::post('/users/destroy/{id}', [UserController::class, 'destroy']);

Route::get('/homepage', [HomeController::class, 'counts']);