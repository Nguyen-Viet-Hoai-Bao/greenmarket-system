@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Nhập Quyền</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item active">
                            <a href="{{ route('export') }}" 
                              class="btn btn-danger waves-effect waves-light">Xuất</a>
                            </ol>
                          </li>
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
    <form id="myForm" action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
      @csrf

    <div class="row">
          <div class="col-lg-6">
              <div>
                  <div class="form-group mb-6">
                      <label for="example-text-input" class="form-label">Tệp Xlsx Nhập Vào</label>
                      <input class="form-control" type="file" name="import_file" value="" id="example-text-input">
                  </div>
              </div>
          </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-primary waves-effect waves-light">Tải Lên</button>
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