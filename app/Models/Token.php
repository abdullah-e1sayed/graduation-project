<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Token extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'user_id', 'expiration'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid()
    {
        return Carbon::now()->lt($this->expiration);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->expiration) {
                $model->expiration = Carbon::now()->addMinutes(4);
            }
        });
    }
}
