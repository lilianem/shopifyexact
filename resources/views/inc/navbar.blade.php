<div class="row">
  <div class="col-sm-12">
    <nav class="navbar fixed-top navbar-light bg-light">
        <a class="navbar-brand" href="#">Shopify Exact App</a>    
            @if (Route::has('login'))                
                  <ul class="navbar-nav mr-auto top-right flex-row">              
                    @auth
                      <li class="nav-item links">
                        <a class="nav-link horiz" href="{{ url('/login/shopify/ind') }}" style="padding-left:10px; padding-right:10px;">Login Shopify</a>
                      </li>
                      <li class="nav-item links">
                        <a class="nav-link horiz" href="{{ url('/login/exact/ind') }}" style="padding-left:10px; padding-right:10px;">Login Exact</a>
                      </li>
                      <li class="nav-item links">
                        <a class="nav-link horiz" href="{{ route('logout') }}" style="padding-left:10px; padding-right:10px;">@lang('Logout')</a>
                      </li>
                    @else
                      <li class="nav-item links">
                        <a class="nav-link horiz" href="{{ route('login') }}" style="padding-left:10px; padding-right:10px;">Login User</a>
                      </li>
                      <li class="nav-item links">
                        <a class="nav-link horiz" href="{{ route('register') }}" style="padding-left:10px; padding-right:10px;">Register User</a>
                      </li>                    
                    @endauth               
                </ul>
            @endif
    </nav>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse" style="top: 60px;">
      <button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      @if (Route::has('login'))      
        <ul class="navbar-nav mr-auto">
          @auth
            <li class="nav-item active links">
              <a class="nav-link" href="{{ URL::to('./home') }}">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item links">
              <a class="nav-link" href="{{ url('/login/shopify/ind') }}">Login Shopify <span class="sr-only"></span></a>
            </li>
            <li class="nav-item links">
              <a class="nav-link" href="{{ url('/login/exact/ind') }}">Login Exact <span class="sr-only"></span></a>
            </li>
            <li class="nav-item links">
              <a class="nav-link" href="{{ route('logout') }}">@lang('Logout')<span class="sr-only"></span></a>
            </li>                                        
          @endauth
        </ul>
      @endif      
    </nav>
  </div>
</div>

