<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="{{$description}}"/>
		<meta property="og:image" content="{{URL::asset($og_image)}}" />
	<meta property="og:locale" content="en_GB" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{$title}} | Nairametrics" />
    <meta property="og:description" content="{{$description}}" />
    <meta property="og:url" content="{{URL($og_url)}}" />
    <meta name="twitter:image" content="{{URL::asset($og_image)}}" />
    <meta property="og:site_name" content="Nairametrics Research" />
	<title>{{$title}}</title>
    <!-- Bootstrap core CSS -->
    <link href="{{URL::asset('/asset/news_book/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Merriweather:300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
    <link href="{{URL::asset('/asset/news_book/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template -->
    <link href="{{URL::asset('/asset/news_book/css/'.$css)}}" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="76x76" href="{{URL::asset('/img/favicon.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{URL::asset('/img/favicon.png')}}">

	<!--Custom Font-->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="{{ url('/js/html5shiv.js') }}"></script>
    <script src="{{ url('/js/respond.min.js') }}"></script>
    <![endif]-->  
      <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-175685435-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-175685435-1');
</script>   
</head>
<body>