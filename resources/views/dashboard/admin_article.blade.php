@extends('layouts.admin')

@section('content')

<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Content Row -->

  <div class="row">

    <!-- Area Chart -->
    <div class="col-xl-21 col-lg-12">
      <div class="card shadow mb-4">
        @foreach($article as $art)
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ucwords($art->title)}}</h6>

        </div>
        <!-- Card Body -->

        <div class="card-body">
          <h1 align="center">{{ucwords($art->title)}}</h1>

          <div align="center">
        <img src="/storage/featured_image/{{$art->featured_image}}" alt="{{ucwords($art->title)}}" 
        class="img-fluid p-4">
          </div>
          <div class=" text-justify p-4">
        {!! $art->content !!}
          </div>
        <br/>
        <div align="center"><a href="/storage/doc/{{$art->story_doc}}" target="_blank" class="btn btn-info">Download Article</a></div>
        </div>

        @endForeach
      </div>
    </div>

  </div>


</div>
<!-- /.container-fluid -->

</div>

<!-- End of Main Content -->
@endsection