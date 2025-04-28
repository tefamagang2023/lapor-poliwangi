<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Entities\Admin;
use Modules\Admin\Entities\BotStatus;
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
            'nomor' => 'required',
        ], [
            'nama.required' => 'Nama unit wajib diisi',
            'nomor.required' => 'Nomor unit wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $unit = Unit::create([
            'nama' => $request->nama,
            'nomor' => $request->nomor,
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
            'nomor' => 'required',
        ], [
            'nama.required' => 'Nama unit wajib diisi',
            'nomor.required' => 'Nomor unit wajib diisi',
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
            'nomor' => $request->nomor,
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
        // dd($pesan_masuk);
        return view('admin::pesan-masuk', compact('pesan_masuk'));
    }

    public function teruskan($id)
    {
        $laporan = Complaint::with('unit')->findOrFail($id);

        // Ambil nomor WhatsApp dari unit
        $nomor = $laporan->unit->nomor;
        // Jika nomor unit tidak ada, kembalikan pesan error
        if (!$nomor) {
            return back()->with('error', 'Nomor unit tidak ditemukan');
            }

        $pesan =   "Halo, terdapat laporan dari : 
                    nama pelapor : {$laporan->nama_pelapor}
                    nomor pelapor : {$laporan->nomor_pelapor}
                    {$laporan->complaint_text}.

                    Jika masalah sudah teratasi, tolong balas menggunakan template dibawah ini:

                    nama pelapor : {$laporan->nama_pelapor}
                    nomor pelapor : {$laporan->nomor_pelapor}
                    laporan : {$laporan->complaint_text}
                    balasan : [isi balasan kamu di sini]";

        // Cek apakah ada media dan file-nya benar-benar ada
            $adaGambar = false;
            if ($laporan->file_path) {
                $filePath = public_path('media/' . $laporan->file_path);
                if (file_exists($filePath)) {
                    $adaGambar = true;
                } else {
                    return back()->with('error', 'File media tidak ditemukan di ' . $filePath);
                }
            }

            // Siapkan endpoint dan payload
            $endpoint = $adaGambar
                ? "http://localhost:3000/send-image/{$laporan->id}"
                : 'http://localhost:3000/send-message';

            $payload = [
                'nomor' => $nomor,
                'pesan' => $pesan,
            ];

            try {
                $response = Http::post($endpoint, $payload);

                if ($response->successful()) {
                    $laporan->status = 'processed';
                    $laporan->user_id = Auth::id();
                    $laporan->processed_at = now('Asia/Jakarta');
                    $laporan->save();

                    return back()->with('success', 'Pesan berhasil diteruskan');
                } else {
                    return back()->with('error', 'Gagal mengirim pesan');
                }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
            'date_replied_by' => now('Asia/Jakarta'),
            'reply_text' => $request->reply_text,
            'updated_at' => now('Asia/Jakarta'),
        ]);

        $complaint = Complaint::find($id);

        // dd($complaint);

        $balasan = $request->reply_text;
        $nomorPelapor = $complaint->nomor_pelapor;
        $complaintText = $complaint->complaint_text;
        $pelaporNama = $complaint->nama_pelapor;

        $text = "Halo {$pelaporNama},\n\n".
                    "Berikut adalah balasan atas keluhan anda yaitu\n".
                    "{$complaintText}\n".
                    "Balasan sementara dari admin\n".
                    "{$balasan}\n\n".
                    "Tolong jawab menggunakan template dibawah ini\n".
                    "Jawaban dari saya adalah\n".
                    "[Jawaban Anda]";

            try {
                $send = Http::post('http://localhost:3000/send-whatsapp', [
                    'nomor' => $nomorPelapor,
                    'pesan' => $text,
                ]);
    
                if ($send->successful()) {
                    // ✅ Tambahkan logika update status
                    // $response->status = '1';
                    // $response->save();
    
                    return back()->with('success', 'Berhasil membalas pesan');
                } else {
                    return back()->with('error', 'Gagal mengirim pesan.');
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }

            // return redirect()->away("https://api.whatsapp.com/send?phone=" . $nomorPelapor . "&text=" . $text);


        // return redirect()->back()->with('success', 'Balasan berhasil dikirim!');
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

    public function sendWa($id)
    {
        $response = responses::with('complaint', 'unit')
            ->where('id', $id)
            ->latest()
            ->first();

        // Ambil nomor WhatsApp dari unit
        $nomor = $response->complaint->nomor_pelapor;
        $pesan =    "Halo {$response->complaint->nama_pelapor},\n".
                    "Saya admin LAPOR Poliwangi mengirim konfirmasi balasan dari laporan Anda:\n".
                    "\"{$response->complaint->complaint_text}\"\n\n".
                    "Laporan tersebut sudah dibalas oleh unit {$response->unit->nama}.\n".
                    "Berikut balasannya:\n".
                    "\"{$response->response_text}\"\n\n".
                    "Terima kasih telah melapor 🙏";

        try {
            $send = Http::post('http://localhost:3000/send-whatsapp', [
                'nomor' => $nomor,
                'pesan' => $pesan,
            ]);

            if ($send->successful()) {
                // ✅ Tambahkan logika update status
                $response->status = '1';
                $response->save();

                return back()->with('success', 'Pesan berhasil diteruskan dan status laporan diperbarui.');
            } else {
                return back()->with('error', 'Gagal mengirim pesan.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // public function index()
    // {
    //     $status = BotStatus::firstOrCreate([], ['is_active' => 'inactive']);

    //     $qrPath = public_path('assets/qr/qr.png');
    //     $botRunning = isBotRunning();

    //     return view('admin::bot', [
    //         'is_active' => $status->is_active,
    //         'qr_exists' => file_exists($qrPath),
    //         'qr_url' => asset('assets/qr/qr.png'),
    //         'bot_running' => $botRunning
    //     ]);
    // }

    // public function toggle()
    // {
    //     $status = BotStatus::first();

    //     if ($status->is_active === 'inactive') {
    //         // Jika bot dalam keadaan nonaktif, nyalakan bot
    //         $node = '"C:\Program Files\nodejs\node.exe"';
    //         $script = base_path('resources/js/bot.js');
            
    //         // Eksekusi script dengan shell_exec
    //         shell_exec("{$node} {$script} --from-laravel > NUL 2>&1 &");

    //         $status->is_active = 'active';  // Ubah status jadi aktif
    //     } else {
    //         // ❌ Matikan proses bot (kalau pakai pm2 lebih bagus)
    //         // pkill tidak jalan di Windows, kamu bisa pakai taskkill sebagai alternatif:
    //         // taskkill /F /IM node.exe /T
    //         killBot();

    //         $status->is_active = 'inactive';  // Ubah status jadi nonaktif
    //     }

    //     $status->save();

    //     return back()->with('success', 'Bot berhasil di-' . ($status->is_active === 'active' ? 'nyalakan' : 'matikan'));
    // }

}
