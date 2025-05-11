@extends('admin.admin_dashboard')
 @section('admin') 
 
 <div class="page-content">
     <div class="container-fluid">
 
         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <h4 class="mb-sm-0 font-size-18">Báo Cáo Tất Cả Admin</h4>
 
                     <div class="page-title-right">
                         <ol class="breadcrumb m-0">
                            
                         </ol>
                     </div>
 
                 </div>
             </div>
         </div>
         <!-- end page title -->
 
 <div class="row">
     
     <div class="col">
         <div class="card">
             <div class="card-body">
                <div>
    
                  <div class="">
                      <div class="row" >
                          
              <div class="col-sm-4">
                  <div class="card">
                  <form id="myForm" action="{{ route('admin.search.bydate') }}" method="post" enctype="multipart/form-data">
                      @csrf
                      
                  <div class="row">
                      <div class="col-lg-12">
                          <div>
                              <h4>Tìm Kiếm Theo Ngày</h4>
                              <div class="form-group mb-3">
                                  <label for="example-text-input" class="form-label">Ngày</label>
                                  <input class="form-control" type="date" name="date"  id="example-text-input">
                              </div>
              
                              <div class="mt-4">
                                  <button type="submit" class="btn btn-primary waves-effect waves-light">Tìm Kiếm</button>
                              </div>
                      
                          </div>
                      </div> 
                  </div>
                  </form> 
                </div>
              </div>
              
              <div class="col-sm-4">
                  <div class="card">
                  <form id="myForm" action="{{ route('admin.search.bymonth') }}" method="post" enctype="multipart/form-data">
                      @csrf
                      
                  <div class="row">
                      <div class="col-lg-12">
                          <div>
                              <h4>Tìm Kiếm Theo Tháng</h4>
                              <div class="form-group mb-3">
                                  <label for="example-text-input" class="form-label">Select Month:</label>
                                  <select name="month" class="form-select">
                                    <option selected>Chọn Tháng</option>
                                    <option value="Janurary">Tháng 1</option>
                                    <option value="February">Tháng 2</option>
                                    <option value="March">Tháng 3</option>
                                    <option value="April">Tháng 4</option>
                                    <option value="May">Tháng 5</option>
                                    <option value="Jun">Tháng 6</option>
                                    <option value="July">Tháng 7</option>
                                    <option value="August">Tháng 8</option>
                                    <option value="September">Tháng 9</option>
                                    <option value="October">Tháng 10</option>
                                    <option value="November">Tháng 11</option>
                                    <option value="December">Tháng 12</option>
                                  </select>
              
                                  <label for="example-text-input" class="form-label">Select Year:</label>
                                  <select name="year_name" class="form-select">
                                    <option selected>Chọn Năm</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option> 
                                  </select>
              
                              </div>
              
                              <div class="mt-4">
                                  <button type="submit" class="btn btn-primary waves-effect waves-light">Tìm Kiếm</button>
                              </div>
                      
                          </div>
                      </div> 
                  </div>
                  </form> 
                </div>
              </div>
              
              
              
              <div class="col-sm-4">
                  <div class="card">
                  <form id="myForm" action="{{ route('admin.search.byyear') }}" method="post" enctype="multipart/form-data">
                      @csrf
                      
                  <div class="row">
                      <div class="col-lg-12">
                          <div>
                              <h4>Tìm Kiếm Theo Năm</h4>
                              <div class="form-group mb-3"> 
                                  <label for="example-text-input" class="form-label">Chọn Năm:</label>
                                  <select name="year" class="form-select">
                                    <option selected>Chọn Năm</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>  
                                  </select>
              
                              </div>
              
                              <div class="mt-4">
                                  <button type="submit" class="btn btn-primary waves-effect waves-light">Tìm Kiếm</button>
                              </div>
                      
                          </div>
                      </div> 
                  </div>
                  </form> 
                </div>
              </div>
              
              
                      </div>
                  </div>
              </div>
             </div>
         </div>
     </div> <!-- end col -->
 
  
 
  
 </div> <!-- end row --> 
  
 
          
     </div> <!-- container-fluid -->
 </div>
 
  
    
 
 
 @endsection