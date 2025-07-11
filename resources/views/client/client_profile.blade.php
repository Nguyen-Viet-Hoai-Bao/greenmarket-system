@extends('client.client_dashboard')
@section('client')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Hồ sơ</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Liên hệ</a></li>
                          <li class="breadcrumb-item active">Hồ sơ</li>
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
                                  <div><i class="mdi mdi-circle-medium me-1 text-success align-middle"></i>{{ $profileData->full_address }}</div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-sm-auto order-1 order-sm-2">
                  <div class="d-flex align-items-start justify-content-end gap-2">
                      <div>
                          <button type="button" class="btn btn-soft-light"><i class="me-1"></i> Nhắn tin</button>
                      </div>
                  </div>
              </div>
          </div>

      </div>
      <!-- end card body -->
  </div>
              <!-- end card -->

  <div class="card-body p-4">
  <form action="{{ route('client.profile.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

  <div class="row">
        <div class="col-lg-6">
            <div>
                <div class="mb-3">
                    <label for="example-text-input" class="form-label">Họ tên</label>
                    <input class="form-control" type="text" name="name" value="{{ $profileData->name }}" id="example-text-input">
                </div>
                
                <div class="mb-3">
                  <label for="example-text-input" class="form-label">Email</label>
                  <input class="form-control" type="text" name="email" value="{{ $profileData->email }}" id="example-text-input">
                </div>
                
                <div class="mb-3">
                  <label for="example-text-input" class="form-label">Số điện thoại</label>
                  <input class="form-control" type="text" name="phone" value="{{ $profileData->phone }}" id="example-text-input">
                </div>
                
                {{-- <div class="mb-3">
                    <label for="example-text-input" class="form-label">City</label>
                    <input class="form-control" type="text" 
                            value="{{ $profileData->city?->city_name ?? 'N/A' }}" 
                            id="example-text-input" readonly>
                </div> --}}
                
                <div class="mb-3">
                    <label for="city" class="form-label">Tỉnh / Thành phố</label>
                    <select class="form-select" id="city" name="city_id">
                        <option value="">-- Chọn Tỉnh / Thành phố --</option>
                        @foreach ($city as $cit)
                            <option value="{{ $cit->id }}" {{ $cit->id == $profileData->ward?->district?->city_id ? 'selected' : '' }}>
                                {{ $cit->city_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                <label for="district" class="form-label">Quận / Huyện</label>
                <select class="form-select" id="district" name="district_id">
                    <option value="">-- Chọn Quận / Huyện --</option>
                </select>
                </div>
                
                <div class="mb-3">
                <label for="ward" class="form-label">Ward</label>
                <select class="form-select" id="ward" name="ward_id">
                    <option value="">-- Chọn Phường / Xã --</option>
                </select>
                </div>
                         
                
                <div class="mb-3">
                    <label for="example-text-input" class="form-label">Thông tin gian hàng</label>
                    <textarea name="shop_info" 
                            id="basicpill-address-input" 
                            rows="2"
                            class="form-control"
                            placeholder="Enter Market Infor">{{ $profileData->shop_info }}</textarea>
                </div>
                
                <div class="mb-3">
                    <label for="example-text-input" class="form-label">Ảnh bìa</label>
                    <input class="form-control" type="file" name="cover_photo" id="image">
                </div>
                
                <div class="mb-3">
                    <img id="showImage"
                        src="{{ (!empty($profileData->cover_photo)) 
                            ? url($profileData->cover_photo)
                            : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png')}}" 
                        alt="" class="p-1 bg-primary" width="210" height="100">
                </div>

            </div>
        </div>

        <div class="col-lg-6">
          <div class="mb-3">
            <label for="example-text-input" class="form-label">Địa chỉ</label>
            <input class="form-control" type="text" name="address" value="{{ $profileData->address }}" id="example-text-input">
          </div>
          
          <div class="mb-3">
            <label for="example-text-input" class="form-label">Ảnh đại diện</label>
            <input class="form-control" type="file" name="photo" id="image">
          </div>
          
          <div class="mb-3">
            <img id="showImage"
                src="{{ (!empty($profileData->photo)) 
                      ? url($profileData->photo)
                      : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png')}}" 
                  alt="" class="rounded-circle p-1 bg-primary" width="110">
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary waves-effect waves-light">Lưu thay đổi</button>
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

<script type="text/javascript">
    $(document).ready(function(){
        $('#image').change(function (e) {
            var reader = new FileReader();
            reader.onload = function(e){
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        })
    })

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#city').on('change', function () {
        var city_id = $(this).val();
        $('#district').html('<option>Loading...</option>');
        $('#ward').html('<option>-- Select Ward --</option>');
        if (city_id) {
            $.ajax({
                url: "{{ url('/client/district/ajax') }}/" + city_id,
                type: "GET",
                success: function (data) {
                    $('#district').html('<option>-- Select District --</option>');
                    $.each(data, function (key, value) {
                        $('#district').append('<option value="' + value.id + '">' + value.district_name + '</option>');
                    });
                }
            });
        }
    });

    $('#district').on('change', function () {
        var district_id = $(this).val();
        $('#ward').html('<option>Loading...</option>');
        if (district_id) {
            $.ajax({
                url: "{{ url('/client/ward/ajax') }}/" + district_id,
                type: "GET",
                success: function (data) {
                    $('#ward').html('<option>-- Select Ward --</option>');
                    $.each(data, function (key, value) {
                        $('#ward').append('<option value="' + value.id + '">' + value.ward_name + '</option>');
                    });
                }
            });
        }
    });
});
</script>

@endsection