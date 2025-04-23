<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,            
            'vulnerability'=> $this->vulnerability,
            'severity'=> $this->severity,
            'site'=> $this->site,
            'count'=> $this->count,
            'created at'=> $this->created_at,
            'updated at'=> $this->updated_at,
             
        ];
    }
}
