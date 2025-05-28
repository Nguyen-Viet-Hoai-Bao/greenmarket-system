@extends('admin.admin_dashboard')
 @section('admin')
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
 <style>
  .form-check-label{
      text-transform: capitalize;
  }
</style>



<div class="page-content">
     <div class="container-fluid">
 
         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <h4 class="mb-sm-0 font-size-18">Vai Trò Trong Quyền Hạn</h4>
 
                     <div class="page-title-right">
                         <ol class="breadcrumb m-0">
                             <li class="breadcrumb-item"><a href="javascript: void(0);">Bảng thống kê</a></li>
                             <li class="breadcrumb-item active">Thêm Vai Trò Trong Quyền Hạn</li>
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
 
 <form id="myForm" action="{{ route('role.permission.store') }}" method="post" enctype="multipart/form-data">
     @csrf
     
 <div class="row">
     <div class="col-lg-12">
         <div>
             
             <div class="form-group mb-3">
                 <label for="example-text-input" class="form-label">Tên Vai Trò</label>
                 <select name="role_id" class="form-select">
                     <option selected disabled>Chọn Vai Trò</option>
                    @foreach ($roles as $role)  
                     <option value="{{ $role->id }}">{{ $role->name }}</option>
                     @endforeach
                 </select>
             </div>
 
             <div class="form-check mb-3">
                 <input class="form-check-input" type="checkbox" id="formCheck1">
                 <label class="form-check-label" for="formCheck1">
                    Quyền Hạn Tất Cả
                 </label>
             </div>   

             <hr>
              @foreach ($permission_groups as $group)
                <div class="row">
                  <div class="col-3">
                      <div class="form-check mb-3">
                          <input class="form-check-input" type="checkbox" id="flexCheckDefault">
                          <label class="form-check-label" for="flexCheckDefault">
                            {{ $group->group_name }}
                          </label>
                      </div>  
                  </div>
          
                  <div class="col-9">
                    @php
                        $permissions = App\Models\Admin::getpermissionByGroupName($group->group_name);
                    @endphp
            
                    @foreach ($permissions as $permission)
                      <div class="form-check mb-3">
                        <input class="form-check-input" name="permission[]" value="{{ $permission->id }}" type="checkbox" id="flexCheckDefault{{ $permission->id }}">
                        <label class="form-check-label" for="flexCheckDefault{{ $permission->id }}">
                          {{ $permission->name }}
                        </label>
                      </div>   
                    @endforeach   
                    <br>
                  </div> 
        
                </div> 
                {{-- //end row --}}
                  
              @endforeach   
 
             <div class="mt-4">
                 <button type="submit" class="btn btn-primary waves-effect waves-light">Lưu Thay Đổi</button>
             </div>
  
         </div>
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
 
  
  
 


 <script>
  $('#formCheck1').click(function(){
      if ($(this).is(':checked')) {
          $('input[ type=checkbox]').prop('checked',true)
      }else{
          $('input[ type=checkbox]').prop('checked',false)
      }
  })
</script>
 
@endsection