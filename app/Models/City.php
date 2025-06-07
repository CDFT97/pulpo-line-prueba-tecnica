<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'region', 'country', 'timezone'];

    public function userSearches()
    {
        return $this->hasMany(UserSearch::class);
    }

    public function userFavorites()
    {
        return $this->hasMany(UserFavorite::class);
    }
}
