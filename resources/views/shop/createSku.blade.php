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

@section('title', "Shopify App - Create Sku")

@section('content')
<div class="container">
	{!! Form::open(['action' => 'ShopifyController@storeSku' , 'method' => 'POST']) !!}
		<div class="form-group font-weight-bold">
			{{Form::label('number', 'Sku #')}}<br/>
			<input list="numbers" name="number">
				<datalist id="numbers">
					@foreach ($skus as $sku)
						<option value="{{$sku}}">
					@endforeach
				</datalist>
		</div>  	
		<div class="form-group font-weight-bold">
			{{Form::label('designationSku', 'Designation Sku')}}<br/>
			<input list="designations" name="designationSku">
				<datalist id="designations">
					@foreach ($designations as $designation)
						<option value="{{$designation}}">
					@endforeach
				</datalist>
		</div>

		<div class="form-group font-weight-bold">
			{{Form::label('description', 'Description')}}<br/>
			<input list="descriptions" name="description">
				<datalist id="descriptions">
					@foreach ($descriptions as $description)
						<option value="{{$description}}">
					@endforeach
				</datalist>
		</div>

		<div class="form-group font-weight-bold">
			{{Form::label('tagSku', 'Collection Sku')}}<br/>
			<input list="tags" name="tagSku">
				<datalist id="tags">
					@foreach ($tags as $tag)
						<option value="{{$tag}}">
					@endforeach
				</datalist>
		</div>
		<div class="form-group font-weight-bold">
			{{Form::label('country', 'Country')}}<br/>
			<input list="countrys" name="country">
				<datalist id="countrys">
					@foreach ($countries as $country)
						<option value="{{$country}}">
					@endforeach
				</datalist>
		</div>		
		<div class="form-group font-weight-bold">
			{{Form::label('price', 'Price')}}
			{{Form::number('price', '', ['class' => 'form-control', 'min'=> '0', 'placeholder' => 'Price']) }}
		</div>
		<div class="form-group font-weight-bold">
			{{Form::label('quantity', 'Quantity')}}
			{{Form::number('quantity', '', ['class' => 'form-control', 'min'=> '0', 'placeholder' => 'Quantity']) }}
		</div>	               
		<div class="btn-group">
			<a href="{{ URL::to('/skus') }}"><button type="button" class='btn btn-primary form-control' name="cancel" value="cancel">Cancel</button></a>
			<button type="submit" class='btn btn-primary form-control' name="confirm" value="confirm">Confirm</button>
	{!! Form::close() !!}
		</div>
</div>
@endsection