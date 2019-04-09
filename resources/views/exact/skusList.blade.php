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

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"> 
      <h2>Exact - Skus List</h2>    
    </div>
    <div class="card-body">
      <div class="table-responsive">      
          <table class="table table-striped">
            @if (!empty($resultsCollect))
            <thead>            
              <tr>
                <th>Product Id</th>
                <th>Product Code</th>
                <th>Product Description</th>
                <th>Product Stock</th>
                <th>Product Salesprice</th>   
                <th>Action</th>            
              </tr>
            </thead>           
            @foreach($resultsCollect as $result)                           
              <tbody>
                <tr>
                  <td>{{ $result['ID'] }}</td>
                  <td>{{ $result['Code'] }}</td>
                  <td>{{ $result['Description'] }}</td>
                  <td>{{ $result['Stock'] }}</td>
                  <td>{{ $result['SalesPrice'] }}</td>                                                            
                  <td><a href="{{ URL::to('/exact/edit/sku/' . $result['ID']) }}"><button type="button" class="btn btn-primary btn-sm">Update</button></a></td>                    
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
  {{ $resultsCollect->link_function() }}
  <a href="{{ URL::to('/exact/sync/sku') }}"><button type="button" class="btn btn-primary btn-sm">Sync Exact->Shopify Skus</button></a>       
</div>
@endsection