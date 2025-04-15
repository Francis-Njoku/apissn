@extends('layouts.auth')

@section('content')



<div class="container px-4 py-5 mx-auto">
    <div class="card card0">
        <div class="d-flex flex-lg-row flex-column-reverse">
            <div class="card card1">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-10 my-5">
                        <h3 class="mb-5 text-center heading">Stock Select Newsletter</h3>
                        <h6 class="msg-info">Sign up</h6>
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                        <div class="form-group"> <label class="form-control-label text-muted">First Name</label> 
                            <input value="{{ old('name') }}" type="text" id="name" name="name" placeholder="First Name" class="form-control @error('name') is-invalid @enderror" required autocomplete="name">
                        </div>
                        
                        <div class="form-group"> <label class="form-control-label text-muted">Last Name</label> 
                            <input value="{{ old('last_name') }}" type="text" id="last_name" name="last_name" placeholder="Last Name" class="form-control @error('last_name') is-invalid @enderror" required autocomplete="last_name">
                        </div>
                        
                        <div class="form-group"> <label class="form-control-label text-muted">Email</label> 
                            <input value="{{ old('email') }}" type="email" id="email" name="email" placeholder="Email Address" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group"> <label class="form-control-label text-muted">Phone</label> 
                            <input value="{{ old('phone') }}" type="text" id="phone" name="phone" placeholder="Phone Number" class="form-control @error('phone') is-invalid @enderror" required autocomplete="phone">
                        </div>

                        <div class="form-group"> <label class="form-control-label text-muted">Password</label> 
                            <input value="{{ old('password') }}" type="password" id="password" name="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" required autocomplete="password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group"> <label class="form-control-label text-muted">Confirm Password</label> 
                            <input value="{{ old('password_confirmation') }}" type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" class="form-control" required autocomplete="password_confirmation">
                        </div>
                                       
                        <div class="row justify-content-center my-3 px-3"> <button class="btn-block btn-color">Sign In</button> </div>
                        </form>
                        <div class="row justify-content-center my-2"> 
                            
                            <a href="/login"><small class="text-muted">Already have an account?</small></a>
                             
                        </div>
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
