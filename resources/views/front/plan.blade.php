@extends('layouts.front')

@section('content')
<div class="container px-4 py-5 mx-auto">
    <div class="card23">
    <div class="card card0 ">
        <div class="d-flex flex-lg-row flex-column-reverse">
            <div class="card card1">
                <div class="row d-flex px-lg-4 px-3 pt-3">
                    <h6 id="logo"><strong>Subscribe to Stock Select Newsletter</strong></h6>
                </div>
                <div class="row justify-content-center my-auto">
                    <div class="col-lg-8 my-5">
                        <h3 class="mb-3">Get your free Hotjar account now.</h3> <small class="text-muted">Try Hotjar BUSINESS free for 15 days<br>Downgrade to Basic (Free Forever) anytime.</small>
                        <div class="form-group mt-5"> <input type="text" id="name" class="form-control" required> <label class="form-control-placeholder" for="name">Full Name</label> </div>
                        <div class="form-group mt-4"> <input type="email" id="mail" class="form-control" required> <label class="form-control-placeholder" for="mail">Email</label> </div>
                        <div class="row justify-content-center my-3"> <button class="btn btn-gray">Sign up</button> </div>
                        <div class="row justify-content-center my-2"> <small class="text-muted">or</small> </div>
                        <div class="row justify-content-center my-2"> <a href="#"><img id="google" src="https://i.imgur.com/8lJt6UN.png" class="mr-2">Sign up with Google</a> </div>
                    </div>
                </div>
                <div class="bottom text-center mb-3"> <a href="#" class="sm-text mx-auto mb-3">Already have an account?</a> </div>
            </div>
            <div class="card card2"> 
                <img id="image" height="1000px;" src="{{ url('/img/newsletter-stock-subscription.jpg') }}"> 
            </div>
        </div>
    </div>
</div>
</div>
@endsection