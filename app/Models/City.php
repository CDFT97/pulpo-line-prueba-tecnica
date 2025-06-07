<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

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
