@extends('client.client_dashboard')
@section('client')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Đánh Giá Đang Chờ Xem Duyệt</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    
                    <div class="card-body">
                        <div style="overflow-x: auto">    
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Người Dùng</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Cửa Hàng</th>
                                        <th>Bình Luận</th>
                                        <th>Đánh Giá</th>                
                                        <th>Trạng Thái</th>  
                                        <th>Hành Động</th> 
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($allreviews as $key => $item)  
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item['user']['name'] }}</td>
                                        <td>{{ $item['product']['productTemplate']['name'] }}</td>
                                        <td>{{ $item['client']['name'] }}</td>
                                        <td>{{ Str::limit($item->comment, 50, '...') }}</td>
                                        <td>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bx bxs-star {{ $i <= $item->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                            @endfor 
                                        </td> 
                                        <td> 
                                            @if ($item->status == 1)
                                                <span class="text-success"><b>Đang Hoạt Động</b></span>
                                            @else
                                                <span class="text-danger"><b>Không Hoạt Động</b></span>
                                            @endif
                                        </td>

                                        <td>
                                            <button class="btn btn-danger btn-sm btn-report-review" data-review-id="{{ $item->id }}">
                                                Yêu cầu ẩn
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row --> 
        
    </div> <!-- container-fluid -->
</div>

<!-- Modal nhập lý do ẩn đánh giá -->
<div class="modal fade" id="reportReviewModal" tabindex="-1" aria-labelledby="reportReviewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="reportReviewForm">
      @csrf
      <input type="hidden" name="review_id" id="modalReviewId" value="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reportReviewModalLabel">Yêu cầu ẩn đánh giá</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="reason" class="form-label">Lý do ẩn đánh giá</label>
            <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
          </div>
          <div id="reportStatus"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
    let reportModal = new bootstrap.Modal(document.getElementById('reportReviewModal'));

    $('.btn-report-review').on('click', function() {
        const reviewId = $(this).data('review-id');
        $('#modalReviewId').val(reviewId);
        $('#reason').val('');
        $('#reportStatus').html('');
        reportModal.show();
    });

    $('#reportReviewForm').on('submit', function(e) {
        e.preventDefault();
        const reviewId = $('#modalReviewId').val();
        const reason = $('#reason').val();
        const token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('reviews.product.report') }}",
            method: 'POST',
            data: {
                _token: token,
                review_id: reviewId,
                reason: reason
            },
            success: function(response) {
                $('#reportStatus').html('<div class="alert alert-success">Yêu cầu đã gửi đến admin.</div>');
                setTimeout(() => {
                    reportModal.hide();
                }, 1500);
            },
            error: function(xhr) {
                let message = 'Có lỗi xảy ra, vui lòng thử lại.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $('#reportStatus').html(`<div class="alert alert-danger">${message}</div>`);
            }
        });
    });
});
</script>

@endsection
