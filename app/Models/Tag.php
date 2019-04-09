<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sku;

class Tag extends Model
{
    protected $fillable = [
        'tagname',
    ];

    /**
     * One to Many RelationShip Sku Tag
    */
    public function skus()
    {
        return $this->hasMany('App\Models\Sku');
    }
}
