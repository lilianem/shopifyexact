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

            button {
                margin-top: 10px;
            }            
        </style>
@endsection

@section('title', "Shopify App - Registered Webhooks List")

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Store Id: {{$store->id}}</h2>
                </div>
                <div class="card-body">                   
                    <table class="table mb-0">
                        @if (!empty($webhooks))
                            <thead>                            
                                <tr>                        
                                    <th>Webhook Id</th>
                                    <th>Webhook Topic</th>                                 
                                </tr>
                            </thead>
                            <tbody>                      
                                @foreach($webhooksCollect as $webhook)
                                    <tr>
                                        <td>{{ $webhook['id'] }}</td>
                                        <td>{{ $webhook['topic'] }}</td>                                    
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <p>No Wehooks Available</p>                                                        
                        @endif                                               
                    </table>
                </div>                
                        
            </div>
            {{ $webhooksCollect->links() }}
            <a href="{{ URL::to('/storelist') }}"><button type="button" class='btn btn-primary btn-sm'>Back</button></a>          
            <a href="{{ URL::to('shop/create/webhooksshopify') }}"><button type="button" class="btn btn-primary btn-sm">Create Webhooks Shopify</button></a>
            <a href="{{ URL::to('shop/delete/webhooksshopify') }}"><button type="button" class="btn btn-primary btn-sm">Delete Registered Webhooks Shopify</button></a>            
            <a href="{{ URL::to('/skus') }}"><button type="button" class="btn btn-primary btn-sm">Skus List</button></a>
            <a href="{{ URL::to('/orders') }}"><button type="button" class="btn btn-primary btn-sm">Orders List</button></a>
            <a href="{{ URL::to('/customers') }}"><button type="button" class="btn btn-primary btn-sm">Customers List</button></a>
            
        </div>        
    </div>    
</div>
@endsection
