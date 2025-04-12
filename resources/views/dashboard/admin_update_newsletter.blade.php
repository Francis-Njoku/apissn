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
                <h6 class="m-0 font-weight-bold text-primary">Update Newsletter</h6>
                
              </div>
              <!-- Card Body -->

              <div class="card-body">
                <form method="POST" enctype="multipart/form-data" action="{{ route('update_newsletter') }}" accept-charset="UTF-8" id='contactForm' class="form-horizontal"  role="form">
                  {{ csrf_field() }}
                  @foreach($news as $letter)
                  <input type="hidden" value="{{$letter->id}}" name="id" required/>
                <select name="news_type" id="" class="form-control mb-4" required>
                  <option value="">Select Newsletter Type</option>
                  @foreach($news_type as $new)
                <option value="{{$new->id}}">{{$new->news}}</option>
                  @endforeach
                </select>
                </select>
              <input type="date" name="news_date" value="{{$letter->news_date}}" class=" form-control mb-4" placeholder="Date" required/>

              <input type="text" name="title" value="{{$letter->title}}" class=" form-control mb-4" placeholder="Title" required/>
              <input type="text" name="name" value="{{$letter->caption}}" class=" form-control mb-4" placeholder="Caption" required/>
                <textarea name="body" class=" form-control mb-4" id="article-editor" required>
                  {{$letter->body}}
                </textarea>
                <select name="status" id="" class="form-control mt-4 mb-4" required>
                  <option value="">Status</option>
                  <option value="approved">Approved</option>
                  <option value="pending">Pending</option>
                </select>
                @endforeach
                <div align="center" class="form-group mt-3">
                  <input type="submit" value="Update" class="btn btn-primary btn-lg"/>
                </div>
                </form>

              </div>
            </div>
          </div>

        </div>


      </div>
      <!-- /.container-fluid -->

    </div>

    <!-- End of Main Content -->
@endsection