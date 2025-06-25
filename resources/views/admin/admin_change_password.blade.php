@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Đổi Mật Khẩu</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Liên Hệ</a></li>
                          <li class="breadcrumb-item active">Đổi Mật Khẩu</li>
                      </ol>
                  </div>

              </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row">
          <div class="col-xl-9 col-lg-8">
  <div class="card">
      <div class="card-body">
          <div class="row">
              <div class="col-sm order-2 order-sm-1">
                  <div class="d-flex align-items-start mt-3 mt-sm-0">
                      <div class="flex-shrink-0">
                          <div class="avatar-xl me-3">
                              <img src="{{ (!empty($profileData->photo)) 
                                        ? url($profileData->photo)
                                        : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png')}}" 
                                    alt="" class="img-fluid rounded-circle d-block">
                          </div>
                      </div>
                      <div class="flex-grow-1">
                          <div>
                              <h5 class="font-size-16 mb-1">{{ $profileData->name }}</h5>
                              <p class="text-muted font-size-13">{{ $profileData->email }}</p>

                              <div class="d-flex flex-wrap align-items-start gap-2 gap-lg-3 text-muted font-size-13">
                                  <div><i class="mdi mdi-circle-medium me-1 text-success align-middle"></i>{{ $profileData->phone }}</div>
                                  <div><i class="mdi mdi-circle-medium me-1 text-success align-middle"></i>{{ $profileData->address }}</div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-sm-auto order-1 order-sm-2">
                  <div class="d-flex align-items-start justify-content-end gap-2">
                      <div>
                          <button type="button" class="btn btn-soft-light"><i class="me-1"></i> Tin Nhắn</button>
                      </div>
                  </div>
              </div>
          </div>

      </div>
      <!-- end card body -->
  </div>
              <!-- end card -->

  <div class="card-body p-4">
  <form action="{{ route('admin.password.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

  <div class="row">
        <div class="col-lg-6">
            <div>
                <div class="mb-3">
                    <label for="example-text-input" class="form-label">Mật Khẩu Cũ</label>
                    <input class="form-control @error('old_password') is-invalid @enderror" 
                          type="password" name="old_password" id="old_password">
                    @error('old_password')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="mb-3">
                  <label for="example-text-input" class="form-label">Mật Khẩu Mới</label>
                  <input class="form-control @error('new_password') is-invalid @enderror" 
                        type="password" name="new_password" id="new_password">
                  @error('new_password')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <label for="example-text-input" class="form-label">Xác Nhận Mật Khẩu Mới</label>
                  <input class="form-control" 
                    type="password" name="new_password_confirmation" id="new_password_confirmation">
                </div>
                  
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

@endsection