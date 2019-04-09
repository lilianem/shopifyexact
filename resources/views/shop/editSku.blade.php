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

@section('title', "Shopify App - Update Sku")

@section('content')
<div class="container">
{!! Form::open(['route' => ['shop.updateSku', $sku->id], 'method' => 'PUT']) !!}	
  <div class="form-group font-weight-bold">
		<h2>Sku Id: {{$sku->id}}</h2>
	</div>
  <div class="form-group font-weight-bold">
		{{Form::label('number', 'Number')}}
		{{Form::text('number', $sku->number, ['class' => 'form-control', 'placeholder' => 'Number']) }}
	</div>
	<div class="form-group font-weight-bold">
		{{Form::label('designationSku', 'Designation Sku')}}
		{{Form::text('designationSku', $sku->designation->designationname, ['class' => 'form-control', 'placeholder' => 'Designation Sku']) }}
	</div>
	<div class="form-group font-weight-bold">
		{{Form::label('description', 'Description')}}
		{{Form::text('description', $sku->designation->description, ['class' => 'form-control', 'placeholder' => 'Description']) }}
	</div>
	<div class="form-group font-weight-bold">
		{{Form::label('tagSku', 'Collection')}}
		{{Form::text('tagSku', $sku->tag->tagname, ['class' => 'form-control', 'placeholder' => 'Collection']) }}
	</div>
  <div class="form-group font-weight-bold">
		{{Form::label('country', 'Country')}}
        @if (!empty($sku->country_price)) 
		    {{Form::text('country', $sku->country_price->country->countryname, ['class' => 'form-control', 'placeholder' => 'Country']) }}
        @else
            {{Form::text('country', " " , ['class' => 'form-control', 'placeholder' => 'Country']) }}
        @endif 
	</div>
  <div class="form-group font-weight-bold">
		{{Form::label('price', 'Price')}}
        @if (!empty($sku->country_price)) 
		    {{Form::text('price', $sku->country_price->price->amount, ['class' => 'form-control', 'placeholder' => 'Price']) }}
        @else
            {{Form::text('price', " ", ['class' => 'form-control', 'placeholder' => 'Price']) }}
        @endif
	</div>
  <div class="form-group font-weight-bold">
		{{Form::label('quantity', 'Quantity')}}
		{{Form::text('quantity', $sku->totsku->quantitysku, ['class' => 'form-control', 'placeholder' => 'Quantity']) }}
	</div>	               
  <div class="btn-group">
		<a href="{{ URL::to('/skus') }}"><button type="button" class='btn btn-primary form-control' name="cancel" value="cancel">Cancel</button></a>
		<button type="submit" class='btn btn-primary form-control' name="confirm" value="confirm">Confirm</button>
	</div>
{!! Form::close() !!}	
</div>
@endsection