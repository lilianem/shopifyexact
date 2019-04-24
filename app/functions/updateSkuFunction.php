<?php

/**
 * Function to retrieve persisted data for the example
 * @param string $key
 * @return null|string
 */
function updateSkuFunction($result, $productdetail, $id, $sku)
{
    if (NULL == $result['Stock'])
    {
        $stock = 0;
    }
    else
    {
        $stock = $result['Stock'];
    }
    
    $totsku = App\Models\Totsku::where('numbertotskus', $result['Code'])->first();
    $totsku->quantitysku = $result['Stock'];    
    $totsku->save();    
    $sku->designation->designationname = $result['Description'];
    $sku->designation->description = $productdetail->ExtraDescription;    
    $sku->designation->save(); 
    $sku->tag->tagname = $result['ItemGroupDescription'];
    $sku->tag->save();
    $modifyDetails = array(
        "title" => $sku->designation->designationname,
        'body_html' => $sku->designation->description,
        "tags" => $sku->tag->tagname,
    );
    foreach ($sku->products as $productB)
    {            
        $productB->quantity = $sku->totsku->quantitysku;
        $productB->save();            
        $storeProvider = App\Models\StoreProvider::where('store_id', $productB->store->id)->first();
        $config = array(
            'ShopUrl' => $productB->store->domain,                
            'AccessToken' => $storeProvider->provider_token,
        );       
        $shopify = new \PHPShopify\ShopifySDK($config); 
        try
        {
            $productOne = $shopify->Product($productB->provproductid)->get();
            $productString = 'OK';
        } catch ( Exception $e)
        {
            $productString = '';
        }
        if (!empty($productString))
        {                
            $products = $shopify->Product($productB->provproductid)->put($modifyDetails);  
        } else
        {
            $products = $shopify->Product->post($modifyDetails);  
        }
        $productVariants = $products['variants'];
        $locations = $shopify->Location->get();
        foreach($locations as $location)
        {
            foreach ($productVariants as $productVariant)
            {
                $inventoryItemId = $productVariant['inventory_item_id'];
                $inventoryItemTrack = $shopify->InventoryItem($inventoryItemId)->put(["tracked" => true]);
                $modify_quantity = array(
                    "inventory_item_id" => $inventoryItemId,
                    "location_id" => $location['id'],                                    
                    "available" => $stock
                );                            
                $inventoryLevel = $shopify->InventoryLevel->set($modify_quantity);
            }
        }               
    }       
}