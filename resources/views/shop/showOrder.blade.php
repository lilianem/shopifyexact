@extends('layouts.app')

@section('stylesheet')
    <!-- Styles -->
    <style>
        html, body {
           background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
                color: #040c41;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
@endsection

@section('title', "Shopify App - Show Order Details")

@section('content')
<div class="container">
  <div class="card">
    <div class="card">        
      <div class="card-header"> 
      <h2>Order Id: {{$id}}</h2>    
      </div>
      <div class="card-body">
        <div class="table-responsive">      
          <table class="table table-striped">
            <thead>
              @if (!empty($order))                        
                <tr>
                  <th>Order #</th>
                  <th>Financial Status</th>
                  <th>Total HT</th>                  
                  <th>VAT</th>
                  <th>Total Discount</th>
                  <th>Total Shipping</th>
                  <th>Total TTC</th>
                  <th>Shipping Address Last Name</th>
                  <th>Shipping Address City - Country</th>
                  <th>Store Vendor</th>           
                </tr>
            </thead>                                    
                  <tbody>
                    <tr>                                                     
                      <td>{{$order['order_number']}}</td>
                      <td>{{$order['financial_status']}}</td>                                                 
                      <td>{{$order['total_price']-$order['total_tax']}}</td>                      
                      <td>{{$order['total_tax']}}</td>
                      <td>{{$order['total_discounts']}}</td>                                      
                      <td>{{$order['total_shipping_price_set']['shop_money']['amount']}}</td>
                      <td>{{$order['total_price']}}</td>                      
                      @if (array_key_exists('shipping_address', $order)) 
                        <td>{{$order['shipping_address']['last_name']}}</td>
                        <td>{{$order['shipping_address']['city']}} - {{$order['shipping_address']['country']}}</td>
                      @else
                        <td> </td>
                        <td> </td>
                      @endif
                      <td>{{$order['line_items'][0]['vendor']}}</td>
                    </tr>
                  </tbody>           
              @else
                <p>No Orders Available</p>
              @endif
          </table>
        </div>
      </div>
    </div>
  </div>  

  <div class="card">
    <div class="card-header"> 
      <p class="font-weight-bold">Order Details</p>    
    </div>
    <div class="card-body">
      <div class="table-responsive">      
        <table class="table table-striped">
          <thead>
            @if (!empty($order['line_items']))                        
              <tr>
                <th>Item Id</th>
                <th>Item Title</th>
                <th>Sku</th>
                <th>Quantity</th>
                <th>Price TTC</th>
                <th>Discount TTC</th>
                <th>Total TTC</th>
                <th>Currency</th>

              </tr>
          </thead>          
          @foreach($order['line_items'] as $line_item)                            
                <tbody>
                  <tr>
                    <td>{{$line_item['product_id']}}</td>                                                      
                    <td>{{$line_item['title']}}</td>
                    <td>{{$line_item['sku']}}</td>                                                 
                    <td>{{$line_item['quantity']}}</td>
                    <td>{{$line_item['price_set']['shop_money']['amount']}}</td>
                    @if(!empty($line_item['discount_allocations']))
                    <td>{{$line_item['discount_allocations'][0]['amount']}}</td>
                    <td>{{$line_item['quantity'] * $line_item['price_set']['shop_money']['amount'] - $line_item['discount_allocations'][0]['amount']}} </td>
                    @else
                    <td>0.00</td>
                    <td>{{$line_item['quantity'] * $line_item['price_set']['shop_money']['amount']}} </td>
                    @endif                    
                    <td>{{$line_item['price_set']['shop_money']['currency_code']}}</td>                    
                  </tr>
                </tbody>              
              @endforeach
            @else
              <p>No Line Items Available</p>
            @endif
        </table>
      </div>
    </div>
  </div>
  <div class="btn-group">
		<a href="{{ URL::to('/orders') }}"><button type="button" class='btn btn-primary btn-sm'>Orders List</button></a>		    
	</div>	
</div>
@endsection