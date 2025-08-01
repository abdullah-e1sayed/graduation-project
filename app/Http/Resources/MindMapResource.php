<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MindMapResource extends JsonResource
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
            'title'=> $this->title,  
            'slug'=> $this->slug,  
            // 'user'=>[
            //         'name'=>$this->user->name,
            //         'email'=>$this->user->email,
            // ] , 
             
        ];
    }
}
