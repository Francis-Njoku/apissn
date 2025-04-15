<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{$header}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{$description}}">
    <meta name="author" content="Nairametrics">

    <meta property="og:image" content="{{URL::asset('/img/favicon.png')}}" />
    <meta property="og:locale" content="en_GB" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{$header}}" />
    <meta property="og:description" content="{{$description}}" />
    <meta property="og:url" content="{{URL($og_url)}}" />
    <meta property="og:site_name" content="{{$header}}" />
    <meta name="email" content="outreach@nairametrics.com" />
    <meta name="website" content="https://nairametrics.com" />
    <meta name="Version" content="v4.0.0" />
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ url('/img/favicon.png') }}">
    <!-- Bootstrap -->
    <link href="{{ url('/assetz/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- tobii css -->
    <link href="{{ url('/assetz/css/tobii.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons -->
    <link href="{{ url('/assetz/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v3.0.6/css/line.css">
    <!-- Slider -->
    <link rel="stylesheet" href="{{ url('/assetz/css/tiny-slider.css') }}" />
    <!-- Main Css -->
    <link href="{{ url('/assetz/css/style.css') }}" rel="stylesheet" type="text/css" id="theme-opt" />
    <link href="{{ url('/assetz/css/colors/default.css') }}" rel="stylesheet" id="color-opt">

</head>

<body>