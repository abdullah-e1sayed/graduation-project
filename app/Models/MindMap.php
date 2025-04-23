<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Scopes\MindMapScope;
use Illuminate\Database\Eloquent\Builder;

class MindMap extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'slug','title'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::addGlobalScope('mindmap', new MindMapScope());       
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug(Str::uuid()->toString());
            }
        });
    }
    public function scopeFilter(Builder $builder, $filters)
    {
        $options = array_merge([
            'title' => null,
        ], $filters);
        $builder ->when($options['title'],function ($query,$title){
            return $query->where('title','LIKE',"%$title%");
        });  
    }
   
}