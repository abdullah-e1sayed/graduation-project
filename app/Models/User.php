<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable , TwoFactorAuthenticatable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'created_at',
        'updated_at',
        'email_verified_at',
    ];
    public function scopeFilter(Builder $builder, $filters)
    {
        $options = array_merge([
            'name' => null,
            'email' => null,
            'phone_number' => null,
        ], $filters);
        $builder ->when($options['name'],function ($query,$name){
            return $query->where('name','LIKE',"%$name%");
        });
        $builder ->when($options['email'],function ($query,$email){
            return $query->where('email','LIKE',"%$email%");
        });
        $builder ->when($options['phone_number'],function ($query,$phone_number){
            return $query->where('phone_number','LIKE',"%$phone_number%");
        });  
    
    }
    protected static function booted()
    {
        static::created(function(User $user){
            Profile::create([
                'user_id'=>$user->id,
                'first_name'=>$user->name,
                'api_token'=>Str::uuid(), 
            ]);
        });     
    }
    public function notes()
    {
        return $this->hasMany(Note::Class,'user_id');
    }

    public function indicators()
    {
        return $this->hasMany(Indicator::Class,'user_id');
    }
    public function vulnerabilities()
    {
        return $this->hasMany(Vulnerability::Class,'user_id');
    }

    public function help_messages()
    {
        return $this->hasMany(HelpMessage::Class,'user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ]; 
    }
    public function profile(){
        return $this->hasOne(Profile::Class,'user_id')->withDefault();
    }
}
