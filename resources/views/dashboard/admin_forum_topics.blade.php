@extends('layouts.admin_search')

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
          <h6 class="m-0 font-weight-bold text-primary">Topic</h6>

        </div>
        <!-- Card Body -->

        <div class="card-body">

          <div class="row">
            <div class="col-md-2">
              <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <a href="/admin/forum/create-topic" class="btn btn-primary">
                  New <i class="fa fa-plus-circle"></i>
                </a>
              </div>
            </div>
            <div class="col-md-3">
              <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h3 class="h3 mb-0 text-gray-800">{{count($count_category)}} Categories</h3>
              </div>
            </div>
            <div class="col-md-3">
              <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h3 class="h3 mb-0 text-gray-800">{{count($count_topic)}} Topics</h3>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <input type="text" name="serach" id="serach" class="form-control" />
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="sorting" data-sorting_type="asc" data-column_name="title" style="cursor: pointer">
                    Title<span id="id_icon"></span></th>
                  <th>Category</th>  
                  <th>Reply Count</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @include('dashboard.admin_forum_topics_pagination_data')
              </tbody>
            </table>
            <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
            <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="title" />
            <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row">
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="POST" action="{{ route('admin-store-category') }}" accept-charset="UTF-8" id='contactForm'
            class="form-horizontal" role="form">
            {{ csrf_field() }}
            <div class="modal-body">
              <input type="text" name="category" class=" form-control mb-4" placeholder="Category" required />
              <textarea name="description" class="description form-control mb-4" id="iieditor" required>
                Description
              </textarea>
              <select name="status" id="uoo" class="status form-control mt-4 mb-4" required>
                <option value="">Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- Update -->
  <div class="row">
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="UpdateForumModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="UpdateForumModalLabel">Add category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="POST" action="{{ route('admin-update-category') }}" accept-charset="UTF-8" id='contactForm'
            class="form-horizontal" role="form">
            {{ csrf_field() }}
            <div class="modal-body">
              <input type="hidden" id="idi" name="id" class="idi form-control mb-4" placeholder="Category" required />

              <input value="title" type="text" id="title" name="title" class="category form-control mb-4" placeholder="Category" required />
              <textarea  name="description" id="description" class="descriptionid form-control mb-4" required>
                Description
              </textarea>
              <select name="status" id="" class="status form-control mt-4 mb-4" required>
                <option value="">Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Update</button>
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