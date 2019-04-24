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

@section('title', "Shopify App - Store List")

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <table class="table mb-0">
                    @if (!empty($storeTestEmpty))
                        <thead>
                            <tr>
                                <th>Store Name</th>
                                <th>Store Domain</th>
                                <th>Action</th>                                
                            </tr>
                        </thead>
                        @foreach($stores as $store)
                        <tbody>                                           
                            <tr>
                                <td>{{ $store->name }}</td>
                                <td>{{ $store->domain }}</td>
                                <td><a href="shop/listwebhooksregisteredshopify/store/{{ $store->id }}">View Registered Webhooks</a></td>
                            </tr>
                        </tbody>
                        @endforeach
                    @else
                        <p>No Stores Available</p>
                                                    
                    @endif                                                                                                    
                </table>            
            </div>
            {{ $stores->links() }}
            <a href="{{ URL::to('/login/shopify/ind') }}"><button type="button" class="btn btn-primary btn-sm">Back</button></a>
            <a href="{{ URL::to('shop/create/webhooksshopify') }}"><button type="button" class="btn btn-primary btn-sm">Create Webhooks Shopify</button></a>
            <a href="{{ URL::to('shop/delete/webhooksshopify') }}"><button type="button" class="btn btn-primary btn-sm">Delete Registered Webhooks Shopify</button></a>            
            <a href="{{ URL::to('/skus') }}"><button type="button" class="btn btn-primary btn-sm">Skus List</button></a>
            <a href="{{ URL::to('/orders') }}"><button type="button" class="btn btn-primary btn-sm">Orders List</button></a>
            <a href="{{ URL::to('/customers') }}"><button type="button" class="btn btn-primary btn-sm">Customers List</button></a>
            
        </div>        
    </div>    
</div>
@endsection
