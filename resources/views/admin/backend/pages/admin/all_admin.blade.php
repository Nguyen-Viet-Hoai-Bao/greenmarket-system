@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Tất Cả Quản Trị Viên</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <a href="{{ route('add.admin') }}" class="btn btn-primary waves-effect waves-light">Thêm Quản Trị Viên</a>
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
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Số Điện Thoại</th>
                <th>Chức Vụ</th>
                <th>Hành Động</th> 
            </tr>
            </thead>


            <tbody>
          @foreach ($alladmin as $key=> $item)  
            <tr>
                <td>{{ $key+1 }}</td>
                <td><img src="{{ (!empty($item->photo)) ? url($item->photo) : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" alt="" style="width: 70px; height:40px;"></td>              
                <td>{{ $item->name }}</td> 
                <td>{{ $item->email }}</td> 
                <td>{{ $item->phone }}</td> 
                <td>
                  @foreach ($item->roles as $role)
                      <span class="badge badge-pill bg-danger">{{ $role->name }}</span>
                  @endforeach    
                </td> 
                <td><a href="{{ route('edit.admin',$item->id) }}" class="btn btn-info waves-effect waves-light">Sửa</a>
                <a href="{{ route('delete.admin',$item->id) }}" class="btn btn-danger waves-effect waves-light" id="delete">Xóa</a>
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



@endsection