<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules;


class AccessTokensController extends Controller
{
    public function store(Request $request)
    {
        $request ->validate([
            'email' => 'required|max:255',
            'password' => 'required|string|min:6',
            'device_name' => 'string|max:255',
        ]);
        
        $user = $this->authenticate($request);

        if ($user && Hash::check($request->password, $user->password)){
            $device_name = $request->post('device_name',$request->userAgent());
            $token = $user->createToken($device_name);
            return Response::json([
                'token'=> $token->plainTextToken,
                'user' =>$user,
            ],201); 
        }
        return Response::json([
            'message'=> 'Invalid credentials',
        ],401);
        
    }
    
    public function destroy($token = null)
    {
        $user = Auth::guard('sanctum')->user();

        //revoke all tokens 
        // $user->tokens()->delete();

        if($token === null){
            $user->currentAccessToken()->delete();
            return;
        }

        // $PersonalAccessToken = PersonalAccessToken::findToken($token);
        // if($user->id == $PersonalAccessToken->tokenable_id 
        // && get_class($user) == $PersonalAccessToken->tokenable_type){
        //     $PersonalAccessToken->delete();
        // }
        
    }

    public function authenticate($request)
    {
        $username = $request->post( config('fortify.username'));
        $password = $request->post('password');
        if($request->is('*/admin/*')){
            $user = Admin::where('email','=',$username)
            ->orWhere('phone_number','=',$username)                    
            ->first();
            if($user && Hash::check($password, $user->password)){
                return $user;
            }
            return false;
        }
        $user = User::where('email','=',$username)
                    ->orWhere('phone_number','=',$username)                    
                    ->first();
                    if($user && Hash::check($password, $user->password)){
                        return $user;
                    }
                    return false;
        
    }
    public function CreateAccount()//????????????????????????????????????????
    {
        User::create([
            'name'=>'admin',
            'email'=>'admin@gmail.com',
            'password'=>Hash::make('password'),
            'phone_number'=>'01234567890',
        ]);
    }
    public function register(Request $request)
    {        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class,'unique:'.Admin::class],
            'phone_number'=>['unique:'.User::class,'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number'=>$request->phone_number,
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['Message'=>'Account created successfully']);        
    }

    public function createAdmin(Request $request)
    {    
        if($request->is('*/admin/*')){
            
            if(Admin::where('email','=',Auth::user()->email)->first()){
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class,'unique:'.Admin::class],
                    'phone_number'=>['unique:'.User::class,'unique:'.Admin::class],
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ]);

                $user = Admin::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number'=>$request->phone_number,
                    'password' => Hash::make($request->password),
                ]);
                return response()->json(['Message'=>'Admin created successfully']);
            }
            return response()->json(["Message"=>"you don't have permesion"]);
        }        
    }

}
