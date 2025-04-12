@extends('layouts.auth')

@section('content')

<div class="container px-4 py-5 mx-auto">
    <div class="card card0">
        <div class="d-flex flex-lg-row flex-column-reverse">
            <div class="card card1">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-10 my-5">
                        <!--<div class="row justify-content-center"> <img src="{{URL::asset('/img/ssn-logo.png')}}"> </div>-->
                        <h3 class="mb-5 text-center heading">Stock Select Newsletter</h3>
                        <h6 class="msg-info">Please login to your account</h6>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                        <div class="form-group"> <label class="form-control-label text-muted">Email Address</label> <input value="{{ old('email') }}" type="text" id="email" name="email" placeholder="email id" class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                                       
                        <div class="form-group"> <label class="form-control-label text-muted">Password</label> <input type="password" id="psw" name="password" placeholder="Password" class="form-control  @error('password') is-invalid @enderror"> 
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group"> <label class="form-control-label text-muted">Remember Me</label> 
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>

                        </div>

                        <div class="row justify-content-center my-3 px-3"> <button class="btn-block btn-color">Sign In</button> </div>
                        </form>
                        <div class="row justify-content-center my-2"> 
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"><small class="text-muted">Forgot Password?</small></a>
                            @endif 
                        </div>
                    </div>
                </div>
                <div class="bottom text-center mb-5">
                    <p href="{{ route('register') }}" class="sm-text mx-auto mb-3">Don't have an account?<a href="{{ route('register') }}"  class="btn btn-white ml-2">Create new</a></p>
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

@endsection