<?php

namespace Modules\Pelapor\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Entities\Unit;
use Modules\UnitPoliwangi\Entities\responses;
use Modules\Pelapor\Entities\Complaint;

class PelaporController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('pelapor::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $unit_id = Unit::all(); // Mengambil semua data dari tabel units
        return view('pelapor::create', compact('unit_id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
            $pelapor = Complaint::tambahlaporan($request);
            if ($pelapor['status'] == 'error') {
                // Jika ada error validasi, kembali ke form dengan error
                return redirect()->back()
                    ->withErrors($pelapor['errors'])
                    ->withInput();
            }
            return redirect()->back()->with('success', 'Laporan berhasil diajukan!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('pelapor::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('pelapor::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function pesanMasuk() {
        $userId = Auth::id();

        $pesan_masuk = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
            ->leftJoin('users as pelapor_user', 'complaints.user_id', '=', 'pelapor_user.id') // Join untuk pelapor
            ->leftJoin('users as replied_user', 'complaints.replied_by', '=', 'replied_user.id') // Join untuk yang membalas
            ->whereNotNull('complaints.reply_text') // Hanya tampilkan yang sudah dibalas
            ->where('complaints.status', 'pending')
            ->where('complaints.nama_pelapor', Auth::user()->name)
            ->whereNotNull('complaints.date_replied_by') 
            ->select(
                'complaints.*', 
                'units.nama as unit_name', 
                'pelapor_user.name as user_name', 
                'replied_user.name as replied_name' // Nama yang membalas
            )
            ->get();
    
        return view('pelapor::pesan-masuk', compact('pesan_masuk'));
    }

    public function balas_pelapor(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'complain_text' => 'required',
        ], [
            'complain_text.required' => 'Balasan wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Ambil data pelapor berdasarkan ID
        $pelapor = Complaint::find($id);

        if (!$pelapor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pelapor tidak ditemukan',
            ], 404);
        }

        // Cek apakah reply_pelapor sudah bernilai 1
        if (!is_null($pelapor->date_reply_pelapor)) {
            return redirect()->back()->with('error', 'Anda sudah membalas, tidak bisa membalas lagi.');
        }

        // Update hanya kolom reply_text
        $pelapor->update([
            'complaint_text' => $request->complain_text,
            'date_reply_pelapor' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Balasan berhasil dikirim!');
    }

    public function pesanKeluar() {
        $userId = Auth::id();

        $pesan_keluar = Complaint::join('units', 'complaints.unit_id', '=', 'units.id')
            ->leftJoin('users as pelapor_user', 'complaints.user_id', '=', 'pelapor_user.id') // Join untuk pelapor
            ->leftJoin('users as replied_user', 'complaints.replied_by', '=', 'replied_user.id') // Join untuk yang membalas
            ->whereNotNull('complaints.date_reply_pelapor')
            ->where('complaints.nama_pelapor', Auth::user()->name)
            ->select(
                'complaints.*', 
                'complaints.nama_pelapor',
                'units.nama as unit_name', 
                'pelapor_user.unit_id', 
                'replied_user.name as replied_name' // Nama yang membalas
            )
            ->get();

            // dd($pesan_keluar);
    
        return view('pelapor::pesan-keluar', compact('pesan_keluar'));
    }
}
