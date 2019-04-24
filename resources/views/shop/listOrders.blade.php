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

@section('title', "Shopify App - Orders List")

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"> 
      <h2>Orders List</h2>    
    </div>
    <div class="card-body">
      <div class="table-responsive">      
        <table class="table table-striped">
          <thead>
            @if (!empty($orders))                        
              <tr>
                <th>Order Id</th>
                <th>Order #</th>
                <th>Financial Status</th>
                <th>Total HT</th>
                <th>Total TTC</th>
                <th>VAT</th>
                <th>Total Discount</th>
                <th>Total Shipping</th>
                <th>Shipping Address Last Name</th>
                <th>Shipping Address City - Country</th>
                <th>Store Vendor</th>
                <th>Action</th>              
              </tr>
          </thead>          
          @foreach($ordersCollect as $order)                            
                <tbody>
                  <tr>
                    <td>{{$order['id']}}</td>                                                      
                    <td>{{$order['order_number']}}</td>
                    <td>{{$order['financial_status']}}</td>                                                 
                    <td>{{$order['total_price']-$order['total_tax']}}</td>
                    <td>{{$order['total_price']}}</td>
                    <td>{{$order['total_tax']}}</td>
                    <td>{{$order['total_discounts']}}</td>                                      
                    <td>{{$order['total_shipping_price_set']['shop_money']['amount']}}</td>
                    @if (!empty($order['shipping_address']))
                      <td>{{$order['shipping_address']['last_name']}}</td>
                      <td>{{$order['shipping_address']['city']}} - {{$order['shipping_address']['country']}}</td>
                    @else
                      <td> </td>
                      <td> </td>
                    @endif
                    <td>{{$order['line_items'][0]['vendor']}}</td>              
                    <td><a href="{{ URL::to('/shop/show/order/' .$order['line_items'][0]['vendor']. '/' . $order['id']) }}"><button type="button" class="btn btn-primary btn-sm">Show Order</button></a></td>         
                  </tr>
                </tbody>              
              @endforeach
            @else
              <p>No Orders Available</p>
            @endif
        </table>
      </div>
    </div>
  </div>
  {{ $ordersCollect->links() }}
  <a href="{{ URL::to('/storelist') }}"><button type="button" class="btn btn-primary btn-sm">Stores List</button></a>
  <a href="{{ URL::to('/orders/sync') }}"><button type="button" class="btn btn-primary btn-sm">Sync Orders and Customers</button></a>    
</div>
@endsection