<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Modules\Pelapor\Entities\Complaint;
use Modules\UnitPoliwangi\Entities\responses;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [];

    public static function menampilkanLaporan() {
        $pesan = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
            ->join('users', 'complaints.user_id', '=', 'users.id')
            ->select('complaints.*', 'units.nama as unit_name', 'users.name as user_name')
            ->get();
        return $pesan;
    }

    public static function menampilkanLaporanMasukUnit()
        {
            $user = Auth::user();

            // Ambil semua data dari tabel responses beserta relasi yang diperlukan
            $pesan_masuk_upt = responses::join('complaints', 'responses.complaint_id', '=', 'complaints.id')
                ->join('units', 'responses.unit_id', '=', 'units.id')
                ->join('users as pelapor', 'complaints.user_id', '=', 'pelapor.id')
                ->join('users as reviewer', 'responses.reviewed_by', '=', 'reviewer.id')
                ->where('responses.status', 'pending')
                ->select(
                    'responses.*', 
                    'units.nama as unit_name', 
                    'complaints.nama_pelapor as nama_pelapor',

                    // Data pelapor
                    'pelapor.name as pelapor_name',

                    // Data reviewer (yang melakukan review)
                    'reviewer.name as reviewer_name',

                )
                ->get();

            return $pesan_masuk_upt;
        }

        public static function menampilkanLaporanMasuk()
        {
            $user = Auth::user();

            // Admin bisa melihat semua laporan kecuali yang "forwarded"
            $pesan_masuk = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
                ->where('complaints.status', 'pending')
                ->select('complaints.*', 'units.nama as unit_name')
                ->get();

            return $pesan_masuk; // Tambahkan return agar bisa digunakan di controller
        }

        public static function menampilkanLaporanKeluar()
        {
            $user = Auth::user();

            // Admin bisa melihat semua laporan kecuali yang "forwarded"
            $pesan_keluar = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
                ->join('users', 'complaints.user_id', '=', 'users.id')
                ->where('complaints.replied_by', $user->id)
                ->select('complaints.*', 'units.nama as unit_name', 'users.name as user_name',)
                ->get();

            return $pesan_keluar; // Tambahkan return agar bisa digunakan di controller
        }

        public static function MengirimLaporan($id) {
            $user = Auth::user();
    
            $laporan = Complaint::find($id);
    
            if(!$laporan){
                return [
                    'status' => 'error',
                    'message' => 'Laporan tidak ditemukan',
                ];
            }
    
            $laporan->status = 'processed';
            $laporan->user_id = $user->id;
            $laporan->processed_at = date('Y-m-d H:i:s');
            $laporan->save();
    
            return $laporan;
        }

        public static function menampilkanLaporanKeluarUnit()
        {
            $user = Auth::user();

            // Admin bisa melihat semua laporan kecuali yang "forwarded"
            $pesan_keluar = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
                ->join('users', 'complaints.user_id', '=', 'users.id')
                ->where('complaints.status', 'completed')
                ->select('complaints.*', 'units.nama as unit_name', 'users.name as user_name',)
                ->get();

            // dd($pesan_keluar);

            return $pesan_keluar; // Tambahkan return agar bisa digunakan di controller
        }

        public static function RedirecWa($complaint_id)
    {
        $response = responses::with('complaint', 'reviewer', 'unit')
            ->where('id', $complaint_id)
            ->latest()
            ->first();

        // 🔹 Pastikan response tidak null sebelum mengakses relasi
        if ($response && $response->complaint) {
            $nomorPelapor = $response->complaint->nomor_pelapor ?? null;
            $namaPelapor = $response->complaint->nama_pelapor ?? 'Tidak diketahui';
            $complaintText = $response->complaint->complaint_text ?? 'Tidak ada deskripsi masalah.';
        } else {
            $nomorPelapor = null;
            $namaPelapor = 'Tidak diketahui';
            $complaintText = 'Tidak ada deskripsi masalah.';
        }

        // dd($nomorPelapor, $namaPelapor, $complaintText);

        // 🔹 Ambil response terbaru
        $responseText = $response->response_text ?? "Belum ada respons.";
        $reviewerNama = $response->reviewer->name ?? "Belum direview";
        $unitReviewer = $response->unit->nama ?? "Tidak diketahui";

        // dd($responseText, $reviewerNama, $unitReviewer);

        $text = "Halo%20*$namaPelapor*%0A"
          . "Saya%20dari%20admin%20mengirim%20konfirmasi%20atas%20masalah%3A%20%22*$complaintText*%22%0A"
          . "Saya%20*$reviewerNama*%20Dari%20*$unitReviewer*%0A"
          . "%22*$responseText*%22";

        $response->status = 'approved';
        $response->save();

        return "https://api.whatsapp.com/send?phone=" . 6282132945801 . "&text=" . $text;
    }
    
    protected static function newFactory()
    {
        return \Modules\Admin\Database\factories\AdminFactory::new();
    }
}
