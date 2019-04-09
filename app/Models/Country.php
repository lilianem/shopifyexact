<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\Price;
use App\Models\CountryPrice;

class Country extends Model
{
    protected $fillable = [
        'countryname',
    ];

    // Many to Many Relationship Price Country
    public function prices()
    {
        return $this->belongsToMany('App\Models\Price');            
    }
    
    public function country_prices()
    {
        return $this->hasMany('App\Models\CountryPrice');            
    }  
}