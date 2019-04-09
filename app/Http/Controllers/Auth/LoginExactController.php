<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Models\Store;
use App\Models\Company;
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
use \Picqer\Financials\Exact\Connection;
use \Picqer\Financials\Exact\Item;
use \Picqer\Financials\Exact\StockCount;
use \Picqer\Financials\Exact\StockCountLine;
use Carbon\Carbon;

class LoginExactController extends Controller
{
    public function loginExact()
    {
       return view('auth.loginExact');
    }

    public function appConnection()
    {
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
        return view('exact.connected')->with('connection', $connection)->with('success', 'You are connected!');       
    }
    
    public function appCallback()
    {
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
        return view('exact.callback');
    }   

    public function skusList()
    {
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
        $products = new \Picqer\Financials\Exact\AccountItem($connection);
        $results = $connection->get($products->url() . "?accountId=guid'00000000-0000-0000-0000-000000000000'");        
        $resultsCollect = collect($results);       
        return view('exact.skusList')->with('resultsCollect', $resultsCollect);            
    }

    public function editExactSku(Connection $connection, $id)
    {        
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
        $skus = new \Picqer\Financials\Exact\Item($connection);
        $sku = $skus->find($id);        
        return view('exact.editExactSku')->with('connection', $connection)->with('sku', $sku);      
    }

    public function updateExactSku(Connection $connection, Request $request, $id)
    {
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
        $skus = new \Picqer\Financials\Exact\Item($connection);
        $sku = $skus->find($id);        
        $sku->Description = $request->input('description');
        $sku->ExtraDescription = $request->input('extraDescription');        
        $sku->update();
        ///api/v1/{division}/logistics/SalesItemPrices?$filter=ID eq guid'00000000-0000-0000-0000-000000000000'&$select=Account
        $forprices = new \Picqer\Financials\Exact\SalesItemPrice($connection);
        $forpriceId = $forprices->findId($id, $key='Item');
        $forprice = $forprices->findWithSelect($forpriceId, 'ID, Price, ItemCode, Item');            
        $forprice->Price = $request->input('price');
        $forprice->update();
        // For Warehouse 
        $connection = connecter();
        // $url = 'inventory/Warehouses';
        $exactWarehouse = new \Picqer\Financials\Exact\Warehouse($connection);     
        // For StockCount 
        $connection = connecter();
        // $url = 'crm/Accounts';
        $exactStockCount = new \Picqer\Financials\Exact\StockCount($connection);
        //$exactStockCount->StockCountDate = '2019-03-25T12:00';
        $exactStockCount->StockCountDate = date('c');
        //dd($exactStockCount->StockCountDate);
        $exactStockCount->Status = 21;
        //dd(gettype(new \DateTime('2019-03-25')));
        $exactStockCount->Warehouse = $exactWarehouse->first()->ID;
        $exactStockCountLine = array();        
        $exactStockCountLine[] = array(
            'Item' =>  $sku->ID,
            'StockCountID' => $exactStockCount->ID,
            'QuantityNew' => $request->input('stock')
        );     
        $exactStockCount->StockCountLines = $exactStockCountLine;
        $exactStockCount->save();
        return redirect()->back()->withInput()->with('success', 'Sku Modified!');
    }

    public function syncExactShopifySkus()
    {
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
        $products = new \Picqer\Financials\Exact\AccountItem($connection);
        $results = $connection->get($products->url() . "?accountId=guid'00000000-0000-0000-0000-000000000000'");   
        ini_set('max_execution_time', 300);
        foreach ($results as $result)
        {
            // /api/v1/{division}/logistics/Items?$filter=ID eq guid'00000000-0000-0000-0000-000000000000'&$select=Barcode        
            $productdetails = new \Picqer\Financials\Exact\Item($connection);
            $productdetailId = $productdetails->findId($result['ID'], $key='ID');                
            $productdetail = $productdetails->findWithSelect($productdetailId, 'ID, Description, ExtraDescription');            
            $company = Company::Find(auth()->user()->company_id)->with(['skus', 'skus.designation', 'skus.tag', 'skus.totsku', 'skus.products', 'skus.country_price.country', 'skus.country_price.price'])->first();
            $sku = Sku::with(['totsku', 'products', 'designation', 'tag'])->where('number', $result['Code'])->first();
            if (empty($sku))              
            {                    
                createSkuFunction($result, $productdetail, $result['Code'],$company->id);                    
            }
            else                                
            {
                updateSkuFunction($result, $productdetail, $result['Code'], $sku);
            }                     
        }
        return redirect()->back()->with('success', 'Skus Synchronised!');    
    }   
}