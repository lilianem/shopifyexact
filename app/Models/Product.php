<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;
use App\User;
use App\Models\Store;

class Product extends Model
{
    protected $fillable = [
        'provproductid', 'quantity',
    ];

    /**
     * One to Many RelationShip Product Sku
    */
    public function sku()
    {
        return $this->belongsTo('App\Models\Sku');
    }

    /**
     * One to Many RelationShip Product User
    */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * One to Many RelationShip Product Store
    */
    public function store()
    {
        return $this->belongsTo('App\Models\Store');
    }
}
