@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Sửa Quản Trị Viên</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Bảng thống kê</a></li>
                            <li class="breadcrumb-item active">Sửa Quản Trị Viên</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-12 col-lg-12"> 
<div class="card">
<div class="card-body p-4">

<form id="myForm" action="{{ route('admin.update',$admin->id) }}" method="post" enctype="multipart/form-data">
    @csrf

    
    
<div class="row">
  
  
<div class="col-xl-6 col-md-6"> 
    <div class="form-group mb-3">
        <label for="example-text-input" class="form-label">Tên Quản Trị Viên</label>
        <input class="form-control" type="text" name="name"  id="example-text-input" value="{{ $admin->name }}">
    </div> 
</div>

<div class="col-xl-6 col-md-6"> 
    <div class="form-group mb-3">
        <label for="example-text-input" class="form-label">Email Quản Trị Viên</label>
        <input class="form-control" type="email" name="email"  id="example-text-input"  value="{{ $admin->email }}">
    </div> 
</div>

<div class="col-xl-6 col-md-6"> 
    <div class="form-group mb-3">
        <label for="example-text-input" class="form-label">SĐT Quản Trị Viên </label>
        <input class="form-control" type="text" name="phone"  id="example-text-input"  value="{{ $admin->phone }}">
    </div> 
</div>

<div class="col-xl-6 col-md-6"> 
    <div class="form-group mb-3">
        <label for="example-text-input" class="form-label">Địa Chỉ Quản Trị Viên</label>
        <input class="form-control" type="text" name="address"  id="example-text-input"  value="{{ $admin->address }}">
    </div> 
</div>


<div class="col-xl-6 col-md-6"> 
    <div class="form-group mb-3">
        <label for="example-text-input" class="form-label">Chức Vụ</label>
        <select name="roles" class="form-select">
            <option>Chọn</option>
            @foreach ($roles as $role)
            <option value="{{ $role->id }}" {{ $admin->hasRole($role->name) ? 'selected' : '' }} >{{ $role->name }}</option>
            @endforeach 
        </select>
    </div> 
</div>


<div class="mt-4">
    <button type="submit" class="btn btn-primary waves-effect waves-light">Lưu Thay Đổi</button>
</div>
  
</div>
</form>
</div>
</div>









              
                <!-- end tab content -->
            </div>
            <!-- end col -->

            
            <!-- end col -->
        </div>
        <!-- end row -->
        
    </div> <!-- container-fluid -->
</div>



@endsection