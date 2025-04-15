@extends('layouts.article')

@section('content')
<div class="container-fluid bg-gradient p-2">

	<h1 align="center" class=" mb-5">Stock Select Feed</h1>
	<div class="row m-auto text-center">
		<div class="col-md-12">
			<div class="form-group">
				<input type="text" class=" form-control-sm" placeholder="Search Feed" name="serach" id="serach" class="form-control" />
			</div>
		</div>
	</div>

	<div id="article_det">

	
	@include('ssn.ssn_pagination')
	</div>

	<input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="created_at" />
                  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc" />
</div>
@endsection