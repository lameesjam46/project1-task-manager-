<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>'required|string|max:225|',
            'email'=>'required|string|email|max:225|unique:users,email',
            'password'=>'required|string|min:8|confirmed',
            'role' => 'sometimes|in:Employee,Manager'
        ]);
        $user=User::query()->create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role?? 'Employee'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        Mail::to($user->email)->send(new WelcomeMail($user));

        return response()->json([
            'message'=>'User Registered Successfully',
            'data'=>$user,
            'token'=>$token
        ], 201);
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string'
        ]);

        if(!Auth::attempt($request->only('email','password')))
            return response()->json(['massage'=>'Invalid email or password'],401);

        $user = User::query()->where('email', $request->email)->firstOrFail();

        if ($user->role === 'Employee') {
            $isProjectManager = DB::table('employee_company')
                ->where('user_id', $user->id)
                ->where('is_project_manager', true)
                ->exists();


            if ($isProjectManager) {
                $user->role = 'project_manager';
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'massage'=>'Login Successfully',
            'data'=>$user,
            'token'=>$token
        ], 201);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['massage'=>'Logout Successfully'],200);

    }

















    public function getProfile(){
        $profile=Auth::user()->profile;
//        $profile=User::find($id)->profile;
        return response()->json($profile,200);
    }
    public function getUserTasks(){
        $tasks=Auth::user()->tasks;
//        $tasks=User::findOrFail($id)->tasks;
        return response()->json($tasks,200);
    }

    public function getUserWithProfile(){
        $user_id= Auth::user()->id;
        $userData=User::query()->with('profile')->findOrFail($user_id);
        return new UserResource($userData);

    }
    public function getAllUser(){
        $userData=User::query()->with('profile')->get();
        return  UserResource::collection($userData);

    }

}
