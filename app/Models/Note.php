<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\NoteScope;
use Illuminate\Database\Eloquent\Builder;


class Note extends Model
{
    use HasFactory,SoftDeletes;
   
    protected $fillable = ['id','user_id','category','title','note','created_at','updated_at','deleted_at'];
    public function user(){
        return $this->belongsTo(User::class ,'user_id','id');
    }

    protected static function booted()
    {
        static::addGlobalScope('note', new NoteScope());       
    }
    public function scopeFilter(Builder $builder, $filters)
    {
        $options = array_merge([
            'category' => null,
            'title' => null,
        ], $filters);
        $builder ->when($options['category'],function ($query,$category){
            return $query->where('category','LIKE',"%$category%");
        });
        $builder ->when($options['title'],function ($query,$title){
            return $query->where('title','LIKE',"%$title%");
        });  
    }
}
