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

@section('title', "Shopify App - Customers List")

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"> 
      <h2>Customers List</h2>    
    </div>
    <div class="card-body">
      <div class="table-responsive">      
        <table class="table table-striped">
          <thead>
            @if (!empty($customersCollect))                        
              <tr>
                <th>Customer Id</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>City</th>
                <th>Country</th>
                <th>Orders Count</th>
                <th>Store Vendor</th>                                
                <th>Action</th>              
              </tr>
          </thead>          
          @foreach($customersCollect as $customer)                            
                <tbody>
                  <tr>
                    <td>{{$customer['id']}}</td>                                                      
                    <td>{{$customer['first_name']}}</td>
                    <td>{{$customer['last_name']}}</td>
                    <td>{{$customer['addresses'][0]['city']}}</td>
                    <td>{{$customer['addresses'][0]['country']}}</td>
                    <td>{{$customer['orders_count']}}</td>
                    @if (!empty($customer['store']))
                      <td>{{$customer['store']}}</td>
                      <td><a href="{{ URL::to('/shop/show/customer/' .$customer['store']. '/' . $customer['id']) }}"><button type="button" class="btn btn-primary btn-sm">Show Customer</button></a></td>
                    @else
                      <td> </td>
                      <td> </td>
                    @endif                                                             
                  </tr>
                </tbody>              
              @endforeach
            @else
              <p>No Customer Available</p>
            @endif
        </table>
      </div>
    </div>
  </div>
  {{ $customersCollect->link_function() }}
  <a href="{{ URL::to('/storelist') }}"><button type="button" class="btn btn-primary btn-sm">Stores List</button></a>
  <a href="{{ URL::to('/orders/sync') }}"><button type="button" class="btn btn-primary btn-sm">Sync Shopify->Exact Orders and Customers</button></a>
  <a href="{{ URL::to('/customers/sync') }}"><button type="button" class="btn btn-primary btn-sm">Sync Shopify->Exact Customers</button></a>    
</div>
@endsection