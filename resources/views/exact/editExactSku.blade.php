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

@section('title', "Exact App")

@section('content')
<div class="container">
{!! Form::open(['route' => ['exact.updateExactSku', $sku->ID], 'method' => 'PUT']) !!}	
  <div class="form-group font-weight-bold">
		{{Form::label('id', 'id')}}
		{{Form::text('id', $sku->ID, ['class' => 'form-control', 'placeholder' => 'id']) }}
	</div>
  <div class="form-group font-weight-bold">
		{{Form::label('number', 'Number')}}
		{{Form::text('number', $sku->Code, ['class' => 'form-control', 'placeholder' => 'Number']) }}
	</div>
	<div class="form-group font-weight-bold">
		{{Form::label('description', 'Designation Sku')}}
		{{Form::text('description', $sku->Description, ['class' => 'form-control', 'placeholder' => 'Designation Sku']) }}
	</div>
    <div class="form-group font-weight-bold">
		{{Form::label('extraDescription', 'Description Sku')}}
		{{Form::text('extraDescription', $sku->ExtraDescription, ['class' => 'form-control', 'placeholder' => 'Description Sku']) }}
	</div>
	<div class="form-group font-weight-bold">
		{{Form::label('stock', 'Stock')}}
		{{Form::text('stock', $sku->Stock, ['class' => 'form-control', 'placeholder' => 'Stock']) }}
	</div>  
  <div class="form-group font-weight-bold">
		{{Form::label('price', 'Cost Price Standard')}}
		{{Form::text('price', $sku->CostPriceStandard, ['class' => 'form-control', 'placeholder' => 'Price']) }}
	</div>  	               
  <div class="btn-group">
		<a href="{{ URL::to('/login/exact/skusList') }}"><button type="button" class='btn btn-primary form-control' name="cancel" value="cancel">Cancel</button></a>
		<button type="submit" class='btn btn-primary form-control' name="confirm" value="confirm">Confirm</button>
	</div>
{!! Form::close() !!}	
</div>
@endsection