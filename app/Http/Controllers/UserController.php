<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
class UserController extends Controller
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
    public function get(){
        $users = User::select('*')->get();
        return $users;
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response('', 401);
        }
        $user->tokens()->delete();
        $token = $user->createToken($request->input('email'))->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    public function create(Request $request)
    {
       
      $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'password' => 'required',
            'role' => 'required'
        ]);
        $users = User::where('email', '=', $request->input('email'))->first();
        if ($users !== null) {
            return response('البريد الالكتروني مأخوذ سابقا', 409);
        }

        if (!Gate::allows('is-super')) {
            
            return response('انت غير مخول لانشاء ادمن جديد', 403);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role'=>$request->role,
        ]);
       
        $token = $user->createToken($request->input('email'))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    public function update(Request $request)
    {
        if (!Gate::allows('is-super')) {
            
            return response('انت غير مخول', 403);
        }
        $id = $request->id; 
        User::whereId($id)->update($request);
        return response('تم التحديث', 200);

    }

    public function destroy($id)
    {
        if (!Gate::allows('is-super')) {
            return response('انت غير مخول', 403);
        }
        User::destroy($id);
        return response('تم حذف المستخدم بنجاح', 200);
    }

    
   
}
