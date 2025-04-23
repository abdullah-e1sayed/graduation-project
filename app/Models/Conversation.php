<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'slug'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get all of the messages for the Conversation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
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
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'user_id'];
}