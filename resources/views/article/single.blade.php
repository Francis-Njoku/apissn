@extends('layouts.article')

@section('content')
<div class="container-fluid bg-gradient p-1">

	<h1 align="center" class=" mb-5">{{ ucfirst($article_title)}}</h1>
	<div class="row m-auto text-center w-100">
		<div class=" col-xs-2 col-md-2">
		</div>
		@foreach($article as $list)
		<div class=" col-xs-8 col-md-8 mb-4">
			<div class="card @if($list->status == " approved") border-left-primary @else border-left-warning @endif
				shadow h-100 py-2">
				<div align="center">
					<a href="/article/{{$list->slug}}"><img src="/storage/featured_image/{{$list->featured_image}}"
							alt="{{ucwords($list->title)}}" class="img-fluid p-4"></a>
				</div>
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">

							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<a href="/article/{{$list->slug}}">
									{{$list->title}}
								</a>
							</div>
							<div align="left" style="font-size: 10px;" class="text-xs font-weight-bold text-primary text-uppercase mb-1">

								{{date('D d M Y',strtotime($list->created_at))}}

							</div>
							<div align="left">
								{!! $list->content !!}
							</div>
							<a href="/storage/doc/{{$list->story_doc}}" target="_blank" class=" mt-5 btn btn-lg btn-info">Download Article</a>
						</div>
						<!--<div class="col-auto">
					  <a href="/admin-newsletter/edit/{{$list->id}}"><i class="fas fa-pencil-alt fa-2x text-gray-300"></i></a>
					</div>-->
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div>

	<input type="hidden" name="hidden_page" id="hidden_page" value="1" />
	<input type="hidden" name="hidden_column_name" id="hidden_column_name" value="title" />
	<input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>
@endsection