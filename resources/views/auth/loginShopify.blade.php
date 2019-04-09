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
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h3>Create a new store</h3>                        
                    </div>
                    <hr class="mb-4">
                    <form method="GET" action="{{ route('login.shopify') }}" aria-label="{{ __('Register') }}">
                        <div class="form-group">
                            <label for="domain">Domain</label>

                            <div class="input-group mb-3">
                                <input id="domain" type="text" class="form-control{{ $errors->has('domain') ? ' is-invalid' : '' }}" name="domain" value="{{ old('domain') }}" placeholder="yourshop" aria-describedby="myshopify" required autofocus>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="myshopify">myshopify.com</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Continue</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <p class="text-center text-muted">Already have a store? <a href="{{ route('storelist') }}">Stores List in here</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
