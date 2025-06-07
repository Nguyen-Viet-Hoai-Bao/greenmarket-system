@extends('admin.admin_dashboard')
 @section('admin') 
 
 <div class="page-content">
     <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <h4 class="mb-sm-0 font-size-18">Báo cáo thu chi</h4>
 
                     <div class="page-title-right">
                         <ol class="breadcrumb m-0">
                            
                         </ol>
                     </div>
 
                 </div>
             </div>
         </div>
         <!-- end page title -->
 
         <div class="row">
             <div class="col-12">
                 <div class="card">
                      
                     <div class="card-body">
                        <h4 class="mb-3">Quản lý thu chi Admin</h4>
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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Loại</th>
                                    <th>Số Tiền</th>
                                    <th>Mô Tả</th>
                                    <th>Thời Gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wallets as $key => $wallet)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if($wallet->type == 'income')
                                            <span class="badge bg-success">Thu</span>
                                        @else
                                            <span class="badge bg-danger">Chi</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($wallet->amount, 0, ',', '.') }}đ</td>
                                    <td>{{ $wallet->description ?? '---' }}</td>
                                    <td>{{ $wallet->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
 
                     </div>
                 </div>
             </div> <!-- end col -->
         </div> <!-- end row --> 
 
          
     </div> <!-- container-fluid -->
 </div>
 
  
    
 
 
 @endsection