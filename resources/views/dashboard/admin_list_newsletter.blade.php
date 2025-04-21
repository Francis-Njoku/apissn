@extends('layouts.admin')

@section('content')

      <!-- Begin Page Content -->
      <div class="container-fluid">

        <!-- Content Row -->
        <div class="row">

          <div class="col-md-12 mb-5">
            <a href="/admin-stock-add"><i class="fas fa-plus-circle fa-2x text-black-50"></i></a>
          </div>

          <!-- News List -->
          @foreach($get_list as $list)
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card @if($list->status == "approved")
              border-left-primary
              @else
              border-left-warning
              @endif shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                      {{date('D d M Y',strtotime($list->news_date))}} - {{$list->news}}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                      <a href="/admin-newsletter/{{$list->id}}">
                      {{$list->title}}
                      </a>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a href="/admin-newsletter/edit/{{$list->id}}"><i class="fas fa-pencil-alt fa-2x text-gray-300"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        {{$get_list->links()}}

        <!-- Content Row -->
      </div>
      <!-- /.container-fluid -->

    </div>

    <!-- End of Main Content -->
@endsection