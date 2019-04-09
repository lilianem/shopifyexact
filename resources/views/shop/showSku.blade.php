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

@section('title', "Shopify App - Show Sku Details")

@section('content')
<div class="container">
  <div class="card">
    <div class="card-body"> 
      <div class="card-header font-weight-bold">
		    <h2>Sku Id: {{$sku->id}}</h2>
	    </div>      
    </div>  
    <div class="card-body"> 
      <div class="card-header font-weight-bold">
        Number
        </div>
      <div class="card-text">
        {{$sku->number}}
        </div>
    </div>  
    <div class="card-body"> 
      <div class="card-header font-weight-bold">
        Designation Sku
      </div>
      <div class="card-text">
		    {{$sku->designation->designationname}}
	    </div>
    </div>  
    <div class="card-body"> 
      <div class="card-header font-weight-bold">	
        Description
      </div>
      <div class="card-text">
		    {{$sku->designation->description}}
      </div>
    </div>  
    <div class="card-body"> 
      <div class="card-header font-weight-bold">
        Collection
      </div>
      <div class="card-text">
		    {{$sku->tag->tagname}}
      </div>
    </div>  
    <div class="card-body"> 
      <div class="card-header font-weight-bold">
        Country
      </div>
      <div class="card-text">
		    {{$sku->country_price->country->countryname}}
	    </div>
    </div>  
    <div class="card-body"> 
      <div class="card-header font-weight-bold">
          Price
      </div>
      <div class="card-text">
		    {{$sku->country_price->price->amount}}
      </div>
    </div>  
    <div class="card-body"> 
      <div class="card-header font-weight-bold">
        Quantity
      </div>
      <div class="card-text">
		    {{$sku->totsku->quantitysku}}
      </div>
    </div>
	</div>	               
  <div class="btn-group">
		<a href="{{ URL::to('/shop/showProducts/sku/' . $sku->id) }}"><button type="button" class='btn btn-primary btn-sm'>Products List Per Sku</button></a>
		<a href="{{ URL::to('/shop/edit/sku/' . $sku->id) }}"><button type="button" class="btn btn-primary btn-sm">Update Sku</button></a>    
	</div>	
</div>
@endsection