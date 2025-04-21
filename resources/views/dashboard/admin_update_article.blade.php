@extends('layouts.admin')

@section('content')

<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Content Row -->

  <div class="row">

    <!-- Area Chart -->
    <div class="col-xl-21 col-lg-12">
      <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Update Article</h6>

        </div>
        <!-- Card Body -->

        <div class="card-body">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <form method="POST" enctype="multipart/form-data" action="{{ route('update-article') }}" accept-charset="UTF-8" id='contactForm'
              class="form-horizontal" role="form">
              {{ csrf_field() }}
              @foreach($articles as $article)
              <input type="hidden" value="{{$article->id}}" name="id" required />
              <p>Title</p>
              <input type="text" value="{{$article->title}}" class=" form-control" name="title" required />
              <br/>
              <img src="/storage/featured_image/{{$article->featured_image}}" alt="{{$article->title}}"
                class=" img-thumbnail">
                <br/>
              <input class="form-control" type="file" value="{{$article->featured_image}}" name="featured"
                 />
                <br/>
              <textarea name="content" class=" form-control mb-4" id="article-editor" required>
                  {{$article->content}}
                </textarea>
                <br/>
              <a href="/storage/doc/{{$article->story_doc}}" target=" _blank" class="btn btn-primary">View PDF</a> 
              <p>Upload PDF</p>
              <input class="form-control" type="file" value="{{$article->featured_image}}" name="story_doc" />

              <select name="status" id="" class="form-control mt-4 mb-4" required>
                <option value="{{$article->status}}">{{ucfirst($article->status) }}</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
              </select>
              @endforeach
              <div align="center" class="form-group mt-3">
                <input type="submit" value="Update" class="btn btn-primary btn-lg" />
              </div>
            </form>
          </div>
          <div class="col-md-2"></div>


        </div>
      </div>
    </div>

  </div>


</div>
<!-- /.container-fluid -->

</div>

<!-- End of Main Content -->
@endsection