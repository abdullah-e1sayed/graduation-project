<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HelpMessageResource extends JsonResource
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
            'message'=> $this->message,  
            'answer'=> $this->answer,  
            'user'=>[
                    'name'=>$this->user->name,
                    'email'=>$this->user->email,
            ] , 
            'admin'=>[
                'name'=>$this->admin->name??'not replayed yet',
                // 'email'=>$this->admin->email,
            ] , 
        ];
    }
}
