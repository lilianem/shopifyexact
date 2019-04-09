<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Socialite;
use App\Models\Store;
use App\Models\Product;
use App\Models\Sku;
use App\Models\Totsku;
use App\Models\Tag;
use App\Models\Country;
use App\Models\CountryPrice;
use App\Models\Price;
use App\Models\Designation;
use App\Models\UserProvider;
use App\Models\StoreProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Oseintow\Shopify\Facades\Shopify;

class LoginShopifyController extends Controller
{
    public function loginShopify()
    {
        return view('auth.loginShopify');
    }

    /**
    * Redirect the user to the GitHub authentication page.
    *
    * @return \Illuminate\Http\Response
    */
    public function redirectToProvider(Request $request)
    {              
        $this->validate($request, [
            'domain' => 'string|required',
        ]);        
        $config = new \SocialiteProviders\Manager\Config(
            env('SHOPIFY_KEY'),
            env('SHOPIFY_SECRET'),
            env('SHOPIFY_REDIRECT'),            
            ['subdomain' => $request->get('domain')]
        );        
        return Socialite::with('shopify')
            ->setConfig($config)
            ->scopes(['read_orders','write_products','write_inventory', 'read_customers'])            
            ->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $shopifyUser = Socialite::driver('shopify')->user();
        // Create store
        $store = Store::firstOrCreate([                   
            'name' => $shopifyUser->name,
            'domain' => $shopifyUser->nickname,
            'company_id' => auth()->user()->company_id,            
        ]);
        // User the OAuth Identity
        UserProvider::firstOrCreate([
            'user_id' => auth()->user()->id,
            'provider' => 'shopify',
            'provider_user_id' => $shopifyUser->id,
            'provider_token' => $shopifyUser->token,
        ]);
        // Store the OAuth Identity
        StoreProvider::firstOrCreate([
            'store_id' => $store->id,
            'provider' => 'shopify',
            'provider_store_id' => $shopifyUser->id,
            'provider_token' => $shopifyUser->token,
        ]);      
        // Attach store to user        
        $store->users()->syncWithoutDetaching([auth()->user()->id]);
        $store->company()->associate(auth()->user()->company_id);
        $store->save();        

        // GET /admin/shop.json
        $shopA = $store->domain;        
        $shopUrl = $shopA;
        $accessToken = $shopifyUser->token;
        $shop = Shopify::setShopUrl($shopUrl)->setAccessToken($accessToken)->get("/admin/shop.json");
        $store->country = $shop['country_name'];
        $store->save();
        // Create Country table
        $country = Country::firstOrCreate([
            'countryname' => $shop['country_name'],
        ]);
        // Create Product table
        $product2ss = Shopify::setShopUrl($shopUrl)->setAccessToken($accessToken)->get("/admin/products.json", ["page"=>1]);
        foreach($product2ss as $product2)
        {
            $product = Product::firstOrCreate([
                'provproductid' => $product2->id,
            ]);                                
            $product->user_id = auth()->user()->id;
            $product->store_id = $store->id;
            $product->save();  
            foreach($product2->variants as $variant)
            {
                $product->quantity = $variant->inventory_quantity;
                $price = Price::firstOrCreate([
                    'amount' => $variant->price,
                ]);                
                $price->countries()->sync($country->id, false);                           
                $designation = Designation::firstOrCreate([
                    'designationname' => $product2->title,
                    'description' => $product2->body_html,                                    
                ]);                                
                $countryprice = CountryPrice::where(['price_id' => $price->id, 'country_id' => $country->id])->first();
                $sku = Sku::firstOrCreate([
                    'number' => $variant->sku,
                    'designation_id' => $designation->id,
                    'country_price_id' => $countryprice->id,
                    'company_id' => auth()->user()->company_id,                                      
                ]);
                $totsku = Totsku::firstOrCreate([
                    'numbertotskus' => $variant->sku,
                    'company_id' => auth()->user()->company_id,
                ],[
                    'quantitysku' => $product->quantity,                                                          
                ]);                
                //GET /admin/smart_collections.json?product_id=632910392            
                $collection2ss = Shopify::setShopUrl($shopUrl)->setAccessToken($accessToken)
                    ->get("/admin/smart_collections.json", ["product_id"=>$product2->id]);
                foreach($collection2ss as $collection2)
                {
                    $tag = Tag::firstOrCreate([
                        'tagname' => $collection2->title,
                    ]);                         
                    $sku->tag_id = $tag->id;
                    $sku->tag()->associate($tag);            
                    $sku->save();   
                }
                $sku->designation()->associate($designation);
                $sku->country_price()->associate($countryprice);
                $sku->totsku()->associate($totsku);
                $sku->save();
                $sku->company()->associate(auth()->user()->company_id);
                $sku->save();      
                $product->sku()->associate($sku);
                $product->save();
            }
        }      
        return redirect('/home');
    }
}