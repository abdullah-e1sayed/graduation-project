<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Token;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * Handle API requests.
     */
    public function sendApi(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $token = $data['token'] ?? null;
            $timestamp = $data['timestamp'] ?? null;
            $receivedSignature = $data['signature'] ?? null;

            if (!$token || !$timestamp || !$receivedSignature) {
                return response()->json(['success' => false, 'error' => 'Missing parameters'], 400);
            }

            $secretKey = config('app.secret_key'); 
            $expectedSignature = hash_hmac('sha256', $token . $timestamp, $secretKey);

            if (!hash_equals($expectedSignature, $receivedSignature)) {
                return response()->json(['success' => false, 'error' => 'Invalid signature'], 400);
            }

            $tokenModel = Token::where('token', $token)->first();

            if (!$tokenModel) {
                return response()->json(['success' => false, 'error' => 'Token not found'], 404);
            }

            $user = $tokenModel->user;
            $expiration = Carbon::parse($tokenModel->expiration)->timestamp;
            $currentTime = Carbon::now()->timestamp;

            if ($currentTime > $expiration) {
                return response()->json(['success' => false, 'error' => 'Token expired'], 401);
            }

            $tokenModel->delete();

            return response()->json([
                'success' => true,
                'api' => $user->profile->api_token,
                'name' => $user->profile->first_name,
            ]);
        }

        return response()->json(['success' => false, 'error' => 'Invalid request method'], 405);
    }
}