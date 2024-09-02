@extends('layouts.front')

@section('content')
<div class="container-fluid bg-gradient p-5">

    <div class="row m-auto text-center w-75" id="ssn-stock">
        <div class="col-md-12 mb-5 princing-item red">
            <h2 class="" align="center">
                Stocks
            </h2>

        </div>
        <div class="col-md-6">
            <img class=" mb-3 img-fluid" src="{{URL::asset('/img/stock-select-1.jpg')}}" alt="SSN">
        </div>
        <div class="col-md-6">
            <h3 align="left" class="mb-3 ml-2">Stocks Select Newsletter</h3>

            <ul class="">
                <li align="left">Get at least two stock recommendation monthly</li>
                <li align="left">Monthly Buy, Sell or Hold Recommendations</li>
                <li align="left">Get exclusive market intelligence on stocks and market in general</li>
                <li align="left">Succinctly written analysis of company results and corporate
                    actions</li>
            </ul>
            <p align="left">
                <a href="/stock-select/pricing" class="ml-2 btn btn-gray btn-lg">Subscribe</a>
            </p>
        </div>
    </div>
</div>
<div class=" bg-white">
    <div class="container-fluid p-5">

        <div class="row m-auto text-center w-75" id="ssn-agrotech">
            <div class="col-md-12 mb-5 princing-item red">
                <h2 class="" align="center">
                    Agrotech
                </h2>

            </div>
            <div class="col-md-6">
                <img class=" mb-3 img-fluid" src="{{URL::asset('/img/agrotech.jpg')}}" alt="Agrotech">
            </div>
            <div class="col-md-6">
                <h3 align="left" class="mb-3 ml-2">Agrotech Newsletter</h3>

                <ul class="">
                    <li align="left">Monthly Recommendations on Agro-Tech investments</li>
                    <li align="left">Corporate Profiles on Fund Providers</li>
                    <li align="left">Investment Analysis</li>
                    <li align="left">Cash and Food Crop Analysis</li>
                </ul>
                <p align="left">
                    <a href="#" class="ml-2 btn btn-gray btn-lg">Coming soon</a>
                </p>
            </div>
        </div>
    </div>

</div>
<div class="container-fluid bg-gradient p-5">

    <div class="row m-auto text-center w-75" id="ssn-premium">
        <div class="col-md-12 mb-5 princing-item red">
            <h2 class="" align="center">
                Premium Content
            </h2>

        </div>
        <div class="col-md-6">
            <img class=" mb-3 img-fluid" src="{{URL::asset('/img/premium_content.png')}}" alt="Premium Content">
        </div>
        <div class="col-md-6">
            <h3 align="left" class="mb-3 ml-2">Premium Exclusive Content</h3>

            <ul class="">
                <li align="left">Full Access to 4 weekly articles on company performances </li>
                <li align="left">The story behind their results</li>
                <li align="left">Deeper audit of their financials and what their numbers means</li>
                <li align="left">Outlook on performances and what you should look out for</li>
            </ul>
            <p align="left">
                <a href="/premium-article/pricing" class="ml-2 btn btn-gray btn-lg">Subscribe</a>
            </p>
        </div>
    </div>
</div>
@endsection