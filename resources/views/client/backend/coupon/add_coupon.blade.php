@extends('client.client_dashboard')
@section('client')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Thêm Mã Giảm Giá</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Bảng thống kê</a></li>
                          <li class="breadcrumb-item active">Thêm Mã Giảm Giá</li>
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
    <form id="myForm" action="{{ route('coupon.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
        
        <div class="row">
            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="example-text-input" class="form-label">Tên Mã Giảm Giá</label>
                    <input class="form-control" type="text" name="coupon_name" value="" id="example-text-input">
                </div>
            </div>
            
            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="example-text-input" class="form-label">Mô Tả Mã</label>
                    <input class="form-control" type="text" name="coupon_desc" value="" id="example-text-input">
                </div>
            </div>
            
            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="example-text-input" class="form-label">Giảm Giá (%)</label>
                    <input class="form-control" type="text" name="discount" value="" id="example-text-input">
                </div>
            </div>
            
            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="example-text-input" class="form-label">Ngày Hết Hạn</label>
                    <input class="form-control" type="date" name="validity" value="" id="example-text-input"
                            min="{{ Carbon\Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d') }}">
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
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

<script type="text/javascript">
  $(document).ready(function (){
      $('#myForm').validate({
          rules: {
            coupon_name: {
                  required : true,
              }, 
              
          },
          messages :{
            coupon_name: {
                required : 'Please Enter Coupon Name',
            }, 
               

          },
          errorElement : 'span', 
          errorPlacement: function (error,element) {
              error.addClass('invalid-feedback');
              element.closest('.form-group').append(error);
          },
          highlight : function(element, errorClass, validClass){
              $(element).addClass('is-invalid');
          },
          unhighlight : function(element, errorClass, validClass){
              $(element).removeClass('is-invalid');
          },
      });
  });
  
</script>

@endsection