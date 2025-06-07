<!doctype html>
<html lang="en">
   <head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="Askbootstrap">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <meta name="author" content="Askbootstrap">
      <title>User Dashboard</title>
      <!-- Favicon Icon -->
      <link rel="icon" type="image/png" href="img/favicon.png">
      <!-- Bootstrap core CSS-->
      <link href="{{ asset('frontend/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
      <!-- Font Awesome-->
      <link href="{{ asset('frontend/vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
      <!-- Font Awesome-->
      <link href="{{ asset('frontend/vendor/icofont/icofont.min.css') }}" rel="stylesheet">

      <!-- Icons Css -->
      <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

      <!-- Select2 CSS-->
      <link href="{{ asset('frontend/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
      <!-- Custom styles for this template-->
      <link href="{{ asset('frontend/css/osahan.css') }}" rel="stylesheet">
      
      <link rel="stylesheet" href="{{ asset('frontend/vendor/owl-carousel/owl.carousel.css') }}">
      <link rel="stylesheet" href="{{ asset('frontend/vendor/owl-carousel/owl.theme.css') }}">

      <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
     
      <style>
        .content-wrapper {
            margin-top: 90px;
        }
      </style>
   </head>
   <body>

    @include('frontend.dashboard.header')
    
    <main class="content-wrapper">
        @yield('dashboard')
    </main>

    @include('frontend.dashboard.footer')

    
<!-- jQuery -->
<script src="{{ asset('frontend/vendor/jquery/jquery-3.3.1.slim.min.js') }}"></script>
<!-- Bootstrap core JavaScript-->
<script src="{{ asset('frontend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- Select2 JavaScript-->
<script src="{{ asset('frontend/vendor/select2/js/select2.min.js') }}"></script>

<!-- Owl Carousel -->
<script src="{{ asset('frontend/vendor/owl-carousel/owl.carousel.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('frontend/js/custom.js') }}"></script>
<script src="{{ asset('frontend/js/cart.js') }}"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
  @if(Session::has('message'))
  var type = "{{ Session::get('alert-type','info') }}"
  switch(type){
      case 'info':
      toastr.info(" {{ Session::get('message') }} ");
      break;

      case 'success':
      toastr.success(" {{ Session::get('message') }} ");
      break;

      case 'warning':
      toastr.warning(" {{ Session::get('message') }} ");
      break;

      case 'error':
      toastr.error(" {{ Session::get('message') }} ");
      break; 
  }
  @endif 
</script>

<script type="text/javascript">
  $.ajaxSetup({
     headers:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                       .attr('content')
     }
  });
</script>

{{-- Apply Coupon --}}
<script>
   function ApplyCoupon() {
      var coupon_name = $('#coupon_name').val();
      $.ajax({
         type: "POST",
         dataType: "json",
         data: {coupon_name:coupon_name},
         url: "/apply-coupon",
         success:function(data){

            // Message
            const Toast = Swal.mixin({
               toast: true,
               position: 'top-end',
               showConfirmButton: false,
               timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
               Toast.fire({
               type: 'success',
               icon: 'success', 
               title: data.success, 
               });
               location.reload();
            }else{
               Toast.fire({
                  type: 'error',
                  icon: 'error', 
                  title: data.error, 
               })
            }
            // End Message  
         }
      })
   }
</script>


{{-- Remove Coupon --}}
<script>
   function CouponRemove() {
      $.ajax({
         type: "GET",
         dataType: "json",
         url: "/remove-coupon",
         success:function(data){
            // Message
            const Toast = Swal.mixin({
               toast: true,
               position: 'top-end',
               showConfirmButton: false,
               timer: 3000 
            })
            if ($.isEmptyObject(data.error)) {
               Toast.fire({
               type: 'success',
               icon: 'success', 
               title: data.success, 
               });
               location.reload();
            }else{
               Toast.fire({
                  type: 'error',
                  icon: 'error', 
                  title: data.error, 
               })
            }
            // End Message  
         }
      })
   }
</script>


{{-- ////////////////////////////////////////////////////////////// --}}
{{-- CART CONTROL --}}
<script>
function reloadCartHeader() {
    $.ajax({
        url: "{{ route('ajax.cart.header.reload') }}",
        method: 'GET',
        dataType: "json",
        success: function(response) {
            if (response.html) {
                $('#cart-header-container').html(response.html);
            }
        },
        error: function() {
            console.error('Lỗi khi tải lại giỏ hàng.');
        }
    });
}

function reloadCart() {
    $.ajax({
        url: "{{ route('ajax.cart.reload') }}",
        type: "GET",
        dataType: "json",
        success: function(response) {
        if (response.html) {
            $('#cart-container').html(response.html);
        }
        },
        error: function(xhr) {
        }
    });
}

$(document).ready(function () {
    $(document).on('click', '.btn-add-to-cart', function () {
        const id = $(this).data('id');
        $.ajax({
        url: `/ajax/add-to-cart/${id}`,
        method: 'GET',
        success: function (res) {
                if (res.status === 'success') {
                    Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message,
                    showConfirmButton: false,
                    timer: 1500
                    });

                    reloadCart();
                    reloadCartHeader();

                    // Cập nhật tất cả các cart-actions liên quan đến sản phẩm này
                    $('.btn-add-to-cart[data-id="' + id + '"], .qty-display[data-id="' + id + '"]').each(function () {
                    const container = $(this).closest('.cart-actions-1, .cart-actions-2, .cart-actions');

                    if (container.length > 0) {
                        container.html(`
                                <div class="d-flex justify-content-center align-items-center mt-2">
                                <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty" data-id="${id}" data-qty="${res.cartItem.quantity - 1}">
                                    <i class="icofont-minus"></i>
                                </button>
                                <span class="btn btn-sm btn-light mx-2 font-weight-bold qty-display" data-id="${id}">
                                    ${res.cartItem.quantity}
                                </span>
                                <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty" data-id="${id}" data-qty="${res.cartItem.quantity + 1}">
                                    <i class="icofont-plus"></i>
                                </button>
                                </div>
                        `);
                    }
                    });

                }
        },
        error: function (xhr) {
                const res = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: res.message ?? 'Không thể thêm sản phẩm.'
                });
        }
        });
    });
});

$(document).on('click', '.btn-change-qty', function () {
    const id = $(this).data('id');
    const newQty = $(this).data('qty');

    if (newQty <= 0) {
        return;
    }

    $.ajax({
        url: `/ajax/update-cart/${id}`,
        method: 'POST',
        data: {
            quantity: newQty,
            _token: '{{ csrf_token() }}'
        },
        success: function (res) {
            if (res.status === 'success') {
                // Cập nhật tất cả các nơi có cùng data-id
                $(`.qty-display[data-id="${id}"]`).text(res.cartItem.quantity);

                reloadCart();
                reloadCartHeader();

                // Cập nhật lại data-qty cho các nút
                $(`.btn-change-qty[data-id="${id}"]`).each(function () {
                    const isMinus = $(this).find('i').hasClass('icofont-minus');
                    const isPlus = $(this).find('i').hasClass('icofont-plus');

                    if (isMinus) {
                        $(this).data('qty', res.cartItem.quantity - 1);
                    }

                    if (isPlus) {
                        $(this).data('qty', res.cartItem.quantity + 1);
                    }
                });
            } else if (res.status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Thông báo',
                    text: res.message ?? 'Không thể cập nhật số lượng.'
                });
            }
        },
        error: function (xhr) {
            const res = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: res.message ?? 'Không thể cập nhật số lượng.'
            });
        }
    });
});

$(document).on('click', '.btn-remove', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/ajax/remove-from-cart/${id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã xóa',
                            text: 'Sản phẩm đã được xóa khỏi giỏ hàng.'
                        }).then(() => {
                            reloadCartHeader();
                            reloadCart();
                            // Cập nhật UI: chuyển tất cả các khu vực chứa sản phẩm này về nút "Thêm vào giỏ"
                            $(`.btn-remove[data-id="${id}"], .qty-display[data-id="${id}"], .btn-change-qty[data-id="${id}"]`).each(function () {
                                const container = $(this).closest('.cart-actions-1, .cart-actions-2, .cart-actions');

                                if (container.length > 0) {
                                container.html(`
                                    <button type="button" class="btn btn-primary btn-sm w-100 btn-add-to-cart mt-2" data-id="${id}">
                                        <i class="icofont-cart"></i> Thêm vào giỏ
                                    </button>
                                `);
                                }
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Không thể xóa sản phẩm.'
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi gửi yêu cầu.'
                    });
                }
            });
        }
    });
});

</script>


{{-- ////////////////////////////////////////////////////////////// --}}
{{-- SEARCH PRODUCT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Lấy form
    const searchForm = document.getElementById('search-form');

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Ngăn reload

        const query = searchForm.query.value;

        // Gửi request tới server
        fetch(`{{ route('search.products') }}?query=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('product-search').innerHTML = html;
        })
        .catch(error => console.error('Search error:', error));
    });
});
</script>

<script>
   document.addEventListener('change', function (e) {
      if (e.target.classList.contains('filter-checkbox')) {
         let categoryIds = [];
         let menuIds = [];

         document.querySelectorAll('.filter-checkbox:checked').forEach(cb => {
            const type = cb.getAttribute('data-type');
            const id = cb.getAttribute('data-id');
            if (type === 'category') categoryIds.push(id);
            if (type === 'menu') menuIds.push(id);
         });

         fetch(`{{ route('filter.products') }}`, {
            method: 'POST',
            headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}',
               'Content-Type': 'application/json',
               'Accept': 'application/json'
            },
            body: JSON.stringify({
               categories: categoryIds,
               menus: menuIds
            })
         })
         .then(response => response.text())
         .then(html => {
            document.getElementById('product-list').innerHTML = html;
         })
         .catch(error => console.error('Error:', error));
      }
   });
</script>


</body>
</html>