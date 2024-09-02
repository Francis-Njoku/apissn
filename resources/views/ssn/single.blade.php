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
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">

							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<a href="/ssn/{{$list->title}}">
									{{$list->title}}
								</a>
							</div>
							<div align="left" style="font-size: 10px;" class="text-xs font-weight-bold text-primary text-uppercase mb-1">

								{{date('D d M Y',strtotime($list->created_at))}}

							</div>
							<div align="left">
								{!! $list->body !!}
							</div>

							<form method="POST" action="{{ route('send_deal_book') }}" accept-charset="UTF-8" id='contactForm'
            					class="form-horizontal" role="form">
            					{{ csrf_field() }}
            	
            					<div class="input-group input-group-newsletter">
              					<input hidden type="text" class="form-control" name="name" value="{{$list->news_date}}"
                				aria-label="Enter email..." aria-describedby="basic-addon">
								
								<input hidden type="email" class="form-control" name="mail" value="{{$email_address}}"
                				aria-label="Enter email..." aria-describedby="basic-addon">

              					<div align="center" class="input-group-append">
                				<button align="center" class="btn btn-primary" type="submit">Get Copy Via Mail!</button>
              					</div>

            					</div>
          					</form>
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