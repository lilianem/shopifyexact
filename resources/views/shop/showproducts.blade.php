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

@section('title', "Shopify App")

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"> 
      <h2>Products List</h2>    
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($products) >= 1)
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Product N°#</th>
                <th>Designation</th>
                <th>Quantity</th>
                <th>Action</th>
              </tr>
            </thead>           
            @foreach($products as $product)
              <tbody>
                <tr>
                  <td>{{$product->id}}</td>
                  <td>{{$product->title}}</td>
                  @foreach($product->variants as $variant)                  
                  <td>{{$variant->inventory_quantity}}</td>
                  @endforeach                 
                  <td><a href="{{ URL::to('/shop/edit/' . $product->id) }}"><button type="button" class="btn btn-primary btn-sm">Update</button></a></td>
              </tbody>
            @endforeach
          </table>
        @else
          <p>Aucun produit existant</p>
        @endif  
      </div>
    </div>
  </div>
</div>
@endsection