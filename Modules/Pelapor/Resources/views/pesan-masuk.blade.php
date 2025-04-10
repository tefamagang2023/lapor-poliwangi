@extends('adminlte::page')
@section('title', 'List Menu')
@section('plugins.Select2', true)
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
    <div class="container mt-4">
        @if (session('success'))
                <div class="alert alert-success mx-5 mt-3">
                    {{ session('success') }}
                </div>
        @endif
        @if ($pesan_masuk->isEmpty())
        <div class="alert text-center mt-5" style="background-color: #3A5A80; color: white;">
            Tidak ada pesan masuk
        </div>
        @else
        @foreach ($pesan_masuk as $item)
        <div class="card shadow mb-4">
          <div class="card-header">
            Kepada : {{ $item->nama_pelapor }}
          </div>
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title text-dark">
                <strong>Dari : </strong> {{ $item->replied_name }}
                <small class="text-muted">{{ \Carbon\Carbon::parse($item->updated_at ?? $item->created_at)->format('H:i:s || d-m-Y') }}</small>
              </h5>
              <p class="card-text">{{ $item->reply_text }}</p>
            </div>
            <div style="margin-left: auto;">
              @if (!is_null($item->date_reply_pelapor))
                  <span class="text-success">Sudah Dibalas</span>
              @else
              <a class="btn btn-dark btn-sm btn-custom me-2" data-bs-toggle="modal" data-bs-target="#modalReply{{ $item->id }}">Reply</a>
              @endif
              <!-- <a href="#" class="btn btn-primary btn-sm btn-custom me-2">Teruskan</a>
              <a class="btn btn-secondary btn-sm btn-custom" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">Detail</a> -->
            </div>
          </div>
        </div>
        @endforeach
        @endif
      </div>
    </div>

    @foreach ($pesan_masuk as $item)
    <div class="modal fade" id="modalReply{{ $item->id }}" tabindex="-1" aria-labelledby="modalReplyLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #3A5A80;">
                    <h5 class="modal-title" id="modalReplyLabel{{ $item->id }}">Balas Laporan</h5>
                </div>
                <form action="{{ route('balas_pelapor', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <h6 class="fw-bold text-muted">Laporan</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $item->complaint_text }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-bold text-muted">Balasan</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $item->reply_text }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold text-muted">Balasan</h6>
                            <textarea class="form-control border-0 shadow p-3" name="complain_text" rows="4" placeholder="Tulis balasan di sini..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn" style="background-color: #3A5A80; color: white;">Kirim Balasan</button>
                    </div>
                </form>
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