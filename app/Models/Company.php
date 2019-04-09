<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\Sku;
use App\User;

class Company extends Model
{
    protected $fillable = [
        'companyName',
    ];

    /**
    * * One to Many RelationShip Company Store
    */
    public function stores()
    {
        return $this->hasMany('App\Models\Store');
    }

    /**
    * * One to Many RelationShip Company User
    */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
    * * One to Many RelationShip Company Sku
    */
    public function skus()
    {
        return $this->hasMany('App\Models\Sku');
    }
}
