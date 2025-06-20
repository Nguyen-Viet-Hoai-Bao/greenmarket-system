@extends('admin.admin_dashboard')
@section('admin')
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
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="alert alert-primary">
                            <strong>Tổng doanh thu:</strong> {{ number_format($totalIncome, 0, ',', '.') }}đ
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
    <form id="myForm" action="{{ route('admin.coupon.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
        
        <div class="row">
            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="coupon_name" class="form-label">Tên Mã Giảm Giá</label>
                    <input class="form-control" type="text" name="coupon_name" id="coupon_name" value="">
                </div>
            </div>
            
            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="coupon_desc" class="form-label">Mô Tả Mã</label>
                    <input class="form-control" type="text" name="coupon_desc" id="coupon_desc" value="">
                </div>
            </div>
            
            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="discount" class="form-label">Giảm Giá (%)</label>
                    <input class="form-control" type="number" min="0" max="100" name="discount" id="discount" value="">
                </div>
            </div>

            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="validity" class="form-label">Ngày Hết Hạn</label>
                    <input class="form-control" type="date" name="validity" id="validity" value=""
                            min="{{ \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d') }}">
                </div>
            </div>

            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="quantity" class="form-label">Số Lượng</label>
                    <input class="form-control" type="number" min="1" name="quantity" id="quantity" value="">
                </div>
            </div>

            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="max_discount_amount" class="form-label">Giới Hạn Giảm Giá Tối Đa (VNĐ)</label>
                    <input class="form-control" type="number" min="0" name="max_discount_amount" id="max_discount_amount" value="">
                </div>
            </div>

            <div class="col-xl-6 col-md-6">
                <div class="form-group mb-6">
                    <label for="image" class="form-label">Ảnh Mã Giảm Giá</label>
                    <input class="form-control" type="file" name="image" id="image" accept="image/*">
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="mt-4">
              <button type="submit" class="btn btn-primary waves-effect waves-light">Lưu Thay Đổi</button>
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
            quantity: {
                required: true,
                min: 1,
                number: true,
            },
            discount: {
                required: true,
                number: true,
                min: 0,
                max: 100,
            },
            max_discount_amount: {
                required: true,
                number: true,
                min: 0,
            },
            validity: {
                required: true,
                date: true,
            },
          },
          messages :{
            coupon_name: {
                required : 'Vui lòng nhập tên mã giảm giá',
            }, 
            quantity: {
                required: 'Vui lòng nhập số lượng',
                min: 'Số lượng phải lớn hơn 0',
                number: 'Số lượng phải là số',
            },
            discount: {
                required: 'Vui lòng nhập phần trăm giảm giá',
                min: 'Giảm giá tối thiểu là 0%',
                max: 'Giảm giá tối đa là 100%',
                number: 'Giảm giá phải là số',
            },
            max_discount_amount: {
                required: 'Vui lòng nhập giới hạn giảm giá tối đa',
                min: 'Giá trị phải lớn hơn hoặc bằng 0',
                number: 'Giá trị phải là số',
            },
            validity: {
                required: 'Vui lòng chọn ngày hết hạn',
                date: 'Ngày không hợp lệ',
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
