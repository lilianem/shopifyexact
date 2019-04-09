<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Designation;
use App\Models\CountryPrice;
use App\Models\Totsku;
use App\Models\Tag;
use App\Models\Company;

class Sku extends Model
{
    protected $fillable = [
        'number', 'company_id',
    ];

    /**
     * One to Many RelationShip Product Sku
    */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    /**
     * One to Many RelationShip Sku Designation
    */
    public function designation()
    {
        return $this->belongsTo('App\Models\Designation');
    }

    /**
     * One to Many RelationShip Table Pivot Country_Price and Sku
     */
    public function country_price()
    {
        return $this->belongsTo('App\Models\CountryPrice');
    }

    /**
     * One to Many RelationShip Sku Totsku
    */
    public function totsku()
    {
        return $this->belongsTo('App\Models\Totsku');
    }

    /**
     * One to Many RelationShip Sku Tag
    */
    public function tag()
    {
        return $this->belongsTo('App\Models\Tag');
    }

    /**
     * One to Many RelationShip Sku Company
    */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
    
}
