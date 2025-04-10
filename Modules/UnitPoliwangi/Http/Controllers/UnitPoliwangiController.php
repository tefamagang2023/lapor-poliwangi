<?php

namespace Modules\UnitPoliwangi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\UnitPoliwangi\Entities\responses;

class UnitPoliwangiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('unitpoliwangi::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('unitpoliwangi::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('unitpoliwangi::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('unitpoliwangi::edit');
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


    public function pesanMasuk()
    {
        $pesan_masuk = responses::menampilkanlaporan(); 
        return view('unitpoliwangi::pesanmasuk', compact('pesan_masuk'));
    }

    public function balasPesan($complaint_id) {
        $balasPesan = responses::BalasPesanView($complaint_id);
    
        if (!$balasPesan) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }
        return view('unitpoliwangi::balasPesan', compact('balasPesan'));
    }

    public function store(Request $request)
    {
        // *🔹 Kirim Data ke Model*
        $response = responses::storeResponse($request);

        if (!$response) {
            return [
                'status' => 'error',
                'errors' => ['Keluhan tidak ditemukan.'],
            ];
        }

        return redirect()->route('pesanMasuk')->with('success', 'Laporan berhasil ditanggapi!');
    }

    public function pesan_keluar()
    {
        $pesan_keluar = responses::menampilkanlaporanKeluar(); 
        return view('unitpoliwangi::pesankeluar', compact('pesan_keluar'));
    }

}