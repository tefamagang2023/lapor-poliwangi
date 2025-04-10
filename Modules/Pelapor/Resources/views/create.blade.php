@extends('adminlte::page')
@section('title', 'List Menu')
@section('plugins.Select2', true)
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
<form action='{{ route('store-laporan') }}' method='post'>
    @csrf
      <div class="card mt-3 mx-3 shadow" style="border-radius: 15px;">
        <div class="card-body">
            @if (session('success'))
                    <div class="alert alert-warning mx-5 mt-3">
                        {{ session('success') }}
                    </div>
            @endif
            <h1 class="mx-5 mb-3" style="color: grey;">Laporan</h1>
            <div class="mx-5 mb-3">
                <label for="unit" class="col-sm-2 col-form-label required">Unit</label>
                    <div class="col">
                        <select name="unit_id" class="form-control">
                            <option value="" class="text-center">--- Pilih ---</option>
                            @foreach ($unit_id as $item)
                                <option value="{{ $item->id }}" class="text-center" {{ old('unit_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        @if (count($errors) > 0)
                        <div style="width:auto; color:#dc4c64; margin-top:0.25rem;">
                            {{ $errors->first('unit_id') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="mx-5 mt-3">
                <label for="nomor_pelapor" class="form-label">Nomor Hp</label>
                <input type="text" class="form-control" name="nomor_pelapor">
            </div>
            <div class="mx-5 mt-3">
                <label for="exampleFormControlTextarea1" class="form-label">Permasalahan</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" name="complaint_text" rows="3"></textarea>
            </div>
            <div class="d-flex justify-content-end mx-5">
                <button type="submit" class="btn btn-secondary btn-lg mt-3">Kirim</button>
            </div>
            </div>
        </div>
    </form>
@endsection

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
