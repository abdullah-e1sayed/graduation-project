<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Models\Token;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function streamlitView(Request $request)
    {
        $user=Auth::user(); 

        $token = Str::uuid()->toString();
        $expiration = Carbon::now()->addMinutes(1);

        Token::create([
            'token' => $token,
            'user_id' => $request->user()->id,
            'expiration' => $expiration,
        ]);
        return response()->json(['token' => $token, 'expiration' => $expiration->toDateTimeString()]);

        // return view('chat',compact('token'));
    }
}