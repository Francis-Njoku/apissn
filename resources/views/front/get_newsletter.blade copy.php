@extends('layouts.news')

@section('content')

<div class="overlay"></div>

<video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
  <source src="{{URL::asset('/asset/news_book/img/newsletter.jpg')}}" type="video/mp4">
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
          <h5 class="mb-2">Welcome to Nairametrics Stock Select Newsletter delivered to your inbox weekly. This
            newsletter is edited by Ugodre Obi-Chukwu, the founder of Nairametrics.</h5>
          <p><a class=" mb-1 text-white " href="/pricing" style="font-size: 15px;">Not a subscriber? Follow this link to subscribe.</a></p>

          <p class=" mb-1 text-xs" style="font-size: 15px;">
            As a subscriber, you also have complete access to all our newsletters including previous
            one that were published before you subscribe.</p>
            <p class="text-xs" style="font-size: 15px;">
            To get previous newsletters follow these simple steps;</p>
          <ol>
            <li>
              Click on the drop down menu to select any of the newsletters.
            </li>
            <li>
              You can only select one at a time.
            </li>
            <li>
              Enter the email address you registered with when you subscribed to our newsletter.
            </li>
            <li>
              Click on "Get Newsletter".
            </li>
            <li>
              Go to your inbox to receive the newsletter.
            </li>
          </ol>
          <p class=" mb-1 text-white " style="font-size:  15px;">
            Remember to seek further information from your financial adviser should you ever have doubts about what stock to buy, sell or hold.
 
            <br/>
          Best of Luck
          </p>
          <form method="POST" action="{{ route('send_deal_book') }}" accept-charset="UTF-8" id='contactForm'
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