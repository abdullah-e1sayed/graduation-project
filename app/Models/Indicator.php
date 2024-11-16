<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\IndicatorScope;
use Illuminate\Database\Eloquent\Builder;


class Indicator extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['id','user_id','vulnerabilities','created_at','updated_at','deleted_at'];
    protected static function booted()
    {
        static::addGlobalScope('indicator', new IndicatorScope());
               
    }
    public function scopeFilter(Builder $builder, $filters)
    {
        $options = array_merge([
            'site' => null,
        ], $filters);
        $builder ->when($options['site'],function ($query,$site){
            return $query->where('site','LIKE',"%$site%");
        });        
    }
    
    public function user(){
        return $this->belongsTo(User::class ,'user_id','id');
    }

}
