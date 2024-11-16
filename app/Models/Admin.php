<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;



class Admin extends Authenticatable
{
    use HasFactory , Notifiable , HasApiTokens;
    protected $fillable = [
        'name','email','password','phone_number',
    ];
    protected $hidden = [
        'password',        
        'created_at',
        'updated_at',
    ];
    public function help_messages()
    {
        return $this->hasMany(HelpMessage::Class,'admin_id');
    }
    public function profile(){
        return $this->hasOne(Profile::Class,'user_id')->withDefault();
    }
}
