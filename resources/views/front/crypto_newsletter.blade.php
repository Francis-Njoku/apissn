@extends('layouts.news')

@section('content')

<div class="overlay"></div>

<video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
  <source src="{{URL::asset('/asset/news_book/img/agrotech-bg.jpg')}}" type="video/mp4">
</video>

<div class="masthead">
  <div class="masthead-bg"></div>
  <div class="container h-100">
    <div class="row h-100">
      <div class="col-12 my-auto">
        @if(session('success2'))
        <div align="center" class=" col-md-12 col-sm-12 col-xs-12">
          <div class="alert alert-success">
            {{session('success2')}}
          </div>
        </div>
        @endif
        <div class="masthead-content text-white py-5 py-md-0">
          <h4 class="mb-2">Welcome to the Nairametrics What crypto did Newsletter delivered to your inbox bi-weekly. 
            This newsletter is edited by the Nairametrics team
          </h4>
          <p><a class=" mb-4 text-white " href="/crypto/pricing">Not a subscriber? Follow this link to subscribe.</a>
          </p>
          <p class="mb-4 text-white">
            Get complete access to all my previous newsletters including ones that published before you subscribed.
          </p>
          <p class=" mb-4 text-white ">
            Remember to seek further information from your financial advisor should you ever have doubts about what
            stock to buy, sell or hold.
            <br />
            Best of Luck
          </p>
          <form method="POST" action="{{ route('send_crypto_book') }}" accept-charset="UTF-8" id='contactForm'
            class="form-horizontal" role="form">
            {{ csrf_field() }}
            <div class="input-group input-group-newsletter">
              <select name="name" id="" class="form-control">
                <option value="">Select Week</option>
                @foreach($get_news as $news)
                <option value="{{$news->news_date}}">{{date('D d M Y',strtotime($news->news_date))}}</option>
                @endforeach
              </select>
            </div>
            <br />
            <div class="input-group input-group-newsletter">
              <input type="email" class="form-control" name="mail" placeholder="Enter email..."
                aria-label="Enter email..." aria-describedby="basic-addon">

              <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Get Newsletter!</button>
              </div>

            </div>
          </form>

          <div style="padding-top: 40px;">
            <h5>Powered by <a href="https://nairametrics.com" target="_blank">Nairametrics</a></h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="social-icons">
  <ul class="list-unstyled text-center mb-0">
    <li class="list-unstyled-item">
      <a href="https://twitter.com/intent/tweet?url=https://ssn.nairametrics.com&via=nigeriandata&text=Nairametrics">
        <i class="fab fa-twitter"></i>
      </a>
    </li>
    <li class="list-unstyled-item">
      <a href="https://www.facebook.com/sharer.php?u=https://ssn.nairametrics.com">
        <i class="fab fa-facebook-f"></i>
      </a>
    </li>
    <li class="list-unstyled-item">
      <a href="https://www.linkedin.com/sharing/share-offsite/?url=https://ssn.nairametrics.com">
        <i class="fab fa-linkedin-in"></i>
      </a>
    </li>
  </ul>
</div>

@endsection