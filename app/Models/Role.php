<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    CONST ROLE_PREMIUM = ['name' => 'premium', 'id' => 2];
    CONST ROLE_FREE = ['name' => 'free', 'id' => 1];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
