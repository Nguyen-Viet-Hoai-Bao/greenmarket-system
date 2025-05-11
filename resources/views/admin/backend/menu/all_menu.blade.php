@extends('admin.admin_dashboard')
@section('admin')


<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Tất cả Menu</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                        <a href="{{ route('add.menu') }}" 
                            class="btn btn-info waves-effect waves-light">Thêm Menu</a>
                      </ol>
                  </div>

              </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-body">

<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
    <thead>
    <tr>
        <th>STT</th>
        <th>Tên Menu</th>
        <th>Hình ảnh</th>
        <th>Hành động</th>
    </tr>
    </thead>


    <tbody>
      @foreach ($menu as $key=>$item)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $item->menu_name }}</td>
            <td>
              <img src="{{ asset($item->image) }}" 
                    alt=""
                    style="width: 70px; heigh:40px;"></td>
            <td>
              <a href="{{ route('edit.menu', $item->id) }}"
                  class="btn btn-info waves-effect waves-light">Chỉnh sửa</a>
              <a href="{{ route('delete.menu', $item->id) }}" 
                  class="btn btn-danger waves-effect waves-light" id="delete">Xóa</a>
            </td>
        </tr>
      @endforeach

    </tbody>
</table>

                  </div>
              </div>
          </div> <!-- end col -->
      </div> <!-- end row --> 

  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

@endsection