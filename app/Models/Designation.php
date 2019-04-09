<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;

class Designation extends Model
{
    protected $fillable = [
        'designationname', 'description',
    ];

    /**
     * One to Many RelationShip Product User
    */
    public function skus()
    {
        return $this->hasMany('App\Models\Sku');
    }
}
