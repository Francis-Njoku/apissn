@extends('layouts.forum')

@section('content')
<div class="container-fluid bg-gradient p-5">
	<div class="row m-auto text-center w-75">
		<div class="col-8">
			<div class="card-header d-flex flex-row align-items-center justify-content-between">
				<h6 class="m-0 font-weight-bold text-primary">Create Topic </h6>

			</div>
			<div class="card">
				<!-- Card Header - Dropdown -->

				<!-- Card Body -->

				<div class="card-body">
					@if(session('success2'))
					<div align="center" class="col-md-12 col-sm-12 col-xs-12">
						<div class="alert alert-success">
							{{session('success')}}
						</div>
					</div>
					@endif


					@if(session('error2'))
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div align="center" class="alert alert-danger">
							{{session('error')}}
						</div>
					</div>
					@endif
					<form method="POST" action="{{ route($get_route) }}" accept-charset="UTF-8" id='contactForm'
						class="form-horizontal" role="form">
						{{ csrf_field() }}
						<select name="category" id="" class="form-control mb-4" required>
							<option value="">Select Forum Category</option>
							@foreach($forums as $forum)
							<option value="{{$forum->id}}">{{$forum->name}}</option>
							@endforeach
						</select>
						<input type="text" name="topic" class=" form-control mb-4" placeholder="Topic" required />
						<textarea name="description" class=" form-control mb-4" id="editor" required>
						Description
					</textarea>
						<div align="center" class="form-group mt-3">
							<input type="submit" value="Create" class="btn btn-primary btn-lg" />
						</div>
					</form>

				</div>
			</div>
		</div>
		<div class="col-4"></div>
	</div>
</div>
@endsection