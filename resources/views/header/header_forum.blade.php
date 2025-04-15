<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="{{$description}}">
  <meta name="author" content="Nairametrics">

  <meta property="og:image" content="{{URL::asset('/img/favicon.png')}}" />
	<meta property="og:locale" content="en_GB" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{$header}}" />
    <meta property="og:description" content="{{$description}}" />
    <meta property="og:url" content="{{URL($og_url)}}" />
    <meta property="og:site_name" content="{{$header}}" />

    <link rel="apple-touch-icon" sizes="76x76" href="{{URL::asset('/img/favicon.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{URL::asset('/img/favicon.png')}}">

  <title>{{$title}}</title>

  <!-- Bootstrap core CSS -->
  <link href="{{ url('/asset/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
  <!--<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css' rel='stylesheet'>-->
  <link href="{{ url('/asset/css/reg.css') }}" rel="stylesheet">
  <link href="{{ url('/asset/css/pricing-page.css') }}" rel="stylesheet">
  <link href="{{ url('/asset/css/forum.css') }}" rel="stylesheet">
  <link href="{{ url('/dash/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="{{ url('/asset/css/one-page-wonder.css') }}" rel="stylesheet">
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-175685435-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
  
    gtag('config', 'UA-175685435-1');
  </script>
</head>