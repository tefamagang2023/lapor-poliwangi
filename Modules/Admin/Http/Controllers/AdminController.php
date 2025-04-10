<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Entities\Admin;
use Modules\Admin\Entities\Unit;
use Modules\Pelapor\Entities\Complaint;
use Modules\UnitPoliwangi\Entities\responses;
use Modules\UnitPoliwangi\Entities\Unit as EntitiesUnit;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index_unit()
    {
        $unit = Unit::all();
        return view('admin::index-unit', compact('unit'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store_unit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ], [
            'nama.required' => 'Nama unit wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $unit = Unit::create([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Unit berhasil ditambahkan',
            'data' => $unit,
        ], 201);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function edit_unit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ], [
            'nama.required' => 'Nama unit wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $unit = Unit::findOrFail($id);
        $unit->update([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Unit berhasil diubah',
            'data' => $unit,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function hapus_unit($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return response()->json(['success' => 'Unit berhasil dihapus']);
    }

    public function pesan() {
        $pesan = Admin::menampilkanLaporan();
        return view('admin::pesan', compact('pesan'));
    }

    public function pesan_masuk()
    {
        $pesan_masuk = Admin::menampilkanLaporanMasuk(); 
        return view('admin::pesan-masuk', compact('pesan_masuk'));
    }

    public function pesan_keluar()
    {
        $pesan_keluar = Admin::menampilkanLaporanKeluar(); 
        return view('admin::pesan-keluar', compact('pesan_keluar'));
    }

    public function pesan_masuk_unit()
    {
        $pesan_masuk_upt = Admin::menampilkanLaporanMasukUnit(); 
        return view('admin::pesan-masuk-unit', compact('pesan_masuk_upt'));
    }

    public function balas(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reply_text' => 'required',
        ], [
            'reply_text.required' => 'Balasan wajib diisi',
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

        if (!is_null($pelapor->date_replied_by)) {
            return redirect()->back()->with('error', 'Keluhan ini sudah dibalas, tidak bisa membalas lagi.');
        }

        // Update hanya kolom reply_text
        $pelapor->update([
            'replied_by' => Auth::id(),
            'date_replied_by' => now(),
            'reply_text' => $request->reply_text,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Balasan berhasil dikirim!');
    }

    public function teruskanLaporan($id) {
        $laporan = Admin::mengirimLaporan($id);
    
        if (isset($laporan['status']) && $laporan['status'] === 'error') {
            return redirect()->back()->with('error', $laporan['message']);
        }
    
        return redirect()->back()->with('success', 'Laporan berhasil dikirim!');
    }

    public function pesan_keluar_unit()
    {
        $pesan_keluar = Admin::menampilkanLaporanKeluarUnit(); 
        return view('admin::pesan-keluar-unit', compact('pesan_keluar'));
    }

    public function sendWhatsApp($complaint_id)
    {
        $waLink = Admin::RedirecWa($complaint_id);

        if ($waLink) {
            return redirect()->away($waLink);
        } else {
            return back()->with('error', 'Data tidak ditemukan atau nomor tidak tersedia.');
        }
    }

    
}
