<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HelpMessage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\HelpMessageResource;
use App\Models\Admin;

class HelpMessageController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:sanctum')->except('index','show');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
           
        if(Admin::where('email','=',Auth::user()->email)->first()){
            $helpMessages = HelpMessage::Filter($request->query())
                ->with('user:id,name,email','admin:id,name,email')
                ->orderBy('id', 'desc') 
                ->paginate(4); 
            return HelpMessageResource::collection($helpMessages);  
        }
        $helpMessages = HelpMessage::where('answer','!=',null)->Filter($request->query())
                ->with('user:id,name,email','admin:id,name,email')
                ->orderBy('id', 'desc') 
                ->paginate(4); 
        return HelpMessageResource::collection($helpMessages);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request -> validate([
            'message'=>'required|string',
        ]);            
        $message = array_merge([
            'user_id' => $request->user()->id,
            'message' => null,
        ], $request->all());
        HelpMessage::create($message);

        return Response::json("Message sent successfully .",201);
        
                
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request -> validate([
            'message'=>'sometimes|required|string',
            'answer'=>'required|string',
        ]);
        if(Admin::where('email','=',Auth::user()->email)->first()){
            $HelpMessage=HelpMessage::findOrfail($id);
            $answer = array_merge([
                'admin_id' => Auth::user()->id,
                'answer' => null,
            ], $request->all());

            $HelpMessage->update($answer);
            return Response::json("Answer sent successfully .",201);
        }
        return Response::json("Bad Request !",400);
  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Admin::where('email','=',Auth::user()->email)->first()){ 

            $HelpMessage = HelpMessage::findOrFail($id);            
            $HelpMessage->delete();       
            return response([
                'message' => 'Mesaage deleted successfully'
            ]);
        }
        
        return response([
            'message'=>'Not allowed',
        ]);
        
    }
}
