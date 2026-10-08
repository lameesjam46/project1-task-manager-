<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function store(StoreProfileRequest $request){
        $user_id=Auth::user()->id;
        $validateData=$request->validated();
        $validateData['user_id']=$user_id;
        if ($request->hasFile('Image')){
            $path=$request->file('Image')->store('my photo','public');
            $validateData['Image']=$path;
        }
        $profile=Profile::query()->create($validateData);
        return response()->json([
            'msg'=>'profile created successfully',
            'data'=>$profile,
            'status'=>201
        ]);
    }

    public function show()
    {

     
         // $ProfileUser=Auth::user()->profile;
         // return response()->json($ProfileUser, 200);
        $user = Auth::user();
        $profile = $user->profile;
        $profile->name = $user->name;
        $profile->email = $user->email;
        
        return response()->json($profile, 200);
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;
            
            // Update profile
            $profile->update($request->validated());
            
            // Update user information if provided
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            $user->save();
            
            // Add user information to the profile response
            $profile->name = $user->name;
            $profile->email = $user->email;
            
            return response()->json($profile, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
