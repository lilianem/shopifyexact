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

@section('title', "Shopify App - Delete Webhooks Shopify")

@section('content')
<div class="container">
	{!! Form::open(['action' => 'ShopifyController@deleteWebhooksShopify' , 'method' => 'POST']) !!} 	
		<div class="form-group font-weight-bold">
			{{Form::label('webhookShopify', 'Topic Webhook Shopify')}}<br/>
			<input list="webhookShopifys" name="webhookShopify">
				<datalist id="webhookShopifys">
					@foreach ($deleteWebhooks as $deleteWebhook)
						<option value="{{$deleteWebhook['topic']}}">
					@endforeach
				</datalist>
		</div>           
		<div class="btn-group">
			<a href="{{ URL::to('/storelist') }}"><button type="button" class='btn btn-primary form-control' name="cancel" value="cancel">Back</button></a>
			<button type="submit" class='btn btn-primary form-control' name="confirm" value="confirm">Confirm</button>
	{!! Form::close() !!}
		</div>
</div>
@endsection