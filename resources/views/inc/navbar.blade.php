<body>


  <!-- Navigation -->
  <nav style="background-color: #283b91" class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
    <div class="container">
      <a class="navbar-brand" href="/"><img height="25px" src="{{URL::asset('/img/nairametrics logo.png')}}" /></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
        aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link text-white" href="/premium-article">{{ __('Premium Article') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="/get-newsletter">{{ __('Stock Select') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="/get-crypto-newsletter">{{ __('Cryptocurrency') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="/pricing">{{ __('Pricing') }}</a>
          </li>
          <!-- Authentication Links -->
          @guest
          <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
          </li>
          @if (Route::has('register'))
          <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('register') }}">{{ __('Register') }}</a>
          </li>
          @endif
          @else
          <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link text-white dropdown-toggle" href="#" role="button"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
              {{ Auth::user()->name }} <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </div>
          </li>
          @endguest
        </ul>
      </div>
    </div>
  </nav>