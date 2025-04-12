<div class="row m-auto text-center w-100">

	@foreach($article as $list)
	<div class="col-xl-4 col-md-6 mb-4">
		<div class="card @if($list->status == " approved") border-left-primary @else border-left-warning @endif shadow
			h-100 py-2">
			<!--<div align="center">
				<a href="/article/{{$list->slug}}"><img src="/storage/featured_image/{{$list->featured_image}}"
						alt="{{ucwords($list->title)}}" class="img-fluid p-4"></a>
			</div>-->
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col mr-2">
						<div align="left" class="text-xs font-weight-bold text-primary text-uppercase mb-1">
							<a style="font-size: 12px; color:red" href="/article/{{$list->id}}">
								{{date('D d M Y',strtotime($list->created_at))}}
							</a>
						</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800">
							<a href="/article/{{$list->id}}">
								{{$list->title}}
							</a>
						</div>
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
{!! $article->links() !!}