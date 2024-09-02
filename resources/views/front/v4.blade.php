@extends('layouts.user_v4')

@section('content')
        
        <!-- Hero Start -->
        <section class="bg-half-170 d-table w-100" id="home">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7 col-md-7">
                        <div class="title-heading mt-4">
                            <h1 class="heading mb-3">Build Anything <br>For Your Project</h1>
                            <p class="para-desc text-muted">Launch your campaign and benefit from our expertise on designing and managing conversion centered bootstrap v5 html page.</p>
                            <div class="mt-4 pt-2">
                                <a href="page-services.html" class="btn btn-primary m-1">Our Services</a>
                                <a href="#!" data-type="youtube" data-id="yba7hPeTSjk" class="btn btn-icon btn-pills btn-primary m-1 lightbox"><i data-feather="video" class="icons"></i></a><span class="fw-bold text-uppercase small align-middle ms-2">Watch Now</span>
                            </div>
                        </div>
                    </div><!--end col-->

                    <div class="col-lg-5 col-md-5 mt-4 pt-2 mt-sm-0 pt-sm-0">
                        <img src="{{ url('/assetz/images/illustrator/services.svg') }}" alt="">
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->
        </section><!--end section-->
        <!-- Hero End -->     
        
        <!-- Feature Start -->
        <section class="section pt-0">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <div class="features feature-primary text-center">
                            <div class="image position-relative d-inline-block">
                                <i class="uil uil-flip-h h2 text-primary"></i>
                            </div>

                            <div class="content mt-4">
                                <h5>Built for Everyone</h5>
                                <p class="text-muted mb-0">Nisi aenean vulputate eleifend tellus vitae eleifend enim a Aliquam eleifend aenean elementum semper.</p>
                            </div>
                        </div>
                    </div><!--end col-->
                    
                    <div class="col-md-4 col-12 mt-5 mt-sm-0">
                        <div class="features feature-primary text-center">
                            <div class="image position-relative d-inline-block">
                                <i class="uil uil-minus-path h2 text-primary"></i>
                            </div>

                            <div class="content mt-4">
                                <h5>Responsive Design</h5>
                                <p class="text-muted mb-0">Allegedly, a Latin scholar established the origin of the established text by compiling unusual word.</p>
                            </div>
                        </div>
                    </div><!--end col-->
                    
                    <div class="col-md-4 col-12 mt-5 mt-sm-0">
                        <div class="features feature-primary text-center">
                            <div class="image position-relative d-inline-block">
                                <i class="uil uil-layers-alt h2 text-primary"></i>
                            </div>

                            <div class="content mt-4">
                                <h5>Build Everything</h5>
                                <p class="text-muted mb-0">It seems that only fragments of the original text remain in only fragments the Lorem Ipsum texts used today.</p>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->
        </section><!--end section-->
        <!-- Feature End -->

        <!-- counter Start -->
        <section class="section bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 order-2 order-md-1 mt-4 mt-sm-0 pt-2 pt-sm-0">
                        <div class="section-title me-lg-5">
                            <h4 class="title mb-4">Get Notified About Importent Email</h4>
                            <p class="text-muted">This prevents repetitive patterns from impairing the overall visual impression and facilitates the comparison of different typefaces. Furthermore, it is advantageous when the dummy text is relatively realistic.</p>
                            <a href="javascript:void(0)" class="btn btn-outline-primary">Start Now <i class="uil uil-angle-right-b"></i></a>
                        </div>
                    </div><!--end col-->

                    <div class="col-md-6 order-1 order-md-2">
                        <img src="{{ url('/assetz/images/laptop.png') }}" class="img-fluid" alt="">
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->

            <div class="container mt-100 mt-60">
                <div class="row justify-content-center" id="counter">
                    <div class="col-md-4 mt-4 pt-2">
                        <div class="counter-box text-center px-lg-4">
                            <h2 class="mb-0 text-primary display-4"><span class="counter-value" data-target="97">3</span>%</h2>
                            <h5 class="counter-head">Happy Client</h5>
                            <p class="text-muted mb-0">The most well-known dummy text is the 'Lorem Ipsum', which is said to have originated in the 16th century.</p>
                        </div><!--end counter box-->
                    </div><!--end col-->

                    <div class="col-md-4 mt-4 pt-2">
                        <div class="counter-box text-center px-lg-4">
                            <h2 class="mb-0 text-primary display-4"><span class="counter-value" data-target="15">1</span>+</h2>
                            <h5 class="counter-head">Awards</h5>
                            <p class="text-muted mb-0">The most well-known dummy text is the 'Lorem Ipsum', which is said to have originated in the 16th century.</p>
                        </div><!--end counter box-->
                    </div><!--end col-->

                    <div class="col-md-4 mt-4 pt-2">
                        <div class="counter-box text-center px-lg-4">
                            <h2 class="mb-0 text-primary display-4"><span class="counter-value" data-target="98">3</span>%</h2>
                            <h5 class="counter-head">Project Complete</h5>
                            <p class="text-muted mb-0">The most well-known dummy text is the 'Lorem Ipsum', which is said to have originated in the 16th century.</p>
                        </div><!--end counter box-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->
        </section><!--end section-->
        <!-- counter End -->

        <!-- Testimonial Start -->
        <section class="section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img src="{{ url('/assetz/images/illustrator/analyze_report_4.svg') }}" class="me-md-4" alt="">
                    </div><!--end col-->

                    <div class="col-md-6 mt-4 mt-sm-0 pt-2 pt-sm-0">
                        <div class="section-title ms-lg-5">
                            <h4 class="title mb-4">Clean And Modern Code</h4>
                            <p class="text-muted">This prevents repetitive patterns from impairing the overall visual impression and facilitates the comparison of different typefaces. Furthermore, it is advantageous when the dummy text is relatively realistic.</p>
                            <a href="javascript:void(0)" class="btn btn-outline-primary">Start Now <i class="uil uil-angle-right-b"></i></a>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->

            <div class="container mt-100 mt-60">
                <div class="row justify-content-center">
                    <div class="col-12 text-center">
                        <div class="section-title mb-4 pb-2">
                            <h4 class="title mb-4">Our Happy Customers</h4>
                            <p class="text-muted para-desc mx-auto mb-0">Start working with <span class="text-primary fw-bold">Landrick</span> that can provide everything you need to generate awareness, drive traffic, connect.</p>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->

                <div class="row">
                    <div class="col-12 mt-4">
                        <div class="tiny-three-item">
                            <div class="tiny-slide text-center">
                                <div class="client-testi rounded shadow m-2 p-4">
                                    <img src="{{ url('/assetz/images/client/amazon.svg') }}" class="img-fluid avatar avatar-ex-sm mx-auto" alt="">
                                    <p class="text-muted mt-4">" It seems that only fragments of the original text remain in the Lorem Ipsum texts used today. "</p>
                                    <h6 class="text-primary">- Thomas Israel</h6>
                                </div>
                            </div>

                            <div class="tiny-slide text-center">
                                <div class="client-testi rounded shadow m-2 p-4">
                                    <img src="{{ url('/assetz/images/client/google.svg') }}" class="img-fluid avatar avatar-ex-sm mx-auto" alt="">
                                    <p class="text-muted mt-4">" The most well-known dummy text is the 'Lorem Ipsum', which is said to have originated in the 16th century. "</p>
                                    <h6 class="text-primary">- Carl Oliver</h6>
                                </div>
                            </div>

                            <div class="tiny-slide text-center">
                                <div class="client-testi rounded shadow m-2 p-4">
                                    <img src="{{ url('/assetz/images/client/lenovo.svg') }}" class="img-fluid avatar avatar-ex-sm mx-auto" alt="">
                                    <p class="text-muted mt-4">" One disadvantage of Lorum Ipsum is that in Latin certain letters appear more frequently than others. "</p>
                                    <h6 class="text-primary">- Barbara McIntosh</h6>
                                </div>
                            </div>

                            <div class="tiny-slide text-center">
                                <div class="client-testi rounded shadow m-2 p-4">
                                    <img src="{{ url('/assetz/images/client/paypal.svg') }}" class="img-fluid avatar avatar-ex-sm mx-auto" alt="">
                                    <p class="text-muted mt-4">" Thus, Lorem Ipsum has only limited suitability as a visual filler for German texts. "</p>
                                    <h6 class="text-primary">- Jill Webb</h6>
                                </div>
                            </div>

                            <div class="tiny-slide text-center">
                                <div class="client-testi rounded shadow m-2 p-4">
                                    <img src="{{ url('/assetz/images/client/shopify.svg') }}" class="img-fluid avatar avatar-ex-sm mx-auto" alt="">
                                    <p class="text-muted mt-4">" There is now an abundance of readable dummy texts. These are usually used when a text is required. "</p>
                                    <h6 class="text-primary">- Dean Tolle</h6>
                                </div>
                            </div>

                            <div class="tiny-slide text-center">
                                <div class="client-testi rounded shadow m-2 p-4">
                                    <img src="{{ url('/assetz/images/client/spotify.svg') }}" class="img-fluid avatar avatar-ex-sm mx-auto" alt="">
                                    <p class="text-muted mt-4">" According to most sources, Lorum Ipsum can be traced back to a text composed by Cicero. "</p>
                                    <h6 class="text-primary">- Christa Smith</h6>
                                </div>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->

            <div class="container mt-100 mt-60">
                <div class="row justify-content-center">
                    <div class="col-12 text-center">
                        <div class="section-title mb-4 pb-2">
                            <h4 class="title mb-4">Subscribe for our Latest Newsletter</h4>
                            <p class="text-muted para-desc mx-auto mb-0">Start working with <span class="text-primary fw-bold">Landrick</span> that can provide everything you need to generate awareness, drive traffic, connect.</p>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->

                <div class="row justify-content-center mt-4 pt-2">
                    <div class="col-lg-7 col-md-10">
                        <div class="subcribe-form">
                            <form class="ms-0">
                                <input type="email" id="email" name="email" class="rounded-pill border" placeholder="E-mail :">
                                <button type="submit" class="btn btn-pills btn-primary">Submit <i class="uil uil-arrow-right"></i></button>
                            </form><!--end form-->
                        </div><!--end subscribe form-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->
        </section><!--end section-->
        <!-- Testimonial End -->

@endsection