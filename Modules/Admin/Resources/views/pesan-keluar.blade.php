@extends('adminlte::page')
@section('title', 'List Menu')
@section('plugins.Select2', true)
@section('content_header')
    <h1 class="m-0 text-dark"></h1>
@stop

@section('content')
<div class="container mt-4">
        @if ($pesan_keluar->isEmpty())
        <div class="alert alert-secondary text-center" style="color: black;">
            Tidak ada pesan keluar
        </div>
        @else
        <div class="mb-3 p-3 row">
            <label for="Cari Laporan" class="col-form-label">Search : </label>
            <div class="col-sm-4 d-flex">
            <input type="text" placeholder="Search Laporan" class="form-control" id="cariLaporan">
            <button type="button" class="btn" style="background-color: #3A5A80; color: white;" data-bs-toggle="modal" data-bs-target="#modalFilter"><i class="fa-solid fa-filter"></i></button>
            </div>
        </div>
        @foreach ($pesan_keluar as $item)
        <div id="tidakAdaHasil" class="alert text-center d-none" style="background-color: #3A5A80; color: white;">
            Laporan tidak ditemukan.
        </div>
        <div class="card shadow mb-4 card-laporan"
            data-unit="{{ strtolower($item->unit_name) }}"
            data-status="{{ strtolower($item->status) }}">
          <div class="card-header">
            Kepada : {{ $item->nama_pelapor }}
          </div>
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title text-dark">
                <strong>Dari : Admin</strong> 
                <small class="text-muted" data-tanggal="{{ \Carbon\Carbon::parse($item->date_replied_by)->format('H:i:s || d-m-Y') }}">{{ \Carbon\Carbon::parse($item->date_replied_by)->format('H:i:s || d-m-Y') }}</small>
              </h5>
              <p class="card-text">{{ $item->reply_text }}</p>
            </div>
            <div style="margin-left: auto;">
              <a class="btn btn-secondary btn-sm btn-custom" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">Detail</a>
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
                            <th class="col-md-1 text-center">Dari</th>
                            <th class="col-md-1 text-center">Kepada</th>
                            <th class="col-md-2 text-center">Permasalahan</th>
                            <th class="col-md-2 text-center">Balasan</th>
                            <th class="col-md-1 text-center">Diajukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td class="col-md-1 text-center">Admin</td>
                            <td class="col-md-1 text-center">{{ $item->nama_pelapor }}</td>
                            <td class="col-md-2 text-center">{{ $item->complaint_text }}</td>
                            <td class="col-md-2 text-center">{{ $item->reply_text }}</td>
                            <td class="col-md-1 text-center">{{ \Carbon\Carbon::parse($item->date_replied_by)->format('d-m-Y') }}</td>
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

<!-- Modal Filter -->
<div class="modal fade" id="modalFilter" tabindex="-1" aria-labelledby="modalFilterLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formFilter">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFilterLabel">Filter Laporan</h5>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="filterUnit" class="form-label">Unit : </label>
            <select id="filterUnit" class="form-select">
              <option value="">Semua Unit</option>
              @foreach ($pesan_keluar->pluck('unit_name')->unique() as $unit)
              <option value="{{ strtolower($unit) }}">{{ $unit }}</option>
              @endforeach
            </select>
          </div>
            <div class="mb-3">
                <label for="filterStartDate" class="form-label">Tanggal Mulai :</label>
                <input type="date" id="filterStartDate" class="form-control">
            </div>
            <div class="mb-3">
                <label for="filterEndDate" class="form-label">Tanggal Akhir :</label>
                <input type="date" id="filterEndDate" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-primary" id="resetFilter">Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('css')
<!-- Font Awesome v6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
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

        /* Modal styling */
        .modal-content {
            border-radius: 16px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background-color: #3A5A80;
            color: white;
            border-bottom: none;
            padding: 20px;
        }

        .modal-title {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .modal-body {
            background-color: #f8f9fa;
            padding: 25px;
        }

        .modal-footer {
            background-color: #f1f1f1;
            padding: 15px 20px;
            border-top: none;
        }

        .modal-footer .btn-primary {
            background-color: #3A5A80;
            border: none;
        }

        .modal-footer .btn-primary:hover {
            background-color: #2e4c6b;
        }

        .form-label {
            font-weight: 600;
            color: #3A5A80;
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.5rem;
        }

        .form-select:focus {
            border-color: #3A5A80;
            box-shadow: 0 0 0 0.2rem rgba(58, 90, 128, 0.25);
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
        
        const items = document.querySelectorAll('.card-laporan');

        document.getElementById('cariLaporan').addEventListener('input', applyLiveFilter);
        document.getElementById('filterUnit').addEventListener('change', applyLiveFilter);
        document.getElementById('filterStartDate').addEventListener('change', applyLiveFilter);
        document.getElementById('filterEndDate').addEventListener('change', applyLiveFilter);

        function applyLiveFilter() {
            const unitValue = document.getElementById('filterUnit').value.toLowerCase();
            const keyword = document.getElementById('cariLaporan').value.toLowerCase();
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;

            let count = 0;

            items.forEach((item) => {
                const unitText = item.dataset.unit;
                const complaintText = item.querySelector('.card-text').innerText.toLowerCase();
                const dateElement = item.querySelector('.card-title small');
                const complaintDate = dateElement.dataset.tanggal;
                const reportDate = new Date(complaintDate);

                const matchUnit = unitValue === '' || unitText.includes(unitValue);
                const matchKeyword = keyword === '' || complaintText.includes(keyword);

                let matchDate = true;
                if (startDate) {
                    const start = new Date(startDate + 'T00:00:00');
                    matchDate = matchDate && reportDate >= start;
                }
                if (endDate) {
                    const end = new Date(endDate + 'T23:59:59');
                    matchDate = matchDate && reportDate <= end;
                }

                if (matchUnit && matchKeyword && matchDate) {
                    item.classList.remove("d-none");
                    count++;
                } else {
                    item.classList.add("d-none");
                }
            });

            document.getElementById('tidakAdaHasil').classList.toggle('d-none', count !== 0);
        }

        document.getElementById('resetFilter').addEventListener('click', () => {
            document.getElementById('filterUnit').value = '';
            document.getElementById('cariLaporan').value = '';
            document.getElementById('filterStartDate').value = '';
            document.getElementById('filterEndDate').value = '';
            applyLiveFilter(); // Jalankan ulang filter
        });
    </script>
@endpush