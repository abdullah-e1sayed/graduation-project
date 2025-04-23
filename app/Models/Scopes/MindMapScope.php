<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
class MindMapScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user=Auth::user();

        if(Admin::where('email','=',$user?->email)->first()){

        }elseif($user){
            $builder->where('user_id','=',$user->id); 
        }
    }
}
