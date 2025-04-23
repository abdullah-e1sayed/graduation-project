<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Profile;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function users(Request $request)
    {
        $user=Auth::user();
        if(Admin::where('email','=',$user->email)->first()){
            $users = User::Filter($request->query())
            ->orderBy('id', 'desc') 
            ->paginate();
            return ProfileResource::collection($users);
        }
    }
    
    public function Profile ()
    {
        $user=Auth::user();        
        return new ProfileResource($user); 
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request -> validate([
            'phone_number'=>'nullable|string|max:255',
            'first_name'=>'nullable|string|max:255',
            'name'=>'nullable|string|max:255',
            'last_name'=>'nullable|string|max:255',
            'birthday'=>'nullable|date_format:Y-m-d',
            'gender'=>'nullable|in:male,female',
            'country'=>'nullable|string|size:2',
            'locale'=>'nullable|string|size:2',            
        ]);
        $user=Auth::user();     
        if(Admin::where('email','=',$user->email)->first()){
            $user->update($request->all());
            return Response::json("Admin Profile Edited successfully .",201);
        }   
        $profile=Profile::where('user_id',$user->id)->first();
        if(!$user){
            return Response::json("Not Found .",404);
        }
        if($request->phone_number){
            $user->update([
                'phone_number' => $request->phone_number
            ]);
        }
        $profile->update($request->all());
        
        return Response::json("Profile Edited successfully .",201);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request)
    {
    
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        $user = $request->user();
        $user->delete();
        $user->currentAccessToken()->delete();
        return Response::json("Acount Deleted successfully .",201);

    }
    public function changePassword(Request $request)
    {
        $request -> validate([
            'current_password' => ['required', 'string', 'current_password:sanctum'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        request()->user()->update([
            'password' => Hash::make($request->password),
        ]);
        request()->user()->tokens()->delete();
        return Response::json("Password Changed successfully .",201);
        

    }
}
