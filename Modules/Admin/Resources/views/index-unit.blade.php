@extends('adminlte::page')
@section('title', 'List Unit')
@section('plugins.Datatables', true)
@section('content_header')
    <h1 class="m-0 text-dark">List Unit</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">Tambah Unit</button>
    </div>
    <div class="card-body">
        <table id="unitTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">Nama Unit</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unit as $item)
                <tr>
                    <td class="text-center">{{ $item->nama }}</td>
                    <td class="text-center">
                        <a href="#" onclick="showEditModal('{{ $item->id }}', '{{ $item->nama }}')" 
                        class="btn btn-warning btn-sm mx-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="#" onclick="showConfirmModal('{{ route('hapus_unit', $item->id) }}', '{{ $item->nama }}')" 
                        class="btn btn-danger btn-sm mx-2">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Unit</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formTambah">
                    @csrf
                    <div class="form-group">
                        <label>Nama Unit</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Unit</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label>Nama Unit</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage">Apakah Anda yakin ingin menghapus unit ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Tambah Unit
    $('#formTambah').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route('unit_store') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#modalTambah').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal menyimpan data');
            }
        });
    });

    // Fungsi untuk menampilkan modal edit
    function showEditModal(id, nama) {
        $('#edit_id').val(id);
        $('#edit_nama').val(nama);
        $('#modalEdit').modal('show');
    }

    // Edit Unit
    $('#formEdit').submit(function(e) {
        e.preventDefault();
        let id = $('#edit_id').val(); // Ambil ID dari input hidden

        $.ajax({
            url: `/admin/edit_unit/${id}`, // Gunakan ID dalam URL
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#modalEdit').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal menyimpan perubahan: ' + xhr.responseText);
            }
        });
    });

    let deleteUrl = '';

    function showConfirmModal(url, nama) {
        deleteUrl = url;
        $('#deleteMessage').text(`Apakah Anda yakin ingin menghapus unit "${nama}"?`);
        $('#modalConfirmDelete').modal('show');
    }

    $('#confirmDelete').click(function() {
        $.ajax({
            url: deleteUrl,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#modalConfirmDelete').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal menghapus data');
            }
        });
    });
</script>
@endpush
