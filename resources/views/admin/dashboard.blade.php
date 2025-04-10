@extends('adminlte::page')
@section('title', 'List Menu')
@section('plugins.Select2', true)
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
<div class="row mt-5 mx-3">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-radius:15px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Jumlah Pesan Masuk</div>
                            <div class="h5 mb-4 mt-3 font-weight-bold text-gray-800"> Pesan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2xl text-gray-300 me-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" style="border-radius:15px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Pelapor</div>
                            <div class="h5 mb-4 mt-3 font-weight-bold text-gray-800"> Orang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2xl text-gray-300 me-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2" style="border-radius:15px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Laporan</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-4 mt-3 mr-3 font-weight-bold text-gray-800"> Laporan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cart-plus fa-2xl text-gray-300 me-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2" style="border-radius:15px;">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Laporan Bulan Ini</div>
                    <div class="h5 mb-4 mt-3 font-weight-bold text-gray-800"> Laporan</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-dollar-sign fa-2xl text-gray-300 me-3"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>

        <div class="row mx-3">
        <!-- Data Stok Barang -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4" style="border-radius:15px;">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="border-radius:15px;">
                    <h6 class="m-0 font-weight-bold text-secondary">Data Stok Barang</h6>
                    <!-- <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div> -->
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-container" style="margin-left: 40px;">
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Penjualan Bulan Ini -->
        <div class="col-xl-8 col-lg-8">
            <div class="card shadow mb-4" style="border-radius:15px;">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="border-radius:15px;">
                    <h6 class="m-0 font-weight-bold text-secondary">Data Penjualan Bulan Ini</h6>
                    <!-- <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div> -->
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-container" style="margin-left: 20px;">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script src="/assets/js/jquery.nestable.min.js"></script>
    <script src="/assets/js/menu-manager.js"></script>
    <script src="/assets/js/bootstrap-select.min.js"></script>
    <script>
         $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            $('#tt').selectpicker();
         });
        function setIcon(nama){
            $("#icon").val(nama);
            $('#modalIcon').modal('toggle');
        }
        
    </script>
@endpush