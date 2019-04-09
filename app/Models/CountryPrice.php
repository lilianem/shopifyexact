<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;

class CountryPrice extends Model
{
    protected $table = 'country_price';
    /**
     * One to Many RelationShip Table Pivot CountryPrice and Sku
     */
    public function skus()
    {
        return $this->hasMany('App\Models\Sku');
    }

    //Many to Many Relationship Price Country
    public function price()
    {
        return $this->belongsTo('App\Models\Price');            
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');            
    }
}