<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;

class Totsku extends Model
{
    protected $fillable = [
        'numbertotskus', 'quantitysku', 'company_id',
    ];

    /**
     * One to Many RelationShip Sku Totsku
    */
    public function skus()
    {
        return $this->hasMany('App\Models\Sku');
    }
}
