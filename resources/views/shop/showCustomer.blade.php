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

@section('title', "Shopify App - Show Customer Details")

@section('content')
<div class="container">
  <div class="card">
    <div class="card">        
      <div class="card-header"> 
      <h2>Customer Id: {{$id}}</h2>    
      </div>
      <div class="card-body">
        <div class="table-responsive">      
          <table class="table table-striped">
            <thead>
              @if (!empty($customer))                        
                <tr>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Company</th>
                  <th>Address</th>
                  <th>Address</th>
                  <th>City</th>
                  <th>Country</th>
                  <th>Zip</th>
                  <th>Phone</th>
                  <th>Store Vendor</th>
                  <th>Orders Count</th>
                  <th>Total Spent</th>
                  <th>VAT Number(Note)</th>                    
                </tr>
            </thead>                                    
                  <tbody>
                    <tr>                                                     
                      <td>{{$customer[0]['first_name']}}</td>
                      <td>{{$customer[0]['last_name']}}</td>                                                 
                      <td>{{$customer[0]['addresses'][0]['company']}}</td>
                      <td>{{$customer[0]['addresses'][0]['address1']}}</td>
                      <td>{{$customer[0]['addresses'][0]['address2']}}</td>
                      <td>{{$customer[0]['addresses'][0]['city']}}</td>
                      <td>{{$customer[0]['addresses'][0]['country']}}</td>                                      
                      <td>{{$customer[0]['addresses'][0]['zip']}}</td>
                      <td>{{$customer[0]['addresses'][0]['phone']}}</td>
                      <td>{{$store->name}}</td>
                      <td>{{$customer[0]['orders_count']}}</td>
                      <td>{{$customer[0]['total_spent']}}</td>
                      <td>{{$customer[0]['note']}}</td>
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
  
  <div class="btn-group">
		<a href="{{ URL::to('/customers') }}"><button type="button" class='btn btn-primary btn-sm'>Customers List</button></a>		    
  </div>	
</div>
@endsection