<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $primaryKey='user_id';
    Protected $fillable=[
        'user_id','first_name','last_name','birthday','gender','country','locale','api_token'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
