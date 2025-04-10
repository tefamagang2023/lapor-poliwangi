<?php

namespace Modules\UnitPoliwangi\Entities;

use App\Models\Core\User;
use Complaints;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Entities\Unit;
use Modules\Pelapor\Entities\Complaint;

class responses extends Model
{
    use HasFactory;
    protected $table = 'responses';
    protected $fillable = ['complaint_id', 'unit_id', 'response_text', 'status', 'reviewed_by', 'sent_at', 'reviewed_at'];
    
    protected static function newFactory()
    {
        return \Modules\UnitPoliwangi\Database\factories\ResponsesFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public static function getBalasPesan()
    {
        return self::with(['user', 'unit'])->get();
    }

    public static function menampilkanlaporan()
    {
        $user = Auth::user();

        // Ambil unit dari user jika ada
        $unitUser = $user->unit_id; 

        // Cek apakah user memiliki unit_id
        if ($unitUser) {
            $pesan_masuk = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
                ->join('users', 'complaints.user_id', '=', 'users.id')
                ->where('complaints.unit_id', $unitUser) // Filter berdasarkan unit_id
                ->where('complaints.status', 'processed') // Filter berdasarkan status forwarded
                ->select('complaints.*', 'units.nama as unit_name', 'users.name as user_name')
                ->get();
        } else {
            // Jika user tidak terkait dengan unit mana pun, kosongkan hasil
            $pesan_masuk = collect();
        }

        return $pesan_masuk;
    }

    public static function BalasPesanView($complaint_id)
    {
        // Ambil data complaint berdasarkan ID yang diberikan
        $complaint = Complaint::where('id', $complaint_id)->first();

        // Pastikan complaint ditemukan
        if (!$complaint) {
            return redirect()->back()->with('error', 'Keluhan tidak ditemukan.');
        }

        if ($complaint->status == 'completed') {
            return redirect()->back()->with('error', 'Keluhan ini sudah dibalas, tidak bisa mengakses halaman ini.');
        }
        $balasPesan = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
            ->join('users', 'complaints.user_id', '=', 'users.id')
            ->where('complaints.id', $complaint_id) // 🔹 Filter berdasarkan complaint_id
            ->select('complaints.*', 'units.nama as unit_name', 'users.name as user_name')
            ->first(); // 🔹 Mengambil satu data saja

        return $balasPesan;
    }

    public static function storeResponse($request)
    {
        // Validasi input
        // *🔹 Validasi di Model*
        $validator = Validator::make($request->all(), [
            'complaint_id' => 'required|exists:complaints,id',
            'unit_id' => 'required|exists:units,id',
            'response_text' => 'required|string',
        ], [
            'complaint_id.required' => 'Keluhan harus dipilih.',
            'unit_id.required' => 'Unit harus dipilih.',
            'response_text.required' => 'Balasan tidak boleh kosong.',
        ]);

        // Jika validasi gagal, kembalikan error
        if ($validator->fails()) {
            return [
                'status' => 'error',
                'errors' => $validator->errors(),
            ];
        }

        // Ambil data complaint berdasarkan complaint_id
        $complaint = Complaint::find($request->input('complaint_id'));

        // Jika complaint tidak ditemukan, kembalikan error
        if (!$complaint) {
            return redirect()->back()->with('error', 'Keluhan tidak ditemukan.');
        }

        // Cek apakah answer_status sudah bernilai 1
        if ($complaint->status == 'completed') {
            return redirect()->back()->with('error', 'Keluhan ini sudah dibalas, tidak bisa membalas lagi.');
        }

        // *🔹 Simpan ke Database*
        $response = responses::create([
            'complaint_id' => $request->input('complaint_id'),
            'unit_id' => $request->input('unit_id'),
            'response_text' => $request->input('response_text'),
            'reviewed_by' => Auth::id(),
            'sent_at' => now(),
            'status' => 'pending',
            'reviewed_at' => now(),
        ]);

        Complaint::where('id', $request->input('complaint_id'))
        ->update(['status' => 'completed', 'completed_at' => now()]);

        return $response;
    }

    public static function menampilkanlaporanKeluar() {
        $user = Auth::user();
    
        // Ambil unit dari user jika ada
        $unitUser = $user->unit_id; 
    
        // Cek apakah user memiliki unit_id
        if ($unitUser) {
            $pesan_keluar = responses::join('complaints', 'responses.complaint_id', '=', 'complaints.id') // Join ke complaints
                ->join('units', 'complaints.unit_id', '=', 'units.id') // Join ke units
                ->join('users as pelapor', 'complaints.user_id', '=', 'pelapor.id') // Join ke users
                ->join('users as reviewer', 'responses.reviewed_by', '=', 'reviewer.id')
                ->where('complaints.unit_id', $unitUser) // Filter berdasarkan unit_id
                ->where('complaints.status', 'completed') // Filter status forwarded
                ->select(
                    'responses.*', // Ambil semua data dari responses
                    'complaints.status as complaint_status', 
                    'units.nama as unit_name',
                    // Data pelapor
                    'pelapor.name as pelapor_name',
                    

                    // Data reviewer (yang melakukan review)
                    'reviewer.name as reviewer_name',
                )
                ->get();
        } else {
            // Jika user tidak memiliki unit_id, hasil kosong
            $pesan_keluar = collect();
        }
    
        return $pesan_keluar;
    }
}