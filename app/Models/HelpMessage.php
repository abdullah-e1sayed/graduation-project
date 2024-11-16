<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class HelpMessage extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['id','user_id','message','answer','created_at','updated_at','deleted_at'];

    public function user(){
        return $this->belongsTo(User::class ,'user_id','id');
    }
    public function admin(){
        return $this->belongsTo(User::class ,'admin_id','id');
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        $options = array_merge([
            'message' => null,
        ], $filters);
        // dd($options);
        $builder ->when($options['message'],function ($query,$message){
            return $query->where('message','LIKE',"%$message%");
        });       
       
    }
}
