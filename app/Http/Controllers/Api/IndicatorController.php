<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Indicator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\IndicatorResource;

class IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $indicators = Indicator::Filter($request->query())
        ->orderBy('id', 'desc') 
        ->paginate(5); 
        return IndicatorResource::collection($indicators);  
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request ->validate([
            'vulnerability' => 'string|required|max:255',
            'severity'=> 'in:critical,high,medium,low',
            'site'=>'string|required|max:255',
            'count'=> 'required|numeric|min:0',
        ]);

        $indicator = array_merge([
            'user_id' => $request->user()->id,
        ], $request->all());
        Indicator::create($indicator);

        return Response::json(["message"=>"Indicator added successfully ."],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $indicator=Indicator::find($id);
        if(!$indicator){
            return Response::json(["message"=>"Not Found ."],404);
        }
        return new IndicatorResource($indicator);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $indicator=Indicator::findOr($id);
        if(!$indicator){
            return Response::json(["message"=>"Not Found ."],404);
        }
        $request ->validate([
            'vulnerability' => 'string|sometimes|max:255',
            'severity'=> 'in:critical,high,medium,low',
            'site'=>'string|sometimes|max:255',
            'count'=> 'sometimes|numeric|min:0',
        ]);

        $indicator->update($request->all());

        return Response::json(["message"=>"Indicator updated successfully ."],201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $indicator=Indicator::find($id);
        if(!$indicator){
            return Response::json(["message"=>"Not Found ."],404);
        }
        $indicator->delete();       
        return Response::json(["message"=>"Indicator deleted successfully . "],404);

    }
}
