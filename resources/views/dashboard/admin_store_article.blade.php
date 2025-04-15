@extends('layouts.admin')

@section('content')

<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Content Row -->
  <div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{count($users)}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-calendar fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- total articles -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Articles</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{count($all_articles)}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-money-bill fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Approved Articles</div>
              <div class="row no-gutters align-items-center">
                <div class="col-auto">
                  <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{count($all_articles)}}</div>
                </div>

              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Subscribers</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format(count($total_approved))}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-comments fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Content Row -->

  <div class="row">

    <!-- Area Chart -->
    <div class="col-xl-21 col-lg-12">
      <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Post Article</h6>

        </div>
        <!-- Card Body -->

        <div class="card-body">
          <form enctype="multipart/form-data" method="POST" action="{{ route('store-article') }}" accept-charset="UTF-8" id='contactForm'
            class="form-horizontal" role="form">
            {{ csrf_field() }}
            <input type="text" name="title" class=" form-control mb-4" placeholder="Title" required />
            <textarea name="content" class=" form-control mb-4" id="article-editor" required>
              Content
            </textarea>
            <p>Featured Image</p>
            <input type="file" accept=".jpg, .jpeg, .png" name="featured" class=" form-control mb-4" placeholder="Featured Image" required />
            <p>Upload PDF</p>
            <input type="file" accept="image/*,.pdf" name="story_doc" class=" form-control mb-4" placeholder="story document" required />

            <select name="status" id="" class="form-control mt-4 mb-4" required>
              <option value="">Status</option>
              <option value="approved">Approved</option>
              <option value="pending">Pending</option>
            </select>
            <div align="center" class="form-group mt-3">
              <input type="submit" value="Upload" class="btn btn-primary btn-lg" />
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