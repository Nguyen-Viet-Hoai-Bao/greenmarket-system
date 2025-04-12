@extends('admin.admin_dashboard')
@section('admin')

<style>
        .btn-add-district {
        background-color: #92edd8;
        border-color: #a5ecec;
    }
    .btn-add-ward {
        background-color: #92eded;
        border-color: #a5ecec;
    }
    .btn-edit {
        background-color: #92cded;
        border-color: #a5d7ec;
    }
    .btn-delete {
        background-color: #edb692;
        border-color: #ece0a5;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">All City</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal">Add City</button>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="sidebar-menu">
                            <ul class="metismenu list-unstyled" id="side-menu">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Location Name</th>
                                            <th>Location Slug</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($city as $item)
                                            <tr class="city-row">
                                                <td><strong>{{ $item->city_name }}</strong></td>
                                                <td><strong>{{ $item->city_slug }}</strong></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-edit waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myCityEdit"  id="{{ $item->id }}" onclick="cityEdit(this.id)">Edit</button>
                                                    <a href="{{ route('delete.city', $item->id) }}" 
                                                        class="btn btn-sm btn-delete waves-effect waves-light" id="delete">Delete</a>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-add-district"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#myModalDistrict"
                                                        data-city-id="{{ $item->id }}"
                                                    >
                                                        Add District
                                                    </button>
                                                </td>
                                            </tr>
                                            @foreach($item->districts as $district)
                                                <tr class="district-row">
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $district->district_name }}</td>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $district->district_slug }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-edit waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myDistrictEdit"  id="{{ $district->id }}" onclick="districtEdit(this.id)">Edit</button>

                                                        <a href="{{ route('delete.district', $district->id) }}" 
                                                            class="btn btn-sm btn-delete waves-effect waves-light" id="delete">Delete</a>
                                                        <button
                                                            type="button"
                                                            class="btn btn-add-ward btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#myModalWard"
                                                            data-district-id="{{ $district->id }}"
                                                        >
                                                            Add Ward
                                                        </button>
                                                    </td>
                                                </tr>
                                                @foreach($district->wards as $ward)
                                                    <tr class="ward-row">
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $ward->ward_name }}</td>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $ward->ward_slug }}</td>
                                                        <td>
                                                          <button type="button" class="btn btn-sm btn-edit waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myWardEdit"  id="{{ $ward->id }}" onclick="wardEdit(this.id)">Edit</button>
  
                                                          <a href="{{ route('delete.ward', $ward->id) }}" 
                                                              class="btn btn-sm btn-delete waves-effect waves-light" id="delete">Delete</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('city.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">City Name</label>
                    <input class="form-control" type="text" name="city_name">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalDistrict" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="districtForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create District</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="city_id" id="district_city_id">
                    <label class="form-label">District Name</label>
                    <input class="form-control" type="text" name="district_name">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModalWard" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="wardForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Ward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="district_id" id="ward_district_id">
                    <label class="form-label">Ward Name</label>
                    <input class="form-control" type="text" name="ward_name">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal-Edit-content -->
<div id="myCityEdit" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="myModalLabel">Edit City</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form id="myCityEditForm" action="{{ route('city.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
            
                  <input type="hidden" name="cat_id" id="cat_id">
                  <div class="row">
                      <div class="col-lg-12">
                          <div>
                              <div class="form-group mb-6">
                                  <label for="example-text-input" class="form-label">City Name</label>
                                  <input class="form-control" type="text" name="city_name" value="" id="cat">
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
              </div>
          </form>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal-District-Edit-content -->
<div id="myDistrictEdit" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="myModalLabel">Edit Distric</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form id="myDistrictEditForm" action="{{ route('district.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
            
                  <input type="hidden" name="cat_id_1" id="cat_id_1">
                  <div class="row">
                      <div class="col-lg-12">
                          <div>
                              <div class="form-group mb-6">
                                  <label for="example-text-input" class="form-label">District Name</label>
                                  <input class="form-control" type="text" name="district_name" value="" id="cat_1">
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
              </div>
          </form>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal-Ward-Edit-content -->
<div id="myWardEdit" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="myModalLabel">Edit Ward</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form id="myWardForm" action="{{ route('ward.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
            
                  <input type="hidden" name="cat_id_2" id="cat_id_2">
                  <div class="row">
                      <div class="col-lg-12">
                          <div>
                              <div class="form-group mb-6">
                                  <label for="example-text-input" class="form-label">Ward Name</label>
                                  <input class="form-control" type="text" name="ward_name" value="" id="cat_2">
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
              </div>
          </form>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  function cityEdit(id){
      $.ajax({
          type: 'GET',
          url: '/edit/city/'+id,
          dataType: 'json',

          success:function(data){
              $('#cat').val(data.city_name);
              $('#cat_id').val(data.id);
          }
      })
  }
  function districtEdit(id){
      $.ajax({
          type: 'GET',
          url: '/edit/district/'+id,
          dataType: 'json',

          success:function(data){
              $('#cat_1').val(data.district_name);
              $('#cat_id_1').val(data.id);
          }
      })
  }
  function wardEdit(id){
      $.ajax({
          type: 'GET',
          url: '/edit/ward/'+id,
          dataType: 'json',

          success:function(data){
              $('#cat_2').val(data.ward_name);
              $('#cat_id_2').val(data.id);
          }
      })
  }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const districtModal = document.getElementById('myModalDistrict');
        districtModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const cityId = button.getAttribute('data-city-id');
            const form = document.getElementById('districtForm');
            form.setAttribute('action', `/city/district/store/${cityId}`);
        });

        const wardModal = document.getElementById('myModalWard');
        wardModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const districtId = button.getAttribute('data-district-id');
            const form = document.getElementById('wardForm');
            form.setAttribute('action', `/district/ward/store/${districtId}`);
        });
    });
</script>

@endsection