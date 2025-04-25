@extends('adminlte::page')
@section('title', 'List Menu')
@section('plugins.Select2', true)
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
      <div class="container mt-4">
        @if ($pesan_keluar->isEmpty())
        <div class="alert text-center mt-5" style="background-color:#3A5A80; color: white;">
            Tidak ada pesan masuk
        </div>
        @else
        @foreach ($pesan_keluar as $item)
        <div class="card shadow mb-4" style="border-radius: 15px;">
          <div class="card-header">
            Kepada : {{ $item->unit_name }}
          </div>
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title text-dark">
                <strong>Dari : </strong> {{ $item->nama_pelapor }}
                <small class="text-muted">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s || d-m-Y') }}</small>
              </h5>
              <p class="card-text">{{ $item->complaint_text }}</p>
            </div>
            <div style="margin-left: auto;">
              <!-- <a class="btn btn-dark btn-sm btn-custom me-2" data-bs-toggle="modal" data-bs-target="#modalReply{{ $item->id }}">Reply</a>
              <a href="#" class="btn btn-primary btn-sm btn-custom me-2">Teruskan</a> -->
              <a class="btn btn-light btn-sm btn-custom" style="background-color: #3A5A80; color: white;" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">Detail</a>
            </div>
          </div>
        </div>
        @endforeach
        @endif
      </div>
    </div>

@foreach ($pesan_keluar as $item)
<div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel{{ $item->id }}">Detail Laporan</h5>
            </div>
            <div class="modal-body">
                <h5>Data Pelapor</h5>
                <table class="table table-striped">
                    <thead>
                        <tr class="text-center">
                            <th class="col-md-1 text-center">Nama Pelapor</th>
                            <th class="col-md-1 text-center">Kepada</th>

                            <th class="col-md-2 text-center">Permasalahan</th>
                            <th class="col-md-1 text-center">Diajukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td class="col-md-1 text-center">{{ $item->nama_pelapor }}</td>
                            <td class="col-md-1 text-center">{{ $item->unit_name }}</td>

                            <td class="col-md-2 text-center">{{ $item->complaint_text }}</td>
                            <td class="col-md-1 text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('css')
<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .card:hover {
        transform: scale(1.02);
    }
    .card-header {
        background-color: #3A5A80;
        color: white;
        font-weight: bold;
        padding: 15px;
    }
    .btn-custom {
        transition: all 0.3s ease-in-out;
    }
    .btn-custom:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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