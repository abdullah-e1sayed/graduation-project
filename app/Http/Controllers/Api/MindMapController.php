<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MindMap;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MindMapResource;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MindMapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mindMap = MindMap::Filter($request->query())
        ->orderBy('id', 'desc') 
        ->paginate(5); 
        return MindMapResource::collection($mindMap); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            $title = trim($title,'#');
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

    /**
     * Display the specified resource.
     */
    public function show( $slug)
    {
        $mindMap = MindMap::where('slug', $slug)->first();
        if (!$mindMap) {
            abort(404, 'Mind Map not found');
        }
        $fileContent =file_get_contents(storage_path('app/public/user_'.$mindMap->user->id.'/'.$mindMap->slug.'.html'));            
        return view('mindmap',['mindMapContent' => $fileContent]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $request -> validate([
            'title'=>'sometimes|required|string|max:255',
        ]);
        $mindMap = MindMap::where('slug',$slug)->first();
        if(!$mindMap){
            return Response::json("Not Found .",404);
        }
        $mindMap->update([
            'title'=>$request->title,
        ]);
        
        return Response::json("Mind Map Edited successfully .",201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( string $slug)
    {
        $mindMap = MindMap::where('slug',$slug)->first();
        if(!$mindMap){
            return Response::json("Not Found .",404);
        }
        $mindMap->delete();
        return Response::json("Mind Map deleted successfully. ",201);
              
    
    }
}
