
// $(function(){
//     $(document).on('click','#delete',function(e){
//         e.preventDefault();
//         var link = $(this).attr("href");

  
//                   Swal.fire({
//                     title: 'Are you sure?',
//                     text: "Delete This Data?",
//                     icon: 'warning',
//                     showCancelButton: true,
//                     confirmButtonColor: '#3085d6',
//                     cancelButtonColor: '#d33',
//                     confirmButtonText: 'Yes, delete it!'
//                   }).then((result) => {
//                     if (result.isConfirmed) {
//                       window.location.href = link
//                       Swal.fire(
//                         'Deleted!',
//                         'Your file has been deleted.',
//                         'success'
//                       )
//                     }
//                   }) 


//     });

// });


$(function(){
  $(document).on('click','#delete',function(e){
      e.preventDefault();
      var link = $(this).attr("href");

      Swal.fire({
          title: 'Bạn có chắc chắn?',
          text: "Bạn có muốn xóa dữ liệu này không?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Vâng, xóa đi!',
          cancelButtonText: 'Hủy bỏ'
      }).then((result) => {
          if (result.isConfirmed) {
              window.location.href = link;
              Swal.fire(
                  'Đã xóa!',
                  'Dữ liệu của bạn đã được xóa.',
                  'success'
              )
          }
      });
  });
});




$(function(){
    $(document).on('click','#confirmOrder',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
  
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Bạn có muốn xác nhận đơn hàng này không?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Vâng, xác nhận!',
            cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
                Swal.fire(
                    'Đã xác nhận!',
                    'Đơn hàng đã được xác nhận.',
                    'success'
                )
            }
        });
    });
  });

$(function(){
  $(document).on('click','#processingOrder',function(e){
      e.preventDefault();
      var link = $(this).attr("href");

      Swal.fire({
          title: 'Bạn có chắc chắn?',
          text: "Bạn có muốn xử lý đơn hàng này không?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Vâng, xác nhận!',
          cancelButtonText: 'Hủy bỏ'
      }).then((result) => {
          if (result.isConfirmed) {
              window.location.href = link;
              Swal.fire(
                  'Đã xác nhận!',
                  'Đơn hàng đã được xử lý.',
                  'success'
              )
          }
      });
  });
});

$(function(){
    $(document).on('click','#deliverdOrder',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
  
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Bạn có muốn giao đơn hàng này không?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Vâng, xác nhận!',
            cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
                Swal.fire(
                    'Đã xác nhận!',
                    'Đơn hàng đang được giao.',
                    'success'
                )
            }
        });
    });
  });
