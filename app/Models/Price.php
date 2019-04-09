<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\CountryPrice;

class Price extends Model
{
    protected $fillable = [
    	'amount',
    ];
    
    // Many to Many Relationship Price Country
    public function countries()
    {
        return $this->belongsToMany('App\Models\Country');            
    }
    
    public function country_prices()
    {
        return $this->hasMany('App\Models\CountryPrice');            
    }
}
