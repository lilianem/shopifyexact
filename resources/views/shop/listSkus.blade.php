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

@section('title', "Shopify App - Skus List")

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"> 
      <h2>Skus List</h2>    
    </div>
    <div class="card-body">
      <div class="table-responsive">      
        <table class="table table-striped">
          <thead>
            @if (!empty($skus))                        
              <tr>
                <th>Sku Id</th>
                <th>Sku #</th>
                <th>Designation</th>
                <th>Country</th>
                <th>Price</th>
                <th>Collection</th>
                <th>Quantity</th>
                <th>Action</th>
                <th>  </th>               
              </tr>
          </thead>          
              @foreach($skus as $sku)                            
                <tbody>
                  <tr>
                    <td>{{$sku->id}}</td>                                                      
                    <td>{{$sku->number}}</td>
                    <td>{{$sku->designation->designationname}}</td> 
                    @if (!empty($sku->country_price))                                                
                      <td>{{$sku->country_price->country->countryname}}</td>
                      <td>{{$sku->country_price->price->amount}}</td>
                    @else
                      <td> </td>
                      <td> </td>
                    @endif
                    <td>{{$sku->tag->tagname}}</td>
                    @if (isset($sku->totsku))               
                      <td>{{$sku->totsku->quantitysku}}</td>
                    @else
                      <td> </td>
                    @endif
                    <td><a href="{{ URL::to('/shop/showProducts/sku/' . $sku->id) }}"><button type="button" class="btn btn-primary btn-sm">Products List</button></a></td>           
                    <td><a href="{{ URL::to('/shop/edit/sku/' . $sku->id) }}"><button type="button" class="btn btn-primary btn-sm">Update Sku</button></a></td>
                  </tr>
                </tbody>              
              @endforeach
            @else
              <p>No Skus Available</p>
            @endif
        </table>
      </div>
    </div>
  </div>
  {{ $skus->links() }}
  <a href="{{ URL::to('/storelist') }}"><button type="button" class="btn btn-primary btn-sm">Stores List</button></a>
  <a href="{{ URL::to('shop/create/sku/') }}"><button type="button" class="btn btn-primary btn-sm">Create Sku</button></a>
  <a href="{{ URL::to('shop/syncshopexact/skus/') }}"><button type="button" class="btn btn-primary btn-sm">Sync Shopify->Exact Skus</button></a>      
</div>
@endsection