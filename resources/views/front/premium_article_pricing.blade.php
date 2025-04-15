@extends('layouts.front')

@section('content')
<div class="container-fluid bg-gradient p-5">

    <div class="row m-auto text-center w-75">
        
        
        <div class="col-md-12 princing-item red">
            <div class="pricing-divider ">
                <h3 class="text-light">MONTHLY</h3>
                <h4 class="my-0 display-4 text-light font-weight-normal mb-3"><span class="h3">&#8358;</span> 5,000
                    <span class="h5"></span></h4> <svg class='pricing-divider-img' enable-background='new 0 0 300 100'
                    height='100px' id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100'
                    width='300px' x='0px' xml:space='preserve' y='0px'>
                    <path class='deco-layer deco-layer--4'
                        d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428	c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z'
                        fill='#FFFFFF'></path>
                </svg>
            </div>
            <div class="card-body bg-white mt-0 shadow">
                <ul class="list-unstyled mb-5 position-relative">
                    <li>Full Access to 4 weekly articles on company performances </li>
                    <li>The story behind their results</li>
                    <li>Deeper audit of their financials and what their numbers means</li>
                    <li>Outlook on performances and what you should look out for</li>        
                </ul>
                @guest
                <a href="/check" class="btn btn-lg btn-block btn-custom ">Subscribe</a>
                @else
                <form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal"
                    role="form">
                    <input type="hidden" name="email" value="{{ Auth::user()->email }}"> {{-- required --}}
                    <input type="hidden" name="orderID" value="345">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="amount" value="500000"> {{-- required in kobo --}}
                    <input type="hidden" name="first_name" value="{{ Auth::user()->name }}">
                    <input type="hidden" name="last_name" value="{{ Auth::user()->last_name }}">
                    <input type="hidden" name="phone" value="{{Auth::user()->phone }}">
                    <input type="hidden" name="metadata" value="{{ json_encode($array = ['plan_type' => '41',]) }}">
                    {{-- For other necessary things you want to add to your payload. it is optional though --}}
                    <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
                    <input type="hidden" name="key" value="{{ config('paystack.secretKey') }}"> {{-- required --}}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    {{-- employ this in place of csrf_field only in laravel 5.0 --}}
                    <p>
                        <button class="btn btn-lg btn-block btn-custom" type="submit" value="Pay Now!">
                            Continue
                        </button>
                    </p>
                </form>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection