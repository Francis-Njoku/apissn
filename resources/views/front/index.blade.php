@extends('layouts.front')

@section('content')
<header class="masthead text-center text-white">
    <div class="masthead-content">
      <div class="container">
        <h1 class="masthead-heading mb-0">Become a Smarter Investor</h1>
        <h2 class="masthead-subheading mb-0">Discover market-beating stocks, 
          learn to invest with confidence, and get insights into the factors that drive capital appreciation</h2>
        <a href="/pricing" class="btn btn-primary btn-xl rounded-pill mt-5">Subscribe Now</a>
      </div>
    </div>
    <div class="bg-circle-1 bg-circle"></div>
    <div class="bg-circle-2 bg-circle"></div>
    <div class="bg-circle-3 bg-circle"></div>
    <div class="bg-circle-4 bg-circle"></div>
  </header>

  <section>
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 order-lg-2">
          <div class="p-5">
            <img class="img-fluid rounded-circle" alt="stock select" src="{{ url('/img/stock-1.jpg') }}">
          </div>
        </div>
        <div class="col-lg-6 order-lg-1">
          <div class="p-5">
            <h2 class="display-6">Get at least two stock recommendation monthly.</h2>
            <p>Get exclusive market intelligence on stocks and market in general</p>
            <a href="/stock-select/pricing" class="btn btn-md btn-primary">Subscribe</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class=" bg-light">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="p-5">
            <img class="img-fluid rounded-circle" src="{{ url('/img/stock-2.jpg') }}" alt="Stock select">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-5">
            <h2 class="display-6">Monthly Buy, Sell or Hold Recommendations</h2>
            <p>Succinctly written analysis of company results and corporate
               actions</p>
            <a href="/stock-select/pricing" class="btn btn-md btn-primary">Subscribe</a>

          </div>
        </div>
      </div>
    </div>
  </section>

  <!--<section>
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 order-lg-2">
          <div class="p-5">
            <img class="img-fluid rounded-circle" alt="stock select" src="{{ url('/img/crypto-front.png') }}">
          </div>
        </div>
        <div class="col-lg-6 order-lg-1">
          <div class="p-5">
            <h2 class="display-6">What Crypto did</h2>
            <p>Notes on the cryptocurrency market activities and how to navigate the various altcoins and the latest ICOs </p>
            <a href="/crypto/pricing" class="btn btn-md btn-primary">Subscribe</a>
          </div>
        </div>
      </div>
    </div>
  </section>-->
  @endsection