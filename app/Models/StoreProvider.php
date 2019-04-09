<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProvider extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id', 'provider', 'provider_store_id', 'provider_token', 'webhookShopify_id'
    ];
}
