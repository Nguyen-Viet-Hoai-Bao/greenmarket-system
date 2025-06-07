@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Chỉnh sửa mã giảm giá</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                          <li class="breadcrumb-item active">Chỉnh sửa mã giảm giá</li>
                      </ol>
                  </div>
              </div>
              
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="alert alert-primary">
                            <strong>Tổng Thu:</strong> {{ number_format($totalIncome, 0, ',', '.') }}đ
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-danger">
                            <strong>Tổng Chi:</strong> {{ number_format($totalExpense, 0, ',', '.') }}đ
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-success">
                            <strong>Số Dư Hiện Tại:</strong> {{ number_format($balance, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row">
          <div class="col-xl-12 col-lg-12">

<div class="card">
  <div class="card-body p-4">
    <form id="myForm" action="{{ route('admin.coupon.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $coupon->id }}">
        <div class="row">
            
            <div class="col-xl-6">
            <div class="form-group mb-3">
                <label class="form-label">Tên mã giảm giá</label>
                <input class="form-control" type="text" name="coupon_name" value="{{ $coupon->coupon_name }}">
            </div>
            </div>

            <div class="col-xl-6">
            <div class="form-group mb-3">
                <label class="form-label">Mô tả mã giảm giá</label>
                <input class="form-control" type="text" name="coupon_desc" value="{{ $coupon->coupon_desc }}">
            </div>
            </div>

            <div class="col-xl-4">
            <div class="form-group mb-3">
                <label class="form-label">Giá trị giảm (%)</label>
                <input class="form-control" type="number" name="discount" value="{{ $coupon->discount }}">
            </div>
            </div>

            <div class="col-xl-4">
            <div class="form-group mb-3">
                <label class="form-label">Ngày hết hạn</label>
                <input class="form-control" type="date" name="validity" value="{{ $coupon->validity }}" min="{{ \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d') }}">
            </div>
            </div>

            <div class="col-xl-4">
            <div class="form-group mb-3">
                <label class="form-label">Số lượng</label>
                <input class="form-control" type="number" name="quantity" value="{{ $coupon->quantity }}">
            </div>
            </div>

            <div class="col-xl-6">
            <div class="form-group mb-3">
                <label class="form-label">Số tiền giảm tối đa (VNĐ)</label>
                <input class="form-control" type="number" name="max_discount_amount" value="{{ $coupon->max_discount_amount }}">
            </div>
            </div>

            <div class="col-xl-6">
            <div class="form-group mb-3">
                <label class="form-label">Loại khách</label>
                <select class="form-control" name="client_id">
                <option value="0" {{ $coupon->client_id == 0 ? 'selected' : '' }}>Tất cả khách</option>
                <option value="1" {{ $coupon->client_id == 1 ? 'selected' : '' }}>Khách hàng mới</option>
                <option value="2" {{ $coupon->client_id == 2 ? 'selected' : '' }}>Khách hàng thân thiết</option>
                </select>
            </div>
            </div>

            <div class="col-xl-6">
            <div class="form-group mb-3">
                <label class="form-label">Hình ảnh mã giảm giá</label>
                <input class="form-control" type="file" name="image">
                @if($coupon->image_path)
                <img src="{{ asset($coupon->image_path) }}" class="mt-2" width="100" alt="Image">
                @endif
            </div>
            </div>

            <div class="col-lg-12">
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
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