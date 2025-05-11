@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Chỉnh Sửa Quyền</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                          <li class="breadcrumb-item active">Chỉnh Sửa Quyền</li>
                      </ol>
                  </div>

              </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row">
          <div class="col-xl-9 col-lg-8">

<div class="card">
  <div class="card-body p-4">
    <form id="myForm" action="{{ route('permission.update') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="id" value="{{ $permission->id }}">

    <div class="row">
          <div class="col-lg-6">
              <div>
                  <div class="form-group mb-6">
                      <label for="example-text-input" class="form-label">Tên Quyền</label>
                      <input class="form-control" type="text" name="name" value="{{ $permission->name }}" id="example-text-input">
                  </div>
              </div>
          </div>

          <div class="col-lg-6">
            <div class="form-group mb-6">
              <label for="example-text-input" class="form-label">Nhóm Quyền</label>
              <select name="group_name" id="" class="form-select">
                <option value="Category" {{ $permission->group_name == 'Category' ? 'selected' : '' }}>Danh Mục</option>
                <option value="City" {{ $permission->group_name == 'City' ? 'selected' : '' }}>Thành Phố</option>
                <option value="Product" {{ $permission->group_name == 'Product' ? 'selected' : '' }}>Sản Phẩm</option>
                <option value="Market" {{ $permission->group_name == 'Market' ? 'selected' : '' }}>Chợ</option>
                <option value="Banner" {{ $permission->group_name == 'Banner' ? 'selected' : '' }}>Quảng Cáo</option>
                <option value="Order" {{ $permission->group_name == 'Order' ? 'selected' : '' }}>Đơn Hàng</option>
                <option value="Reports" {{ $permission->group_name == 'Reports' ? 'selected' : '' }}>Báo Cáo</option>
                <option value="Review" {{ $permission->group_name == 'Review' ? 'selected' : '' }}>Đánh Giá</option>
              </select>
            </div> </br>
            
            <div class="mt-4">
              <button type="submit" class="btn btn-primary waves-effect waves-light">Lưu Thay Đổi</button>
            </div>
            
          </div>
      </div>
    </form>
  </div>            

                <!-- end tab content -->
            </div>
            <!-- end col -->

            <!-- end col -->
        </div>
        <!-- end row -->
        
    </div> <!-- container-fluid -->
  </div>
</div>

@endsection