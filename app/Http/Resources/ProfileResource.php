<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Admin;

class ProfileResource extends JsonResource
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
            'fname'=> $this->profile->first_name	,  
            'lname'=> $this->profile->last_name	,  
            'email'=> $this->email,  
            'phone'=> $this->phone_number,  
            'birth day'=>$this->profile->birthday,
            'gender'=>$this->profile->gender,
            'country'=>$this->profile->country,
            'locale'=>$this->profile->locale, 
            'is admin'=>(Admin::where('email','=',$this->email)->first())?'true':'false',            
        ];
    }
}
