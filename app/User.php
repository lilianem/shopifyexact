<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Product;
use App\Models\Company;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'company_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**    
    * Get the stores for the user
    * * Many to Many RelationShip Store User
    */
    public function stores()
    {
        return $this->belongsToMany('App\Models\Store', 'store_users');
    }

    /**
     * Get the providers for the user
     * * One to Many RelationShip UserProvider User
     */
    public function providers()
    {
        return $this->hasMany('App\Models\UserProvider');
    }

    /**
     * One to Many RelationShip Product User
    */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    /**
    * * One to Many RelationShip Company User
    */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    const ADMIN_EMAIL = 'radikalshopifyexact@gmail.com';

    public function isAdmin()
    {        
        return $this->email === self::ADMIN_EMAIL;    
    }
}
