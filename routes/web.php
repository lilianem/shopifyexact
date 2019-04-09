<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::post('/webhook/productsupdate', 'ShopifyController@webhookProductUpdate'); // For shopify
Route::post('/webhook/orderscreate', 'ShopifyController@webhookOrdersCreate'); // For shopify
Route::post('/webhook/customerscreate', 'ShopifyController@webhookCustomersCreate'); // For shopify

Route::get('/webhook/webhookExactSubscription', 'ShopifyController@webhookExactSubscription'); // For exact
Route::post('/exact/exactwebhook', 'ShopifyController@exactWebhookCallback'); // For exact

Route::get('shop/create/webhooksshopify', 'ShopifyController@createWebhooksShopify')->name('shop.createWebhooksShopify'); // For shopify
Route::post('shop/registerWebhooksShopify', 'ShopifyController@registerWebhooksShopify')->name('shop.registerwebhooksshopify'); // For shopify
Route::get('shop/listwebhooksregisteredshopify/store/{id}', 'ShopifyController@listWebhooksRegisteredShopifyPerStore')->name('shop.listwebhooksregisteredshopifyperstore'); // For shopify
Route::get('shop/delete/webhooksshopify', 'ShopifyController@deleteWebhooksShopifyForm')->name('shop.deleteWebhooksShopifyForm'); // For shopify
Route::post('shop/deleteWebhooksShopify', 'ShopifyController@deleteWebhooksShopify')->name('shop.deleteWebhooksShopify'); // For shopify

Route::middleware('auth')->group(function () {
	Route::get('/login/shopify/ind', 'Auth\LoginShopifyController@loginShopify');
	Route::get('/login/shopify', 'Auth\LoginShopifyController@redirectToProvider')->name('login.shopify');
	Route::get('/login/shopify/callback', 'Auth\LoginShopifyController@handleProviderCallback');
	
	Route::get('/login/exact/ind', 'Auth\LoginExactController@loginExact');
	Route::post('/login/exact/connection', 'Auth\LoginExactController@appConnection')->name('exact.authorize');
	Route::get('/login/exact/callback', 'Auth\LoginExactController@appCallback');
	Route::get('/login/exact/skusList', 'Auth\LoginExactController@skusList')->name('exact.skusList');
	
	Route::get('exact/edit/sku/{id}', 'Auth\LoginExactController@editExactSku')->name('exact.editExactSku');
	Route::put('exact/edit/sku/{id}', 'Auth\LoginExactController@updateExactSku')->name('exact.updateExactSku');

	Route::get('exact/sync/sku', 'Auth\LoginExactController@syncExactShopifySkus')->name('exact.syncExactShopSkus');
		
	Route::get('/storelist', 'ShopifyController@storelist')->name('storelist');
	Route::get('/shop/showProducts/sku/{id}', 'ShopifyController@listProducts');
	Route::get('/shop/show/sku/{id}', 'ShopifyController@showSku');
	Route::get('/skus', 'ShopifyController@listSkus');
	Route::get('/orders', 'ShopifyController@listOrders');
	Route::get('/shop/show/order/{storeId}/{id}', 'ShopifyController@showOrder');
	Route::get('/orders/sync', 'ShopifyController@syncOrders');
	Route::get('/customers/sync', 'ShopifyController@syncCustomers');
	Route::get('shop/syncshopexact/skus/', 'ShopifyController@syncShopExactSkus');

	Route::get('/customers', 'ShopifyController@listCustomers');
	Route::get('/shop/show/customer/{customer}/{id}', 'ShopifyController@showCustomer');
		
	Route::get('shop/edit/sku/{id}', 'ShopifyController@editSku')->name('shop.editSku');
	Route::put('shop/edit/sku/{id}', 'ShopifyController@updateSku')->name('shop.updateSku');
	Route::get('shop/create/sku', 'ShopifyController@createSku')->name('shop.createSku');
	Route::post('shop/store/sku', 'ShopifyController@storeSku')->name('shop.storeSku');
	Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
});

Route::get('/home', 'HomeController@index')->name('home');