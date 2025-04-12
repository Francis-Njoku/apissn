      <!-- Navbar STart -->
      <header id="topnav" class="defaultscroll sticky">
          <div class="container">
              <!-- Logo container-->
              <a class="logo" href="/">
                  <img src="{{ url('/img/nm-ssn-logo.png') }}" height="24" class="logo-light-mode" alt="">
              </a>

              <!-- Logo End -->

              <!-- End Logo container-->
              <div class="menu-extras">
                  <div class="menu-item">
                      <!-- Mobile menu toggle-->
                      <a class="navbar-toggle" id="isToggle" onclick="toggleMenu()">
                          <div class="lines">
                              <span></span>
                              <span></span>
                              <span></span>
                          </div>
                      </a>
                      <!-- End mobile menu toggle-->
                  </div>
              </div>

              <!--Login button Start
              <ul class="buy-button list-inline mb-0">
                  <li class="list-inline-item mb-0">
                      <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                          <div class="btn btn-icon btn-pills btn-soft-primary"><i data-feather="settings" class="fea icon-sm"></i></div>
                      </a>
                  </li>

                  <li class="list-inline-item ps-1 mb-0">
                      <a href="https://1.envato.market/landrick" target="_blank">
                          <div class="btn btn-icon btn-pills btn-primary"><i data-feather="shopping-cart" class="fea icon-sm"></i></div>
                      </a>
                  </li>
              </ul>-->
              <!--Login button End-->


              <div id="navigation">

                  <!-- Navigation Menu-->
                  <ul class="navigation-menu">
                      <li><a href="/" class="sub-menu-item"><i class="uil uil-home fs-6 align-middle"></i></a></li>
                      <li class="has-submenu parent-menu-item">
                          <a href="javascript:void(0)">Services</a><span class="menu-arrow"></span>
                          <ul class="submenu">
                              <li><a href="/premium-article" class="sub-menu-item">Premium Article</a></li>
                              <li><a href="/get-newsletter" class="sub-menu-item">Stock Select</a></li>
                              <li><a href="/get-crypto-newsletter" class="sub-menu-item">Cryptocurrency</a></li>
                          </ul>
                      </li>

                      <li><a href="/pricing" class="sub-menu-item">Pricing</a></li>
                      @guest
                      <li><a href="{{ route('login') }}" class="sub-menu-item">Sign In</a></li>
                      @if (Route::has('register'))
                      <li><a href="{{ route('register') }}" class="sub-menu-item">Sign Up</a></li>
                      @endif
                      @else
                      <li class="has-submenu parent-menu-item">

                          <a href="#">{{ Auth::user()->name }}</a><span class="menu-arrow"></span>
                          <ul class="submenu">
                              <li>
                              <a href="{{ url('/signout') }}" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                              Logout
                          </a>

                          <form id="logout-form" action="{{ url('/signout') }}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                          </form>
                              </li>

                          </ul>
                      </li>
                      @endguest
                  </ul>
                  <!--end navigation menu-->
              </div>

              <!--end navigation-->
          </div>
          <!--end container-->
      </header>
      <!--end header-->
      <!-- Navbar End -->