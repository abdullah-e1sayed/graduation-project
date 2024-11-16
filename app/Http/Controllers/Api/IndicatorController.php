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
        $indicators = Indicator::Filter($request->query())->paginate(); 
        return IndicatorResource::collection($indicators);  
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $indicator = array_merge([
            'user_id' => $request->user()->id,
            'vulnerabilities' => $request->vulnerabilities[0],
        ], [$request->vulnerabilities[0]]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $indicator=Indicator::find($id);
        if(!$indicator){
            return Response::json("Not Found .",404);
        }
        $indicator->delete();       
        return response([
            'message' => 'Indicator deleted successfully'
        ]);
    }
}
