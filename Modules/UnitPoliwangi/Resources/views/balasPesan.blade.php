@extends('adminlte::page')
@section('title', 'List Menu')
{{-- @section('plugins.Select2', true) --}}
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
      <div class="card mt-3 mx-3 shadow" style="border-radius: 15px;">
        <div class="card-body">
            <h1 class="mx-5 mb-3" style="color: grey;">Laporan</h1>
            @if (session('success'))
                <div class="alert alert-success mx-5">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('pesan.store') }}" method="POST">
             @csrf
                <input type="hidden" name="complaint_id" value="{{ $balasPesan->id }}">
                <input type="hidden" name="unit_id" value="{{ $balasPesan->unit_id }}">

                <div class="mx-5 mt-3">
                    <label for="namaPelapor" class="form-label">Nama Pelapor</label>
                    <input type="text" class="form-control" value="{{ $balasPesan->user_name }}" readonly>
                </div>

                <div class="mx-5 mt-3">
                    <label for="complaintText" class="form-label">Laporan</label>
                    <textarea class="form-control" rows="3" readonly>{{ $balasPesan->complaint_text }}</textarea>
                </div>

                <div class="mx-5 mt-3">
                    <label for="namaUPT" class="form-label">Nama UPT</label>
                    <input type="text" class="form-control" value="{{ $balasPesan->unit_name }}" readonly>
                </div>
            

            <div class="mx-5 mt-3">
                <label for="response_text" class="form-label">Solusi</label>
                <textarea class="form-control" id="response_text" name="response_text" rows="3"></textarea>
            </div>

            <div class="d-flex justify-content-end mx-5">
                <a href="" class="btn btn-danger btn-lg mt-3 mx-3">Kembali</a>
                <button type="submit" class="btn btn-secondary btn-lg mt-3">Kirim</button>
            </div>
            </form>

            </div>
        </div>
@endsection

@push('css')
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
