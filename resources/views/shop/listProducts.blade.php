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

@section('title', "Shopify App - Products List per Sku")

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"> 
      <h2>Sku # {{$sku->id}}</h2>
      <h3>{{$sku->designation->description}}</h3>    
    </div>
    <div class="card-body">
      <div class="table-responsive">      
        <table class="table table-striped">
          <thead>
            @if (!empty($productTestEmpty))
              <tr>
                <th>Product Id</th>
                <th>Product #</th>
                <th>Quantity</th>
                <th>Store Id</th>
                <th>Store Name</th>  
              </tr>
          </thead>           
              @foreach($products as $product)                            
                <tbody>
                  <tr>
                    <td>{{$product->id}}</td>                                                      
                    <td>{{$product->provproductid}}</td>
                    <td>{{$product->quantity}}</td>                                             
                    <td>{{$product->store_id}}</td>                  
                    <td>{{$product->store->name}}</td>                                                                   
                  </tr>
                </tbody>              
              @endforeach
            @else
              <p>No Products Available</p>
            @endif
        </table>
      </div>
    </div>
  </div>
  {{ $products->links() }}
  <a href="{{ URL::to('/skus') }}"><button type="button" class="btn btn-primary btn-sm">Skus List</button></a>
  <a href="{{ URL::to('/shop/show/sku/' . $sku->id) }}"><button type="button" class="btn btn-primary btn-sm">Show Sku</button></a>        
</div>
@endsection