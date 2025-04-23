<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = Conversation::where('user_id', auth()->id())
        ->with(['messages' => function ($query) {
            $query->orderBy('created_at', 'asc')->limit(1); // Get the first message
        }])
        ->get();
        return response()->json([
            'success' => true,
            'chats' => $chats,
        ], 200);
    }
    /**
     * Save chat conversation.
     */
    public function saveChat(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'api_key' => 'required|string',
                'conversation' => 'required|array',
                'timestamp' => 'required|integer',
                'signature' => 'required|string',
            ]);

            // geting the data from the request
            $apiKey = $request->input('api_key');
            $conversationData = $request->input('conversation');
            $timestamp = $request->input('timestamp');
            $signature = $request->input('signature');

            // create the expected signature
            $secretKey = config('app.secret_key'); 
            $expectedSignature = hash_hmac('sha256', $apiKey . $timestamp, $secretKey);

            // check if the signature is valid
            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json(['success' => false, 'error' => 'Invalid signature'], 401);
            }

            // check if the api key is valid
            $profile = Profile::where('api_token', $apiKey)->first();

            if (!$profile) {
                return response()->json(['success' => false, 'error' => 'Invalid API key'], 404);
            }

            $user = $profile->user;

            // start a transaction to ensure atomicity
            DB::beginTransaction();

            try {
                // create the conversation
                $conversation = Conversation::create([
                    'user_id' => $user->id,
                    'slug' => Str::slug(Str::uuid()->toString()),
                ]);

                // create the messages
                foreach ($conversationData as $message) {
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'role' => $message['role'] ?? null,
                        'content' => $message['content'] ?? null,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Chat saved successfully!',
                    'slug' => $conversation->slug,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving chat: ' . $e->getMessage());
                return response()->json(['success' => false, 'error' => 'Failed to save chat'], 500);
            }
        }

        return response()->json(['success' => false, 'error' => 'Invalid request method'], 400);
    }
    public function show(Request $request){
        $chat = Conversation::where('slug', '=', $request->slug)->where('user_id', '=', auth()->user()->id)->with('messages')->first(); 
        
        return response()->json([
            'success' => true,
            'chat' => $chat,
        ], 200);
    
    }
    public function deleteChat(Request $request){
        $chat = Conversation::where('slug', '=', $request->slug)->where('user_id', '=', auth()->user()->id)->first();
        $chat->delete();
        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully!',
        ], 200);
    }

}