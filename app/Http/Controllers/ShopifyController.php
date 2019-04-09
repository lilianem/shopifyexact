<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 300);

use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Store;
use App\Models\Product;
use App\Models\Company;
use App\Models\Country;
use App\Models\Price;
use App\Models\Tag;
use App\Models\Sku;
use App\Models\Totsku;
use App\Models\Designation;
use App\Models\CountryPrice;
use App\Jobs\Webhookadd;
use PHPShopify\ShopifySDK;
use PHPShopify\AuthHelper;
use App\Models\UserProvider;
use App\Models\StoreProvider;

class ShopifyController extends Controller
{
    public function storelist()
    {   
        $stores = Store::where('company_id', auth()->user()->company_id)->simplePaginate(15);
        return view('shop.storelist')->with('stores', $stores);
    }    

    public function showSku($id)
    {   
        $sku = Sku::Find($id);
        return view('shop.showSku')->with('sku', $sku);         
    }
      
    public function listSkus()
    {        
        $skus = Sku::where('company_id', auth()->user()->company_id)
            ->with(['company', 'designation', 'tag', 'totsku', 'products', 'country_price.country', 'country_price.price'])->simplePaginate(15);        
        return view('shop.listSkus')->with('skus', $skus);                    
    }

    public function listProducts($id)
    {        
        $sku = Sku::with(['designation'])->Find($id);
        $products = Product::where('sku_id', $id)->orderBy('provproductid', 'asc')->with(['store'])->simplePaginate(15);     
        return view('shop.listProducts')->with('sku', $sku)->with('products', $products);        
    }

    public function listCustomers()
    {
        $customers = [];       
        foreach(auth()->user()->company->stores as $store)
        {
            $storeProvider = StoreProvider::where('store_id', $store->id)->first();
            $config = array(
                'ShopUrl' => $store->domain,
                'AccessToken' => $storeProvider->provider_token,
            );           
            $shopify = new \PHPShopify\ShopifySDK($config);
            $customers0 = $shopify->Customer->get();
            $customers0[0]['store'] = $store->name;
            $customers = array_merge($customers, $customers0);
            $customersCollect = collect($customers);                                                                     
        }    
        return view('shop.listCustomers')->with('customersCollect', $customersCollect);       
    }

    public function showCustomer($storeName, $id)
    {           
        $store = Store::where('name', $storeName)->first();
        $storeProvider = StoreProvider::where('store_id', $store->id)->first();        
        $config = array(
            'ShopUrl' => $store->domain,
            'AccessToken' => $storeProvider->provider_token,
        );                         
        $shopify = new \PHPShopify\ShopifySDK($config);          
        $customer = $shopify->Customer->get(['customer_id'=>$id]);           
        return view('shop.showCustomer')->with('store', $store)->with('customer', $customer)->with('id', $id);       
    }

    public function createWebhooksShopify()
    {
        $topics = ['app/uninstalled', 'carts/create', 'carts/update', 'checkouts/create', 'checkouts/delete', 'checkouts/update', 'collection_listings/add', 'collection_listings/remove',
            'collection_listings/update', 'collections/create', 'collections/delete', 'collections/update', 'customer_groups/create',
            'customer_groups/delete', 'customer_groups/update', 'customers/create', 'customers/delete', 'customers/disable', 'customers/enable',
            'customers/update', 'draft_orders/create', 'draft_orders/delete', 'draft_orders/update', 'fulfillment_events/create',
            'fulfillment_events/delete', 'fulfillments/create', 'fulfillments/update', 'order_transactions/create', 'orders/cancelled', 'orders/create',
            'orders/delete', 'orders/fulfilled', 'orders/paid', 'orders/partially_fulfilled', 'orders/updated', 'product_listings/add',
            'product_listings/remove', 'product_listings/update', 'products/create', 'products/delete', 'products/update', 'refunds/create',
            'shop/update', 'themes/create', 'themes/delete', 'themes/publish', 'themes/update', 'inventory_levels/connect',
            'inventory_levels/update', 'inventory_levels/disconnect', 'inventory_items/create', 'inventory_items/update',
            'inventory_items/delete', 'locations/create', 'locations/update', 'locations/delete', 'tender_transactions/create'];
        return view('shop.createWebhooksShopify')->with('topics', $topics);              
    }

    public function registerWebhooksShopify(Request $request)
    {    
        $this->validate($request, [
            'webhookShopify',           
        ]);

        $webhooks = [];
        foreach(auth()->user()->company->stores as $store)
        {   
            $webhookOK = 'OK';         
            foreach($store->providers as $provider)
            {
                $config = array(
                    'ShopUrl' => $store->domain,
                    'AccessToken' => $provider->provider_token,
                );          
                $shopify = new \PHPShopify\ShopifySDK($config);                
                $params = array(
                    "topic" => $request->input('webhookShopify'),
                    "address" => env('APP_URL') . 'webhook/' . str_replace('/', '', $request->input('webhookShopify')),
                    "format" => "json"
                );             
                $webhook0 = $shopify->Webhook->get();
                foreach ($webhook0 as $webhook1)
                {
                    if ($webhook1['topic'] == $request->input('webhookShopify'))
                    {       
                        $webhookOK = 'NOK';
                    }
                }
                if ($webhookOK == 'NOK')
                {
                    header("HTTP/1.1 401 OK");
                    return redirect()->back()->with('error', 'Webhooks Shopify Already Exist!');
                } else if ($webhookOK == 'OK')
                {
                    $webhook2 = $shopify->Webhook->post($params);
                    header("HTTP/1.1 200 OK");         
                    if (NULL !== $provider->webhookShopify_id and $provider->webhookShopify_id !== 0)
                    {
                        $storeProvider = new StoreProvider;
                        $storeProvider->store_id = $store->id;
                        $storeProvider->provider = $provider->provider;
                        $storeProvider->provider_store_id = $provider->provider_store_id;
                        $storeProvider->provider_token = $provider->provider_token;
                        $storeProvider->webhookShopify_id = $webhook2['id'];
                        $storeProvider->save();
                    } else if (NULL == $provider->webhookShopify_id or $provider->webhookShopify_id == 0)
                    {
                        $provider->webhookShopify_id = $webhook2['id'];
                        $provider->update();
                    }
                }   
            }                 
        }
        return redirect()->back()->with('success', 'Webhooks Shopify Registered!');       
    }

    public function deleteWebhooksShopifyForm()
    {
        $stores = Store::where('company_id', auth()->user()->company_id)->simplePaginate(5);
        $deleteWebhooks = [];
        foreach(auth()->user()->company->stores as $store)
        {            
            foreach($store->providers as $provider)
            {
                $config = array(
                    'ShopUrl' => $store->domain,
                    'AccessToken' => $provider->provider_token,
                );          
                $shopify = new \PHPShopify\ShopifySDK($config);                
                $deleteWebhook0 = $shopify->Webhook->get();
                $deleteWebhooks = array_merge($deleteWebhooks, $deleteWebhook0);       
            }
        }
        return view('shop.deleteWebhooksShopifyForm')->with('deleteWebhooks', $deleteWebhooks);               
    }

    public function deleteWebhooksShopify(Request $request)
    {    
        $this->validate($request, [
            'webhookShopify',           
        ]);

        $webhooks = [];
        foreach(auth()->user()->company->stores as $store)
        {            
            foreach($store->providers as $provider)
            {
                $config = array(
                    'ShopUrl' => $store->domain,
                    'AccessToken' => $provider->provider_token,
                );          
                $shopify = new \PHPShopify\ShopifySDK($config);         

                $webhook0 = $shopify->Webhook->get();
                foreach ($webhook0 as $webhook1)
                {
                    if ($webhook1['topic'] == $request->input('webhookShopify'))
                    {                            
                        $storeProviderDB = StoreProvider::where('webhookShopify_id',$webhook1['id'])->first();
                        if (NULL !== $storeProviderDB)
                        {
                            $storeProviderDB->webhookShopify_id = 0; 
                            $storeProviderDB->save();
                        }                        
                        $shopify->Webhook($webhook1['id'])->delete();
                        header("HTTP/1.1 200 OK");
                    }
                }                         
            }           
        }
        return redirect()->back()->with('success', 'Webhooks Shopify Deleted!');       
    }

    public function listWebhooksRegisteredShopifyPerStore($id)
    {   
        $store = Store::Find($id);    
        foreach($store->providers as $provider)
        {
            $config = array(
                'ShopUrl' => $store->domain,
                'AccessToken' => $provider->provider_token,
            );          
            $shopify = new \PHPShopify\ShopifySDK($config);                
            $webhooks = $shopify->Webhook->get();
            header("HTTP/1.1 200 OK");
            $webhooksCollect = collect($webhooks);              
        }        
        return view('shop.listWebhooksRegisteredShopifyPerStore')->with('store', $store)->with('webhooksCollect', $webhooksCollect);         
    }

    public function listOrders()
    {
        $orders = [];
        foreach(auth()->user()->company->stores as $store)
        {
            $storeProvider = StoreProvider::where('store_id', $store->id)->first();
            $config = array(
                'ShopUrl' => $store->domain,
                'AccessToken' => $storeProvider->provider_token,
            );          
            $shopify = new \PHPShopify\ShopifySDK($config);                
            $orders0 = $shopify->Order->get();
            $orders = array_merge($orders, $orders0);
            $ordersCollect = collect($orders);                    
        }        
        return view('shop.listOrders')->with('ordersCollect', $ordersCollect);       
    }

    public function showOrder($storeName, $id)
    {           
        $store = Store::where('name', $storeName)->first();
        $storeProvider = StoreProvider::where('store_id', $store->id)->first();
        $config = array(
            'ShopUrl' => $store->domain,
            'AccessToken' => $storeProvider->provider_token,
        );             
        $shopify = new \PHPShopify\ShopifySDK($config);
        //GET /admin/orders/#{order_id}.json
        $order = $shopify->Order($id)->get();          
        return view('shop.showOrder')->with('order', $order)->with('id', $id);       
    }
    
    public function syncOrders()
    {
        $orders = [];
        foreach(auth()->user()->company->stores as $store)
        {
            $storeProvider = StoreProvider::where('store_id', $store->id)->first();
            $config = array(
                'ShopUrl' => $store->domain,
                'AccessToken' => $storeProvider->provider_token,
            );         
            $shopify = new \PHPShopify\ShopifySDK($config);
            $orders0 = $shopify->Order->get();
            $orders = array_merge($orders, $orders0);

            foreach($orders as $order)
            {
                // If authorization code is returned from Exact, save this to use for token request
                if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
                    setValue('authorizationcode', $_GET['code']);
                }
                // If we do not have a authorization code, authorize first to setup tokens
                if (getValue('authorizationcode') === null) {
                    authorizer();
                }     
                // For Customer
                $connection = connecter();
                // $url = 'crm/Accounts';
                $exactCustomer = new \Picqer\Financials\Exact\Account($connection);
                if (array_key_exists('customer',$order))
                {
                    if (NULL !== $order['customer'])
                    {
                        $exactCustomerId = $exactCustomer->findId($order['customer']['last_name'], $key='Name');
                        $exactCustomer->Name = $order['customer']['last_name'];
                    }
                } else 
                {
                    $exactCustomerId = null;
                    $exactCustomer->Name = "default";
                }                 
                
                if (empty($exactCustomerId))
                {
                    $shopCustomer = $shopify->Customer->get();
                    $exactCustomer->IsSales = 'true';
                    $exactCustomer->Status = 'C';
                    // field in shopify to use it for VAT number
                    $exactCustomer->VATNumber = $shopCustomer[0]['note'];
                    $exactCustomer->AddressLine1 = $shopCustomer[0]['addresses'][0]['address1'];
                    $exactCustomer->AddressLine2 = $shopCustomer[0]['addresses'][0]['address2'];
                    $exactCustomer->City = $shopCustomer[0]['addresses'][0]['city'];
                    $exactCustomer->CountryName = $shopCustomer[0]['addresses'][0]['country'];
                    $exactCustomer->Postcode = $shopCustomer[0]['addresses'][0]['zip'];
                    $exactCustomer->save();
                    $CustomerId = $exactCustomer->ID;
                } else
                {                
                    $shopCustomer = $shopify->Customer->get();

                    $CustomerId = $exactCustomer->find($exactCustomerId)->ID;
                    $customerFind = $exactCustomer->find($exactCustomerId);
                    $customerFind->IsSales = 'true';
                    $customerFind->Status = 'C';

                    // field in shopify to use it for VAT number
                    $customerFind->VATNumber = $shopCustomer[0]['note'];
                    $customerFind->AddressLine1 = $shopCustomer[0]['addresses'][0]['address1'];
                    $customerFind->AddressLine2 = $shopCustomer[0]['addresses'][0]['address2'];
                    $customerFind->City = $shopCustomer[0]['addresses'][0]['city'];
                    $customerFind->CountryName = $shopCustomer[0]['addresses'][0]['country'];
                    $customerFind->Postcode = $shopCustomer[0]['addresses'][0]['zip'];
                    $customerFind->update();
                }
                // For SalesOrder
                // Create the Exact client
                $connection = connecter();
                // $url = 'salesorder/SalesOrders';
                $exactSalesOrder = new \Picqer\Financials\Exact\SalesOrder($connection);
                $exactSalesOrder->OrderedBy=$CustomerId;
                $exactSalesOrder->OrderNumber=(int)($order['order_number'].'560');
                $exactSalesOrder->Created = $order['created_at'];
                $exactSalesOrder->Currency = $order['currency'];                    
                if (!empty($order['discount_applications']) && array_key_exists('value', $order['discount_applications'][0]))
                {
                    if ($order['discount_applications'][0]['value_type'] == 'percentage' )
                    {
                        $exactSalesOrder->Discount = $order['discount_applications'][0]['value'] / 100;
                    } else if ($order['discount_applications'][0]['value_type'] == 'fixed_amount' )
                    {
                        $exactSalesOrder->Discount = ($order['total_discounts']) / $order['total_line_items_price'];
                    }                        
                }
                $soLines = array();
                foreach ($order['line_items'] as $item)
                {
                    // For Item
                    // Create the Exact client
                    $connection = connecter();
                    // $url = 'logistics/Items';
                    $exactItem = new \Picqer\Financials\Exact\Item($connection);
                    $exactItemId = $exactItem->findId($item['sku'], $key='Code');
                    $soLines[] = array(
                        'Item' => $exactItemId,
                        'Description' => $item['sku'],
                        'Quantity' => $item['quantity'],
                        'NetPrice' => $item['price'],
                        'VATAmount' => $item['tax_lines'][0]['price'],
                        'VATCode' => 5,
                        'VATPercentage' => $item['tax_lines'][0]['rate'],
                    );
                }
                $exactSalesOrder->SalesOrderLines = $soLines;
                $exactSalesOrder->save();                           
            } // foreach orders             
        }  
        return redirect()->back()->with('success', 'Orders and Customers Synchronised!');            
    }    
    
    public function syncCustomers()
    {
        $customers = [];
        foreach(auth()->user()->company->stores as $store)
        {
            $storeProvider = StoreProvider::where('store_id', $store->id)->first();            
            $config = array(
                'ShopUrl' => $store->domain,                   
                'AccessToken' => $storeProvider->provider_token,
            );           
            $shopify = new \PHPShopify\ShopifySDK($config);
            $customers0 = $shopify->Customer->get();
            $customers0[0]['store'] = $store->name;
            $customers = array_merge($customers, $customers0);
            foreach($customers as $customer)
            {
                // If authorization code is returned from Exact, save this to use for token request
                if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
                    setValue('authorizationcode', $_GET['code']);
                }
                // If we do not have a authorization code, authorize first to setup tokens
                if (getValue('authorizationcode') === null) {
                    authorizer();
                }
                // For Customer
                $connection = connecter();
                // $url = 'crm/Accounts';
                $exactCustomer = new \Picqer\Financials\Exact\Account($connection);
                $exactCustomerId = $exactCustomer->findId($customer['last_name'], $key='Name');
                if (empty($exactCustomerId))
                {
                    $exactCustomer->Name=$customer['last_name'];
                    $exactCustomer->IsSales = 'true';
                    $exactCustomer->Status = 'C';

                    // field in shopify to use it for VAT number
                    $exactCustomer->VATNumber = $customer['note'];
                    $exactCustomer->AddressLine1 = $customer['addresses'][0]['address1'];
                    $exactCustomer->AddressLine2 = $customer['addresses'][0]['address2'];
                    $exactCustomer->City = $customer['addresses'][0]['city'];
                    $exactCustomer->CountryName = $customer['addresses'][0]['country'];
                    $exactCustomer->Postcode = $customer['addresses'][0]['zip'];
                    $exactCustomer->save();
                    $CustomerId = $exactCustomer->ID;
                } else
                {
                    $CustomerId = $exactCustomer->find($exactCustomerId)->ID;
                    $customerFind = $exactCustomer->find($exactCustomerId);
                    $customerFind->IsSales = 'true';
                    $customerFind->Status = 'C';

                    // field in shopify to use it for VAT number
                    $customerFind->VATNumber = $customer['note'];
                    $customerFind->AddressLine1 = $customer['addresses'][0]['address1'];
                    $customerFind->AddressLine2 = $customer['addresses'][0]['address2'];
                    $customerFind->City = $customer['addresses'][0]['city'];
                    $customerFind->CountryName = $customer['addresses'][0]['country'];
                    $customerFind->Postcode = $customer['addresses'][0]['zip'];
                    $customerFind->update();
                } 
            } // foreach customers            
        }    
        return redirect()->back()->with('success', 'Customers Synchronised!');          
    }

    public function editSku($id)
    {
        $sku = Sku::Find($id);
        return view('shop.editSku')->with('sku', $sku);               
    }

    public function createSku()
    {        
        $tags = Tag::orderBy('tagname', 'asc')->pluck('tagname');        
        $skus = Sku::orderBy('number', 'asc')->pluck('number', 'id');
        $designations = Designation::orderBy('designationname', 'asc')->pluck('designationname', 'id');
        $descriptions = Designation::orderBy('description', 'asc')->pluck('description', 'id');
        $countries = Country::orderBy('countryname', 'asc')->pluck('countryname', 'id');
        return view('shop.createSku')->with('tags', $tags)->with('skus', $skus)
            ->with('designations', $designations)->with('descriptions', $descriptions)->with('countries', $countries);
    }

    public function storeSku(Request $request)
    {
        $this->validate($request, [
            'number',
            'numbertotskus' => 'unique:totskus',
            'quantity',
            'designationSku',
            'tagSku',            
            'country',
            'price'            
        ]);
        $sku = new Sku;
        $sku->number = $request->input('number');       
        $totsku = Totsku::firstOrCreate([
            'numbertotskus' => $request->input('number'),
            'company_id' => auth()->user()->company_id,
        ],[
            'quantitysku' => $request->input('quantity'),                                                          
        ]);
        $designation = Designation::firstOrCreate([
            'designationname' => $request->input('designationSku')                                    
        ],[
            'description' => $request->input('description'),                                                          
        ]);
        $tag = Tag::firstOrCreate([
            'tagname' => $request->input('tagSku')                                    
        ]);
        $country = Country::firstOrCreate([
            'countryname' => $request->input('country')
        ]);
        $price = Price::firstOrCreate([
            'amount' => $request->input('price')
        ]);
        $price->countries()->sync($country->id, false);       
        $countryprice = CountryPrice::where(['price_id' => $price->id, 'country_id' => $country->id])->first();
        $sku->country_price()->associate($countryprice);
        $sku->company_id = auth()->user()->company_id; 
        $sku->designation()->associate($designation);
        $sku->tag()->associate($tag);        
        $sku->totsku()->associate($totsku);        
        $sku->save();         
        $stores = Store::select('id','domain')->get();
        foreach ($stores as $store)
        {
            $storeProvider = StoreProvider::where('store_id', $store->id)->first();            
            $config = array(
                'ShopUrl' => $store->domain,
                'AccessToken' => $storeProvider->provider_token,
            );
            $shopify = new \PHPShopify\ShopifySDK($config);
            $updateDetails = array(
                'title' => $request->input('designationSku'),
                'body_html' => $request->input('description'),
                'tags' => $request->input('tagSku'),
                "variants"=>array(
                    array(
                        "sku"=> $request->input('number'),
                        "price"=>$request->input('price')
                    )
                )
            );                
            $product1 = $shopify->Product->post($updateDetails);
            $productTab = Product::firstOrCreate([
                'provproductid' => $product1['id'],
            ]);                                
            $productTab->user_id = auth()->user()->id;
            $productTab->store_id = $store->id;
            $productTab->quantity = $request->input('quantity');   
            $productTab->sku()->associate($sku);
            $productTab->save();
            $productVariants = $product1['variants'];                             
            $locations = $shopify->Location->get();
            foreach ($productVariants as $productVariant)
            {
                $inventoryItemId = $productVariant['inventory_item_id'];
                //GET /admin/inventory_items.json
                $inventoryItemTracked = $shopify->InventoryItem($inventoryItemId)->put(["tracked" => true]);                     
                $inventoryLevel = $shopify->InventoryLevel->get(['inventory_item_ids' => $inventoryItemId]);                
                $modify_quantity = array(
                    "location_id" => $inventoryLevel['0']["location_id"],
                    "inventory_item_id" => $inventoryLevel['0']["inventory_item_id"],                                                                
                    "available_adjustment" => $request->input('quantity')
                );                                                    
                $inventoryLevel = $shopify->InventoryLevel->adjust($modify_quantity);
            }
        }
        return redirect()->action('ShopifyController@listSkus')->with('success', 'Sku created!');
    }

    public function syncShopExactSkus()
    {      
        $skus = Sku::with(['totsku', 'products', 'designation', 'tag'])->get();                
        foreach ($skus as $sku)
        {
            if (isset ($sku->totsku))
            {
                if (NULL == $sku->totsku->quantitysku)
                {
                    $quantitysku = 0;
                } else
                {
                    $quantitysku = $sku->totsku->quantitysku;
                }
            }
            
            // If authorization code is returned from Exact, save this to use for token request
            if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
                setValue('authorizationcode', $_GET['code']);
            }
            // If we do not have a authorization code, authorize first to setup tokens
            if (getValue('authorizationcode') === null) {
                authorizer();
            }
            // Create the Exact client
            $connection = connecter();
            $exactsku = new \Picqer\Financials\Exact\Item($connection);
            $exactskuID = $exactsku->findId($sku->number, $key='Code');
            if (NULL !== $exactskuID)
            {
                $exactsku = $exactsku->find($exactskuID);           
                $exactsku->Description = $sku->designation->designationname;
                $exactsku->ExtraDescription = $sku->designation->description;
                $exactsku->update();                
            } else {
                $exactsku->Code = $sku->number;
                $exactsku->Description = $sku->designation->designationname;
                $exactsku->ExtraDescription = $sku->designation->description;        
                $exactsku->save();
            }       
                       
            ///api/v1/{division}/logistics/SalesItemPrices?$filter=ID eq guid'00000000-0000-0000-0000-000000000000'&$select=Account
            $forprices = new \Picqer\Financials\Exact\SalesItemPrice($connection);
            $forpriceId = $forprices->findId($exactsku->ID, $key='Item');
            $forprice = $forprices->findWithSelect($forpriceId, 'ID, Price, ItemCode, Item');
            if (!empty($sku->country_price ))
            {
                if (!empty($forpriceId ))
                {
                    $forprice->Price = $sku->country_price->price->amount;
                    $forprice->update();    
                } else {
                    $forprice->Price = $sku->country_price->price->amount;
                    $forprice->save();
                }
            }                      
                        
            // For ItemWarehouse 
            $connection = connecter();       
            //$url = 'inventory/ItemWarehouses';
            $exactItemWarehouses = new \Picqer\Financials\Exact\ItemWarehouse($connection);            
            $exactItemWarehouseId = $exactItemWarehouses->findId($exactsku->ID, $key='Item');
            $exactItemWarehouse = $exactItemWarehouses->findWithSelect($exactItemWarehouseId, 'ID, Item, Warehouse');
            // For StockCount 
            $connection = connecter();
            // $url = 'crm/Accounts';
            $exactStockCount = new \Picqer\Financials\Exact\StockCount($connection);
            //$exactStockCount->StockCountDate = '2019-03-25T12:00';
            $exactStockCount->StockCountDate = date('c');
            $exactStockCount->Status = 21;            
            $exactStockCount->Warehouse = $exactItemWarehouse->Warehouse;
            $exactStockCountLine = array();        
            $exactStockCountLine[] = array(
                'Item' =>  $exactsku->ID,
                'StockCountID' => $exactStockCount->ID,                
                'QuantityNew' => $quantitysku
            );     
            $exactStockCount->StockCountLines = $exactStockCountLine;
            $exactStockCount->save();
        }
        return redirect()->back()->with('success', 'Skus Shopify->Exact Synchronised!');        
    }       
        
    public function webhookProductUpdate(Request $request)
    {        
        header("HTTP/1.1 200 OK");
        $data = $request->getContent();        
        $data = json_decode($data, true);
        $skus = Sku::with(['totsku', 'products', 'designation', 'tag'])->where('number',  $data['variants'][0]['sku'])->get();
        $sku = Sku::with(['totsku', 'products', 'designation', 'tag'])->where('number', $data['variants'][0]['sku'])->first();        
        $sku->totsku->quantitysku = $data['variants'][0]['inventory_quantity'];        
        $sku->totsku->save();
        $sku->designation->designationname = $data['title'];
        $sku->designation->description = $data['body_html'];
        $sku->designation->save();
        $sku->tag->tagname = $data['tags'];
        $sku->tag->save();        
        $price = Price::updateOrCreate([
            'amount' => $data['variants'][0]['price']
        ]);
        $countryprice = CountryPrice::find($sku->countryprice_id);
        $sku->country_price()->associate($countryprice);
        $sku->save();
        foreach ($skus as $skuA)
        {
            foreach ($skuA->products as $productB)
            {   
                $productB->quantity = $sku->totsku->quantitysku;
                $productB->save();                        
            } // foreach ($skuA->products as $productB)

            // If authorization code is returned from Exact, save this to use for token request
            if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
                setValue('authorizationcode', $_GET['code']);
            }
            // If we do not have a authorization code, authorize first to setup tokens
            if (getValue('authorizationcode') === null) {
                authorizer();
            }
            // Create the Exact client
            $connection = connecter();
            $exactskus = new \Picqer\Financials\Exact\Item($connection);
            $exactskuID = $exactskus->findId($skuA->number, $key='Code');
            $exactsku = $exactskus->find($exactskuID);
            if (!empty($exactsku))
            {
                $exactsku->Description = $skuA->designation->designationname;
                $exactsku->ExtraDescription = $skuA->designation->description;        
                $exactsku->update();    
            } else
            {
                $exactsku->Code = $skuA->number;
                $exactsku->Description = $skuA->designation->designationname;
                $exactsku->ExtraDescription = $skuA->designation->description;        
                $exactsku->save();
            }       
                       
            ///api/v1/{division}/logistics/SalesItemPrices?$filter=ID eq guid'00000000-0000-0000-0000-000000000000'&$select=Account
            $forprices = new \Picqer\Financials\Exact\SalesItemPrice($connection);
            $forpriceId = $forprices->findId($exactsku->ID, $key='Item');
            $forprice = $forprices->findWithSelect($forpriceId, 'ID, Price, ItemCode, Item');
            if (!empty($skuA->country_price ))
            {
                if (!empty($forpriceId ))
                {
                    $forprice->Price = $skuA->country_price->price->amount;
                    $forprice->update();    
                } else
                {
                    $forprice->Price = $skuA->country_price->price->amount;
                    $forprice->save();
                }
            } else 
            {
                if (!empty($forpriceId ))
                {
                    $forprice->Price = $data['variants'][0]['price'];
                    $forprice->update();    
                } else
                {
                    $forprice->Price = $data['variants'][0]['price'];
                    $forprice->save();
                }
            }          
                            
            // For Warehouse 
            $connection = connecter();           
            //  For ItemWarehouse
            //$url = 'inventory/ItemWarehouses';
            $exactItemWarehouses = new \Picqer\Financials\Exact\ItemWarehouse($connection);
            $exactItemWarehouseId = $exactItemWarehouses->findId($exactsku->ID, $key='Item');
            $exactItemWarehouse = $exactItemWarehouses->findWithSelect($exactItemWarehouseId, 'ID, Item, Warehouse');
            // For StockCount 
            $connection = connecter();
            // $url = 'crm/Accounts';
            $exactStockCount = new \Picqer\Financials\Exact\StockCount($connection);
            //$exactStockCount->StockCountDate = '2019-03-25T12:00';
            $exactStockCount->StockCountDate = date('c');
            $exactStockCount->Status = 21;
            $exactStockCount->Warehouse = $exactItemWarehouse->Warehouse;
            $exactStockCountLine = array();        
            $exactStockCountLine[] = array(
                'Item' =>  $exactsku->ID,
                'StockCountID' => $exactStockCount->ID,
                'QuantityNew' => $skuA->totsku->quantitysku
            );     
            $exactStockCount->StockCountLines = $exactStockCountLine;
            $exactStockCount->save();                        
        } // foreach ($skus as $skuA)
    }

    public function webhookOrdersCreate(Request $request)
    {
        header("HTTP/1.1 200 OK");
        $data = $request->getContent();        
        $data = json_decode($data, true);
        
        // If authorization code is returned from Exact, save this to use for token request
        if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
            setValue('authorizationcode', $_GET['code']);
        }
        // If we do not have a authorization code, authorize first to setup tokens
        if (getValue('authorizationcode') === null) {
            authorizer();
        }     
        // For Customer
        $connection = connecter();
        // $url = 'crm/Accounts';
        $exactCustomer = new \Picqer\Financials\Exact\Account($connection);
        //
        if (array_key_exists('customer',$data))        
        {
            if (NULL !== $data['customer'])
            {
                $exactCustomerId = $exactCustomer->findId($data['customer']['last_name'], $key='Name');
                $exactCustomer->Name = $data['customer']['last_name'];
                if (empty($exactCustomerId))
                {
                    $exactCustomer->IsSales = 'true';
                    $exactCustomer->Status = 'C';
                    // field in shopify to use it for VAT number
                    $exactCustomer->VATNumber = $data['customer']['note'];
                    $exactCustomer->AddressLine1 = $data['customer']['default_address']['address1'];
                    $exactCustomer->AddressLine2 = $data['customer']['default_address']['address2'];
                    $exactCustomer->City = $data['customer']['default_address']['city'];
                    $exactCustomer->CountryName = $data['customer']['default_address']['country'];
                    $exactCustomer->Postcode = $data['customer']['default_address']['zip'];
                    $exactCustomer->save();
                    $CustomerId = $exactCustomer->ID;
                } else
                {                             
                    $CustomerId = $exactCustomer->find($exactCustomerId)->ID;
                    $customerFind = $exactCustomer->find($exactCustomerId);
                    $customerFind->IsSales = 'true';
                    $customerFind->Status = 'C';

                    // field in shopify to use it for VAT number
                    $customerFind->VATNumber = $data['customer']['note'];
                    $customerFind->AddressLine1 = $data['customer']['default_address']['address1'];
                    $customerFind->AddressLine2 = $data['customer']['default_address']['address2'];
                    $customerFind->City = $data['customer']['default_address']['city'];
                    $customerFind->CountryName = $data['customer']['default_address']['country'];
                    $customerFind->Postcode = $data['customer']['default_address']['zip'];
                    $customerFind->update();
                }
            }
        } else 
        {
            $exactCustomerId = null;
            $exactCustomer->Name = "default";
            $exactCustomer->IsSales = 'true';
            $exactCustomer->Status = 'C';
            $exactCustomer->save();
            $CustomerId = $exactCustomer->ID;
        }
        // For SalesOrder
        // Create the Exact client
        $connection = connecter();
        // $url = 'salesorder/SalesOrders';
        $exactSalesOrder = new \Picqer\Financials\Exact\SalesOrder($connection);
        $exactSalesOrder->OrderedBy=$CustomerId;
        $exactSalesOrder->OrderNumber=(int)($data['order_number'].'550');
        $exactSalesOrder->Created = $data['created_at'];
        $exactSalesOrder->Currency = $data['currency'];
        if (!empty($data['discount_applications']) && array_key_exists('value', $data['discount_applications'][0]))
        {
            if ($data['discount_applications'][0]['value_type'] == 'percentage' )
            {
                $exactSalesOrder->Discount = $data['discount_applications'][0]['value'] / 100;
            } else if ($data['discount_applications'][0]['value_type'] == 'fixed_amount' )
            {
                $exactSalesOrder->Discount = ($data['total_discounts']) / $data['total_line_items_price'];
            }                       
        }
        $soLines = array();
        foreach ($data['line_items'] as $item)
        {
            // For Item
            // Create the Exact client
            $connection = connecter();
            // $url = 'logistics/Items';
            $exactItem = new \Picqer\Financials\Exact\Item($connection);
            $exactItemId = $exactItem->findId($item['sku'], $key='Code');
            $soLines[] = array(
                'Item' => $exactItemId,
                'Description' => $item['sku'],
                'Quantity' => $item['quantity'],
                'NetPrice' => $item['price'],
                'VATAmount' => $item['tax_lines'][0]['price'],
                'VATCode' => 5,
                'VATPercentage' => $item['tax_lines'][0]['rate'],
            );    
        }
        $exactSalesOrder->SalesOrderLines = $soLines;
        $exactSalesOrder->save();      
    }

    public function webhookCustomersCreate(Request $request)
    {
        header("HTTP/1.1 200 OK");
        $data = $request->getContent();        
        $data = json_decode($data, true);
        
        // If authorization code is returned from Exact, save this to use for token request
        if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
            setValue('authorizationcode', $_GET['code']);
        }
        // If we do not have a authorization code, authorize first to setup tokens
        if (getValue('authorizationcode') === null) {
            authorizer();
        }
        // For Customer
        $connection = connecter();
        // $url = 'crm/Accounts';
        $exactCustomer = new \Picqer\Financials\Exact\Account($connection);
        $exactCustomerId = $exactCustomer->findId($data['last_name'], $key='Name');
        if (empty($exactCustomerId))
        {
            $exactCustomer->Name=$data['last_name'];
            $exactCustomer->IsSales = 'true';
            $exactCustomer->Status = 'C';
    
            // field in shopify to use it for VAT number
            $exactCustomer->VATNumber = $data['note'];
            $exactCustomer->AddressLine1 = $data['addresses'][0]['address1'];
            $exactCustomer->AddressLine2 = $data['addresses'][0]['address2'];
            $exactCustomer->City = $data['addresses'][0]['city'];
            $exactCustomer->CountryName = $data['addresses'][0]['country'];
            $exactCustomer->Postcode = $data['addresses'][0]['zip'];
            $exactCustomer->save();
            $CustomerId = $exactCustomer->ID;
        } else
        {
            $CustomerId = $exactCustomer->find($exactCustomerId)->ID;
            $customerFind = $exactCustomer->find($exactCustomerId);
            $customerFind->IsSales = 'true';
            $customerFind->Status = 'C';
    
            // field in shopify to use it for VAT number
            $customerFind->VATNumber = $data['note'];
            $customerFind->AddressLine1 = $data['addresses'][0]['address1'];
            $customerFind->AddressLine2 = $data['addresses'][0]['address2'];
            $customerFind->City = $data['addresses'][0]['city'];
            $customerFind->CountryName = $data['addresses'][0]['country'];
            $customerFind->Postcode = $data['addresses'][0]['zip'];
            $customerFind->update();
        }       
    }

    public function webhookExactSubscription()
    {
        // If authorization code is returned from Exact, save this to use for token request
        if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
            setValue('authorizationcode', $_GET['code']);
        }
        // If we do not have a authorization code, authorize first to setup tokens
        if (getValue('authorizationcode') === null) {
            authorizer();
        }
        
        $connection = connecter();                

		$topics = [
            'Items',
			'FinancialTransactions',
			'SalesOrders',
			'SalesInvoices',
			'PurchaseOrders',
			'GoodsDeliveries',
			'CostTransactions',
			'TimeTransactions',
			'Documents',
			'Quotations',
			'BankAccounts',
		];    
      
        foreach($topics as $topic)
		{
            // For WebhookSubscriptions
            // $url = 'webhooks/WebhookSubscriptions';
            $subscription = new \Picqer\Financials\Exact\WebhookSubscription($connection);
            $subscriptionId = $subscription->findId($topic, $key='Topic');
            if (NULL !== $subscriptionId)
            {                
                //$subscription->deleteSubscriptions();
                $subscriptionResult = $subscription->find($subscriptionId);
                $subscriptionResult->CallbackURL = env('WEBHOOKEXACTCALLBACK_URL');
                $subscriptionResult->Topic = $topic;
                $subscriptionResult->save();
            } else 
            {
                $subscription->CallbackURL = env('WEBHOOKEXACTCALLBACK_URL');
                $subscription->Topic = $topic;
                $subscription->save();
            }
            
        }
        return view('exact.exactWebhook')->with('connection', $connection)->with('success', 'Webhooks subscribe');        
    }
    
    public function exactWebhookCallback(Request $request)
    {
        if (isset($request['Content'])) {
            // If authorization code is returned from Exact, save this to use for token request
            if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
                setValue('authorizationcode', $_GET['code']);
            }
            // If we do not have a authorization code, authorize first to setup tokens
            if (getValue('authorizationcode') === null) {
                authorizer();
            }        
            $connection = connecter(); 
            $itemInstance = new \Picqer\Financials\Exact\Item($connection);
            $itemFields = ['ID', 'Barcode', 'Code', 'Description', 'ExtraDescription']; // Fields to retrieve
            $item = $itemInstance->filter("ID eq guid'{$request['Content']['Key']}'", "", implode(',', $itemFields));

            // Create the Exact client
            $connection = connecter();
            $products = new \Picqer\Financials\Exact\AccountItem($connection);
            $results = $products->get(['accountId' => "guid'00000000-0000-0000-0000-000000000000'"]);   
            ini_set('max_execution_time', 300);
            foreach ($results as $result)
            {
                if ($result->Code == $item[0]->Code)
                {
                    if (isset(auth()->user()->company_id))
                    {
                        $company = Company::Find(auth()->user()->company_id)
                            ->with(['skus', 'skus.designation', 'skus.tag', 'skus.totsku', 'skus.products', 'skus.country_price.country', 'skus.country_price.price']);
                    } else
                    {
                        $company = Company::where('companyName', env('COMPANY_NAME'))->first();
                    }             
                    $sku = Sku::with(['totsku', 'products', 'designation', 'tag'])->where('number', $result->Code)->first();
                    $resultArray = json_decode(json_encode($result), true);
                    if (empty($sku))              
                    {                    
                        createSkuFunction($resultArray, $item[0], $result->Code, $company->id);                    
                    }
                    else                                
                    {
                        updateSkuFunction($resultArray, $item[0], $result->Code, $sku);
                    }               
                }                            
            }
        }        
    }   

    public function updateSku(Request $request, $id)
    {
        $this->validate($request, [
            'number',
            'numbertotskus' => 'unique:totskus',
            'quantity',
            'designationSku',
            'description',
            'tagSku',            
            'country',
            'price'            
        ]);
        
        $sku = Sku::with(['totsku', 'products', 'designation', 'tag'])->Find($id);
        $skus = Sku::with(['totsku', 'products', 'designation', 'tag'])->where('number', $sku->number)->get();
        $sku->number = $request->input('number');
        $sku->save();
        $sku->totsku->quantitysku = $request->input('quantity');
        $sku->totsku->numbertotskus = $request->input('numbertotskus');
        $sku->totsku->save();
        $sku->designation->designationname = $request->input('designationSku');
        $sku->designation->description = $request->input('description');
        $sku->designation->save();
        $sku->tag->tagname = $request->input('tagSku');
        $sku->tag->save();        
        $country = Country::updateOrCreate([
            'countryname' => $request->input('country')
        ]);
        $price = Price::updateOrCreate([
            'amount' => $request->input('price')
        ]);
        $price->countries()->sync($country->id, false);       
        $countryprice = CountryPrice::where(['price_id' => $price->id, 'country_id' => $country->id])->first();
        $sku->country_price()->associate($countryprice);
        $sku->save();         
        $modifyDetails = array(
            "title" => $sku->designation->designationname,
            "body_html" => $sku->designation->description,
            "tags" => $sku->tag->tagname,
            "variants"=>array(
                array(
                    "sku"=> $request->input('number'),
                    "price"=>$request->input('price')
                )
            )
        );
        foreach ($skus as $skuA)
        {
            foreach ($skuA->products as $productB)
            {   
                $productB->quantity = $sku->totsku->quantitysku;
                $productB->save();
                $storeProvider = StoreProvider::where('store_id', $productB->store->id)->first();
                $config = array(
                    'ShopUrl' => $productB->store->domain,
                    'AccessToken' => $storeProvider->provider_token,
                );              
                $shopify = new \PHPShopify\ShopifySDK($config);
                //Update products
                $products = $shopify->Product($productB->provproductid)->put($modifyDetails);                        
                //Get variants of a product
                $productVariants = $products['variants'];
                $locations = $shopify->Location->get();
                foreach($locations as $location)
                {
                    foreach ($productVariants as $productVariant)
                    {
                        $inventoryItemId = $productVariant['inventory_item_id'];
                        $modify_quantity = array(
                            "inventory_item_id" => $inventoryItemId,
                            "location_id" => $location['id'],                                    
                            "available" => $productB->quantity
                        );                            
                        $inventoryLevel = $shopify->InventoryLevel->set($modify_quantity);
                    }
                }
            }
        }
        return redirect()->action('ShopifyController@listSkus')->with('success', 'Sku modified!');      
    }
}