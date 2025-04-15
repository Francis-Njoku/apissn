@extends('layouts.auth_v4')

@section('content')



<div class="back-to-home">
    <a href="" class="back-button btn btn-icon btn-primary"><i data-feather="arrow-left" class="icons"></i></a>
</div>

<!-- Hero Start -->
<section class="bg-auth-home d-table w-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6">
                <div class="me-lg-5">
                    <img src="{{ url('/assetz/images/home/ssn-signup.png') }}" class="img-fluid d-block mx-auto" alt="">
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="card shadow rounded border-0">
                    <div class="card-body">
                        <h4 class="card-title text-center">Signup</h4>
                        <form class="login-form mt-4" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First name <span class="text-danger">*</span></label>
                                        <div class="form-icon position-relative">
                                            <i data-feather="user" class="fea icon-sm icons"></i>
                                            <input value="{{ old('name') }}" type="text" name="name" class="form-control ps-5 @error('name') is-invalid @enderror" placeholder="First Name" name="s" required="">
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last name <span class="text-danger">*</span></label>
                                        <div class="form-icon position-relative">
                                            <i data-feather="user-check" class="fea icon-sm icons"></i>
                                            <input value="{{ old('last_name') }}" name="last_name" type="text" class="form-control ps-5 @error('last_name') is-invalid @enderror" placeholder="Last Name" required="">
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <div class="form-icon position-relative">
                                            <i data-feather="phone" class="fea icon-sm icons"></i>
                                            <input type="text" name="phone" class="form-control ps-5 @error('phone') is-invalid @enderror" placeholder="Phone Number" name="phone" required="">
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Your Email <span class="text-danger">*</span></label>
                                        <div class="form-icon position-relative">
                                            <i data-feather="mail" class="fea icon-sm icons"></i>
                                            <input value="{{ old('email') }}" type="email" class="form-control ps-5 @error('email') is-invalid @enderror" placeholder="Email" name="email" required="">
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="form-icon position-relative">
                                            <i data-feather="key" class="fea icon-sm icons"></i>
                                            <input value="{{ old('password') }}" name="password" type="password" class="form-control ps-5 @error('email') is-invalid @enderror" placeholder="Password" required="">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->

                                <!--<div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">I Accept <a href="#" class="text-primary">Terms And Condition</a></label>
                                        </div>
                                    </div>
                                </div>
                                end col-->

                                <div class="col-md-12">
                                    <div class="d-grid">
                                        <button class="btn btn-primary">Register</button>
                                    </div>
                                </div>
                                <!--end col-->

                                <div class="mx-auto">
                                    <p class="mb-0 mt-3"><small class="text-dark me-2">Already have an account ?</small> <a href="/login" class="text-dark fw-bold">Sign in</a></p>
                                </div>
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>
<!--end section-->
<!-- Hero End -->
@endsection