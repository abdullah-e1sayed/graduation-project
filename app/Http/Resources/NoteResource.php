<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
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
            'category'=> $this->category	,  
            'title'=> $this->title,  
            'note'=> $this->note,  
            'user'=>[
                    'name'=>$this->user->name,
                    'email'=>$this->user->email,
            ] , 
             
        ];
    }
}
