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
                <h6 class="m-0 font-weight-bold text-primary">Subscribers</h6>
                
              </div>
              <!-- Card Body -->

              <div class="card-body">

                <div class="row">
                  <div class="col-md-3">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                      <h3 class="h3 mb-0 text-gray-800">{{count($active)}} Active Subscribers</h3>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                      <h3 class="h3 mb-0 text-gray-800">{{count($inactive)}} Inactive Subscribers</h3>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                      <h3 class="h3 mb-0 text-gray-800"><a class=" btn btn-danger" href="/admin/delist">Delist</a></h3>
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
                     <th class="sorting" data-sorting_type="asc" data-column_name="email" style="cursor: pointer">Email<span id="id_icon"></span></th>
                     <th class="sorting" data-sorting_type="asc" data-column_name="order_id" style="cursor: pointer">Order ID <span id="post1_icon"></span></th>
                     <th class="sorting" data-sorting_type="asc" data-column_name="amount" style="cursor: pointer">Amount <span id="post2_icon"></span></th>
                     <th>Type</th>
                     <th class="sorting" data-sorting_type="asc" data-column_name="plan" style="cursor: pointer">Period<span id="post3_icon"></span></th>
                     <th class="sorting" data-sorting_type="asc" data-column_name="status" style="cursor: pointer">Status<span id="post4_icon"></span></th>
                     <th >Due Date</th>
                     <th >Payment Date</th>
                    </tr>
                   </thead>
                   <tbody>
                    @include('dashboard.subscriber_pagination_data')
                   </tbody>
                  </table>
                  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="email" />
                  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
                 </div>
              </div>
            </div>
          </div>

        </div>


      </div>
      <!-- /.container-fluid -->

    </div>

    <!-- End of Main Content -->
@endsection