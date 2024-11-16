<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NoteResource;
use App\Models\Admin;

class NoteController extends Controller
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
        $notes = Note::Filter($request->query())->paginate(); 
        return NoteResource::collection($notes);    
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request -> validate([
            'category'=>'sometimes|required|string|max:255',
            'title'=>'required|string|max:255',
            'note'=>'sometimes|required|string',
        ]);            
        $note = array_merge([
            'user_id' => $request->user()->id,
            'category' => null,
            'title' => null,
            'note' => null,
        ], $request->all());
        Note::create($note);

        return Response::json("Note created successfully .",201);
        
                
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note=Note::find($id);
        if(!$note){
            return Response::json("Not Found .",404);
        }
        return new NoteResource($note);       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request -> validate([
            'category'=>'sometimes|required|string|max:255',
            'title'=>'sometimes|required|string|max:255',
            'note'=>'sometimes|required|string',
        ]);
        $note=Note::find($id);
        if(!$note){
            return Response::json("Not Found .",404);
        }
        $note->update($request->all());
        
        return Response::json("Note Edited successfully .",201);
        
  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $note=Note::find($id);
        if(!$note){
            return Response::json("Not Found .",404);
        }
        $note->delete();       
        return response([
            'message' => 'Note deleted successfully'
        ]);
        
    }
}
