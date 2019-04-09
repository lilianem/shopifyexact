<?php
/**
 * Function to retrieve persisted data for the example
 * @param string $key
 * @return null|string
 */
function createSkuFunction($result, $productdetail, $id, $companyId)
{
    $sku = new App\Models\Sku;
    $sku->number = $result['Code'];       
    $totsku = App\Models\Totsku::firstOrCreate([
        'numbertotskus' => $result['Code'],
        'company_id' => $companyId,
    ],[
        'quantitysku' => $result['Stock'],                                                          
    ]);
    $designation = App\Models\Designation::firstOrCreate([
        'designationname' => $result['Description']                                    
    ],[
        'description' => $productdetail->ExtraDescription,                                                          
    ]);
    $tag = App\Models\Tag::firstOrCreate([
        'tagname' => $result['ItemGroupDescription']                                    
    ]);
    $country = App\Models\Country::firstOrCreate([
        'countryname' => 'France'
    ]);
    $price = App\Models\Price::firstOrCreate([
        'amount' => $result['SalesPrice']
    ]);
    $price->countries()->sync($country->id, false);        
    $countryprice = App\Models\CountryPrice::where(['price_id' => $price->id, 'country_id' => $country->id])->first();    
    $sku->company_id = App\Models\Company::where('companyName', env('COMPANY_NAME'))->first()->id;
    $sku->designation()->associate($designation);
    $sku->tag()->associate($tag);
    $sku->country_price()->associate($countryprice);
    $sku->totsku()->associate($totsku);        
    $sku->save();    
    if (NULL == $result['Stock'])
    {
        $stock = 0;
    }
    else
    {
        $stock = $result['Stock'];
    }         
    $stores = App\Models\Store::select('id','domain')->get();
    foreach ($stores as $store)
    {        
        $storeProvider = App\Models\StoreProvider::where('store_id', $store->id)->first();        
        $config = array(
            'ShopUrl' => $store->domain,            
            'AccessToken' => $storeProvider->provider_token,
        );
        $shopify = new \PHPShopify\ShopifySDK($config);
        $updateDetails = array(
            'title' => $result['Description'],
            'tags' => $result['ItemGroupDescription'],
            'body_html' => $productdetail->ExtraDescription,
            "variants"=>array(
                array(
                    "sku"=> $result['Code'],
                    "price"=>$result['SalesPrice']
                )
            )
        );                
        $product1 = $shopify->Product->post($updateDetails);
        $productTab = App\Models\Product::firstOrCreate([
            'provproductid' => $product1['id'],
        ]);
        if (isset(auth()->user()->id))
        {
            $productTab->user_id = auth()->user()->id;
        } else
        {
            $productTab->user_id = 9; // For webhook Exact user_id;
        }                             
        $productTab->store_id = $store->id;
        $productTab->quantity = $result['Stock'];
        $productTab->sku()->associate($sku);
        $productTab->save();
        $productVariants = $product1['variants'];
        foreach ($productVariants as $productVariant)
        {
            $inventoryItemId = $productVariant['inventory_item_id'];
            $inventoryItemTest = $shopify->InventoryItem($inventoryItemId)->put(["tracked" => true]);                        
            //GET /admin/inventory_levels.json?inventory_item_ids=808950810                       
            $inventoryLevel = $shopify->InventoryLevel->get(['inventory_item_ids' => $inventoryItemId]);
            $modify_quantity = array(
                "location_id" => $inventoryLevel['0']["location_id"],
                "inventory_item_id" => $inventoryLevel['0']["inventory_item_id"],                                                                
                "available" => $stock
            );                                                    
            $inventoryLevel = $shopify->InventoryLevel->set($modify_quantity);
        }
    }        
}