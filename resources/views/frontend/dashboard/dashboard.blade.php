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

// $(document).ready(function () {
//     $(document).on('click', '.btn-add-to-cart', function () {
//         const id = $(this).data('id');
//         $.ajax({
//         url: `/ajax/add-to-cart/${id}`,
//         method: 'GET',
//         success: function (res) {
//                 if (res.status === 'success') {
//                     Swal.fire({
//                     toast: true,
//                     position: 'top-end',
//                     icon: 'success',
//                     title: res.message,
//                     showConfirmButton: false,
//                     timer: 1500
//                     });

//                     reloadCart();
//                     reloadCartHeader();

//                     // Cập nhật tất cả các cart-actions liên quan đến sản phẩm này
//                     $('.btn-add-to-cart[data-id="' + id + '"], .qty-display[data-id="' + id + '"]').each(function () {
//                     const container = $(this).closest('.cart-actions-1, .cart-actions-2, .cart-actions');

//                     if (container.length > 0) {
//                         container.html(`
//                                 <div class="d-flex justify-content-center align-items-center mt-2">
//                                 <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty" data-id="${id}" data-qty="${res.cartItem.quantity - 1}">
//                                     <i class="icofont-minus"></i>
//                                 </button>
//                                 <span class="btn btn-sm btn-light mx-2 font-weight-bold qty-display" data-id="${id}">
//                                     ${res.cartItem.quantity}
//                                 </span>
//                                 <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty" data-id="${id}" data-qty="${res.cartItem.quantity + 1}">
//                                     <i class="icofont-plus"></i>
//                                 </button>
//                                 </div>
//                         `);
//                     }
//                     });

//                 }
//         },
//         error: function (xhr) {
//                 const res = xhr.responseJSON;
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Lỗi',
//                     text: res.message ?? 'Không thể thêm sản phẩm.'
//                 });
//         }
//         });
//     });
// });

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

$(document).ready(function () {
    // Hàm format tiền tệ (nếu chưa có)
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }

    let selectedProductUnitId = null;
    let currentProductId = null;
    let currentProductData = null;

    // 1. Xử lý click nút "Thêm vào giỏ hàng"
    $(document).on('click', '.btn-add-to-cart', function () {
        currentProductId = $(this).data('id');
        
        // Gửi yêu cầu Ajax để lấy chi tiết sản phẩm và các unit
        $.ajax({
            url: `/api/product-details-for-cart/${currentProductId}`,
            method: 'GET',
            success: function (res) {
                if (res.status === 'success' && res.product) {
                    currentProductData = res.product;

                    if (currentProductData.display_mode === 'unit' && currentProductData.units.length > 0) {
                        const modalContent = $('#product-selection-modal-content');
                        modalContent.html(`
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Đang tải...</span>
                                </div>
                                <p class="mt-2">Đang tải thông tin sản phẩm...</p>
                            </div>
                        `);
                        $('#confirmAddToCartBtn').prop('disabled', true);
                        $('#modalQuantity').val(1);
                        selectedProductUnitId = null;

                        $('#productSelectionModal').modal('show');
                        
                        let unitsHtml = '';
                        unitsHtml += `<p class="font-weight-bold mt-3">Chọn Trọng lượng/Đơn vị:</p>`;
                        unitsHtml += `<div class="d-flex flex-wrap">`;
                        currentProductData.units.forEach(unit => {
                            const formattedPrice = formatCurrency(unit.price);
                            let unitLabel = '';
                            if (unit.display_mode === 'unit') {
                                unitLabel = unit.weight ? `${unit.weight} KG - HSD: ${unit.expiry_date}` : `HSD: ${unit.expiry_date}`;
                            } else {
                                unitLabel = unit.batch_qty ? `${unit.batch_qty} ${currentProductData.unit}` : `N/A`;
                            }
                            unitLabel += ` (${formattedPrice})`;

                            // THAY ĐỔI Ở ĐÂY: Thêm class 'in-cart' và thuộc tính 'disabled' nếu unit đã có trong giỏ
                            const isInCartClass = unit.is_in_cart ? 'in-cart' : '';
                            const isDisabledAttr = (!unit.has_stock || unit.is_in_cart) ? 'disabled' : ''; // Vô hiệu hóa nếu hết hàng HOẶC đã có trong giỏ

                            unitsHtml += `
                                <button
                                    class="btn btn-outline-info btn-unit-select mx-1 my-1 ${isInCartClass}"
                                    data-unit-id="${unit.id}"
                                    data-price="${unit.price}"
                                    data-display-mode="${unit.display_mode}"
                                    data-batch-qty="${unit.batch_qty}"
                                    data-total-available-quantity="${unit.total_available_quantity}"
                                    ${isDisabledAttr}
                                >
                                    ${unitLabel}
                                    ${unit.is_in_cart ? ' (Đã chọn)' : (unit.has_stock ? '' : ' (Hết hàng)')}
                                </button>
                            `;
                        });
                        unitsHtml += `</div>`;

                        modalContent.html(`
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="${currentProductData.image ? currentProductData.image : 'https://via.placeholder.com/150?text=No+Image'}" alt="${currentProductData.name}" class="img-fluid rounded" style="max-width: 150px;">
                                </div>
                                <div class="col-md-8">
                                    <h5 class="font-weight-bold">${currentProductData.name}</h5>
                                    <p>${currentProductData.description ?? 'Không có mô tả.'}</p>
                                    ${unitsHtml}
                                    <div class="quantity-control mt-3" hidden>
                                        <p class="font-weight-bold mb-1">Số lượng:</p>
                                        <div class="input-group" style="width: 120px;">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-secondary btn-qty-modal-minus" type="button" disabled>-</button>
                                            </div>
                                            <input type="text" class="form-control text-center" id="modalQuantity" value="1" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-qty-modal-plus" type="button" disabled>+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="final-price-display mt-2 font-weight-bold" hidden>Tổng tiền: <span class="text-danger"></span></p>
                                </div>
                            </div>
                        `);
                        updateModalFinalPrice();

                    } else {
                        let defaultUnitId = null;
                        if (currentProductData.units && currentProductData.units.length > 0) {
                            defaultUnitId = currentProductData.units[0].id;
                            if (!currentProductData.units[0].has_stock) {
                                Swal.fire({ icon: 'warning', title: 'Hết hàng', text: 'Sản phẩm này hiện không có sẵn để thêm vào giỏ.' });
                                return;
                            }
                            // Thêm kiểm tra nếu unit mặc định đã có trong giỏ và không thể tăng số lượng (display_mode = 'unit')
                            if (currentProductData.units[0].is_in_cart && currentProductData.units[0].display_mode === 'unit') {
                                Swal.fire({ icon: 'info', title: 'Thông báo', text: 'Đơn vị sản phẩm này đã có trong giỏ hàng.' });
                                return;
                            }
                        } else {
                            Swal.fire({ icon: 'warning', title: 'Thông báo', text: 'Sản phẩm này hiện không có sẵn để thêm vào giỏ.' });
                            return;
                        }
                        
                        addToCartFinal(currentProductId, defaultUnitId, 1);
                    }

                } else {
                    Swal.fire({ icon: 'error', title: 'Lỗi', text: res.message ?? 'Không thể tải thông tin sản phẩm. Vui lòng thử lại sau.' });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Có lỗi xảy ra khi tải thông tin sản phẩm. Vui lòng thử lại sau.' });
                console.error('AJAX Error: ', status, error, xhr.responseText);
            }
        });
    });

    // 2. Xử lý khi click vào các nút chọn đơn vị (weight/HSD) trong modal
    $(document).on('click', '.btn-unit-select', function() {
        // Đảm bảo không xử lý click nếu nút bị disabled (do hết hàng hoặc đã trong giỏ)
        if ($(this).is(':disabled')) {
            return;
        }

        $('.btn-unit-select').removeClass('active btn-info').addClass('btn-outline-info');
        $(this).removeClass('btn-outline-info').addClass('active btn-info');

        selectedProductUnitId = $(this).data('unit-id');
        const unitDisplayMode = $(this).data('display-mode');
        const totalAvailableQuantity = $(this).data('total-available-quantity');

        $('#modalQuantity').val(1);

        if (unitDisplayMode === 'unit') {
            $('.btn-qty-modal-plus').prop('disabled', true);
            $('.btn-qty-modal-minus').prop('disabled', true);
        } else {
            if (totalAvailableQuantity > 1) {
                $('.btn-qty-modal-plus').prop('disabled', false);
            } else {
                 $('.btn-qty-modal-plus').prop('disabled', true);
            }
            $('.btn-qty-modal-minus').prop('disabled', true);
        }
        $('#confirmAddToCartBtn').prop('disabled', false);
        
        updateModalFinalPrice();
    });

    // Hàm cập nhật tổng tiền trong modal
    function updateModalFinalPrice() {
        const qty = parseInt($('#modalQuantity').val());
        let finalPrice = 0;
        const selectedUnitBtn = $('.btn-unit-select.active');
        if (selectedUnitBtn.length > 0) {
            const unitPrice = parseFloat(selectedUnitBtn.data('price'));
            finalPrice = unitPrice * qty;
        }
        $('.final-price-display span').text(formatCurrency(finalPrice));
    }

    // 5. Hàm gửi yêu cầu thêm sản phẩm vào giỏ hàng (từ modal hoặc trực tiếp)
    function addToCartFinal(productId, unitId, quantity) {
        $.ajax({
            url: `/ajax/add-to-cart-with-unit/${productId}`,
            method: 'POST',
            data: {
                product_unit_id: unitId,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                if (res.status === 'success') {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });

                    $('#productSelectionModal').modal('hide');
                    
                    selectedProductUnitId = null; 
                    currentProductId = null;
                    currentProductData = null;

                    reloadCart();
                    reloadCartHeader();

                    if (res.cartItem.display_mode === 'quantity') {
                        const itemKey = res.cartItem.id;
                        
                        $(`.btn-add-to-cart[data-id="${res.cartItem.id}"], .qty-display[data-id="${res.cartItem.id}"], .qty-display[data-id="${itemKey}"]`).each(function () {
                            const container = $(this).closest('.cart-actions-1, .cart-actions-2, .cart-actions');
                            if (container.length > 0) {
                                container.html(`
                                    <div class="d-flex justify-content-center align-items-center mt-2">
                                        <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty" data-id="${itemKey}" data-qty="${res.cartItem.quantity - 1}">
                                            <i class="icofont-minus"></i>
                                        </button>
                                        <span class="btn btn-sm btn-light mx-2 font-weight-bold qty-display" data-id="${itemKey}">
                                            ${res.cartItem.quantity}
                                        </span>
                                        <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty" data-id="${itemKey}" data-qty="${res.cartItem.quantity + 1}">
                                            <i class="icofont-plus"></i>
                                        </button>
                                    </div>
                                `);
                            }
                        });
                    }

                } else if (res.status === 'error' || res.status === 'info') {
                    Swal.fire({ icon: res.status, title: 'Thông báo', text: res.message ?? 'Không thể thêm sản phẩm.' });
                }
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                Swal.fire({ icon: 'error', title: 'Lỗi', text: res.message ?? 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ.' });
                console.error('AJAX Error on adding to cart:', xhr.responseText);
            }
        });
    }

    // 6. Xử lý khi click nút "Thêm vào giỏ hàng" trong modal (nếu modal đang mở)
    $(document).on('click', '#confirmAddToCartBtn', function () {
        if (!selectedProductUnitId || !currentProductId) {
            Swal.fire({ icon: 'warning', title: 'Lỗi', text: 'Vui lòng chọn một đơn vị sản phẩm.' });
            return;
        }

        const quantity = parseInt($('#modalQuantity').val());
        addToCartFinal(currentProductId, selectedProductUnitId, quantity);
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