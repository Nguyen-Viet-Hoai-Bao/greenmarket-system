@extends('frontend.dashboard.dashboard')
@section('dashboard')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >

<section class="section pt-4 pb-4 osahan-account-page">
  <div class="container">
     <div class="row">

        @include('frontend.dashboard.sidebar')

<div class="col-md-9">
  <div class="osahan-account-page-right rounded shadow-sm bg-white p-4 h-100">
    <div class="tab-pane">
      <h4 class="font-weight-bold mt-0 mb-4">Danh sách yêu thích</h4>
      <div class="row">

@foreach ($wishlist as $wish)
<div class="col-md-4 col-sm-6 mb-4 pb-2">
  <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
      <div class="list-card-image">
        <a href="{{ route('market.details', $wish->client_id) }}">
        <img src="{{ asset($wish['client']['photo'] ) }}" class="img-fluid item-img" style="width: 300px; hight:300px">
        </a>
      </div>
      <div class="p-3 position-relative">
        <div class="list-card-body">
            <h6 class="mb-1">
              <a href="{{ route('market.details', $wish->client_id) }}" class="text-black">
                {{ $wish['client']['name'] }}
              </a>
            </h6>
            <div style="float: right; margin-botton:5px">
              <a href="{{ route('remove.wishlist', $wish->id) }}" class="badge badge-danger">
                <i class="icofont-ui-delete"></i>
              </a>
            </div>
        </div>
      </div>
  </div>
</div>
@endforeach

      </div>
    </div>

  </div>
</div>
     </div>
  </div>
</section>

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

@endsection