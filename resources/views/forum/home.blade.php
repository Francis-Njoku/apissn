@extends('layouts.forum')

@section('content')
<div class="container-fluid bg-gradient">
	<div class="row m-auto text-center w-75">
		<div class="col-12 text-left mb-5">
			<a href="/forum/create-topic" class="btn btn-info btn-circle btn-lg text-left mt-4">
				<i class="fa fa-plus-circle"></i>
			</a>
		</div>
		<div class="col-md-8">
			<form class="search-form">
				<div class="input-group">
					<input id="input-1" class="form-control" type="text" aria-describedby="search-btn">
					<label for="input-1" class="sr-only">Search</label>
					<div class="input-group-append">
						<button id="search-btn" class="btn btn-primary">Search</button>
					</div>
				</div>
			</form>
			<ul class="topics-table">
				<li class="topics-header">
					<ul class="header-titles">
						<li>Category</li>
						<li>Topics</li>
						<li>Posts</li>
						<li>Freshness</li>
					</ul>
				</li>
				<li class="topics-body">
					@foreach($get_category as $cat)
					<ul class="topic-item-1">
						<li>
							<a class="badge badge-primary" href="">{{$cat->category}}</a>
							<p>{{$out = strlen($cat->description) > 40 ? substr($cat->description,0,40)."..." : $cat->description}}
							</p>
						</li>
						<li>{{$cat->topic_count}}</li>
						<li>{{$cat->reply_count}}</li>
						<li>

							<a class="badge badge-info" href="http://skatingbuzz.test/forums/topic/random-4/"
								title="Random 4">{{date('D d M Y',strtotime($cat->updated_at))}}</a>
							<p class="bbp-topic-meta">
								<!--<span class="bbp-topic-freshness-author">
	                                <a 
	                                href="http://skatingbuzz.test/forums/user/admin/" 
	                                title="View admin's profile" 
	                                class="bbp-author-name badge badge-info" 
	                                rel="nofollow">
	                                    admin
                                    </a>
	                            </span>-->
							</p>
						</li>
					</ul>
					@endForeach
				</li>
			</ul>
		</div>
		<div class="col-md-4">
			<div style="background-color: white;" class="cartd mb-5">
				
				<div class="card-body">
					<h4 class="card-title" style="margin-top: 20px;">Latest Topics</h4>
					<hr/>
					@foreach($get_trending_topic as $trending)
					<p class="card-text"><a
							href="/{{$trending->category}}/{{$trending->slug}}">{{ucwords($trending->title)}}</a></p>
					<hr/>		
					@endForeach
				</div>
			</div>

			<div style="background-color: white;" class="cartd">

				<div class="card-body">
					<h4 class="card-title">Trending Topics</h4>
					<hr/>
					@foreach($get_latest_topic as $latest)
					<p class="card-text"><a
							href="/{{$latest->category}}/{{$latest->slug}}">{{ucwords($latest->title)}}</a></p>
					<hr/>		
					@endForeach
				</div>
			</div>
		</div>
	</div>
</div>
@endsection