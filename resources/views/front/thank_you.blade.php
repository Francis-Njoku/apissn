@extends('layouts.front')

@section('content')
<div style=" padding: 100px;">
    @if(session('error'))
<div class="col-md-12 col-sm-12 col-xs-12">
<div align="center" class="alert alert-danger">
{{session('error')}}
</div>
</div>
@endif
<div class="jumbotron text-center">
    <h3 class=" display-4">Thank You!</h3>
    <p class="lead"><strong>Please check your email</strong> for more information.
    </p>
    <hr>
    <p>
        Having trouble? <a href="mailto:ssn@nairametrics.com">Contact us</a>
    </p>
    <p class="lead">
        <a class="btn btn-primary btn-sm" href="/" role="button">Continue to homepage</a>
    </p>
</div>
</div>
@endsection