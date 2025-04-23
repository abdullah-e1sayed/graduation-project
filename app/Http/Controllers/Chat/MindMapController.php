<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MindMap;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MindMapController extends Controller
{

    public function saveMindMap(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'api_key' => 'required|string',
                'timestamp' => 'required|integer',
                'signature' => 'required|string',
                'text' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => 'Invalid data'], 400);
            }

            $apiKey = $request->input('api_key');
            $timestamp = $request->input('timestamp');
            $signature = $request->input('signature');
            $text = $request->input('text');
            
            //take secound row from text .

            $title = explode("\n", $text)[2];
            $secretKey = config('app.secret_key');
            $expectedSignature = hash_hmac('sha256', $apiKey . $timestamp, $secretKey);

            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json(['success' => false, 'error' => 'Invalid signature'], 401);
            }

            $profile = Profile::where('api_token', $apiKey)->first();
            if (!$profile) {
                return response()->json(['success' => false, 'error' => 'Invalid API key'], 404);
            }

            $user = $profile->user;

            $mindMap = MindMap::create([
                'title'=> $title,
                'user_id' => $user->id
            ]);
            $mindMapName = $mindMap->slug;

            $userFolder = 'public/user_' . $user->id;
            $mdFilename = "$mindMapName.md";
            $mdPath = storage_path("app/$userFolder/$mdFilename");
            $htmlFilename = "$mindMapName";
            $htmlPath = storage_path("app/$userFolder/$htmlFilename.html");
            Storage::makeDirectory($userFolder);

            file_put_contents($mdPath, $text);
            shell_exec("markmap $mdPath -o $htmlPath  --no-open");

            if (file_exists($mdPath)) {
                unlink($mdPath);
            }

            $mindMap->slug = "$htmlFilename";
            $mindMap->save();

            $url = route('viewMindMap', ['slug' => $mindMap->slug]);
            return response()->json(['success' => true, 'file_url' => $url], 201);
        }

        return response()->json(['success' => false, 'error' => 'Invalid request method'], 405);
    }

    public function viewMindMap($slug)
    {
        // dd(Auth::id());
        $mindMap = MindMap::where('slug', $slug)
                        //   ->where('user_id', Auth::id())
                          ->first();
        // dd($mindMap);
        if (!$mindMap) {
            abort(404, 'Mind Map not found');
        }
        $fileContent =file_get_contents(storage_path('app/public/user_'.$mindMap->user->id.'/'.$mindMap->slug.'.html'));            

        // $htmlFilePath = asset('storage/user_5/' . $mindMap->slug.'.html');

        return view('mindmap',['mindMapContent' => $fileContent]);
    }
}