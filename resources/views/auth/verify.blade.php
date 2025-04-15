@extends('layouts.auth')

@section('content')


<div class="container px-4 py-5 mx-auto">
    <div class="card card0">
        <div class="d-flex flex-lg-row flex-column-reverse">
            <div class="card card1">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-10 my-5">
                        
                        <h3 class="mb-5 text-center heading">Email Verification</h3>
                        <div class="ml-5">
                            @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                        </div>
                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf

                        <div class="form-group"> <label class="form-control-label text-muted">Username</label> <input type="text" id="email" name="email" placeholder="Phone no or email id" class="form-control"> </div>
                        <div class="form-group"> <label class="form-control-label text-muted">Password</label> <input type="password" id="psw" name="psw" placeholder="Password" class="form-control"> </div>
                        <div class="row justify-content-center my-3 px-3"> <button class="btn-block btn-color">{{ __('click here to request another') }}</button> </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card card2">
                <div class="my-auto mx-md-5 px-md-5 right">
                    <h3 class="text-white">By Subscribing you get access to the following</h3> 
                    
                        <ul>
                            <li>Get at least two stock recommendation monthly</li>
                            <li>Monthly Buy, Sell or Hold Recommendations</li>
                            <li>Get exclusive market intelligence on stocks and market in general</li>
                            <li>Succinctly written analysis of company results and corporate
                                actions</li>
                        </ul>
                        <p></p>

                    
                    <small class="text-white">This newsletter is specially edited by Ugodre Obi-Chukwu, 
                        the founder of Nairametrics</small>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

@endsection
