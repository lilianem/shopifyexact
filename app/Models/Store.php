<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Country;
use App\Models\StoreProvider;
use App\Models\Company;

class Store extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'domain', 'company_id'
    ];

    /**
    * Get the providers for the user
    * * One to Many RelationShip StoreProvider Store
    */
    public function providers()
    {
        return $this->hasMany('App\Models\StoreProvider');
    }

    /**
    * Get all of the users that belong to the store.
    * * Many to Many RelationShip Store User
    */
    public function users()
    {
        return $this->belongsToMany(
            'App\User', 'store_users', 'store_id', 'user_id'
        );
    }

    /**
     * One to Many RelationShip Product Store
    */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    /**
     * One to Many RelationShip Store Company
    */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

}
