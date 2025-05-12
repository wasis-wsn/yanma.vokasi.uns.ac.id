<?php

namespace App\Http\Controllers;

use App\Models\KeringananUKT;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KeringananUKTController extends Controller
{
    public function landingPage()
    {
        $ukt = KeringananUKT::all();
        $ukt = $ukt->toArray();
        $index_tgl = ['pengajuan', 'verif_fakultas', 'verif_univ'];
        foreach ($ukt as $key => $u) {
            foreach ($index_tgl as $index) {
                $tanggalArray = $u[$index] ? explode(' to ', $u[$index]) : '';
                $tanggalAwal = $tanggalArray ? Carbon::createFromFormat('d-M-Y', $tanggalArray[0])->translatedFormat('d F') : '';
                $tanggalAkhir = $tanggalArray ? Carbon::createFromFormat('d-M-Y', $tanggalArray[1])->translatedFormat('d F Y') : '';
                $u[$index] = $tanggalAwal . ' - ' . $tanggalAkhir;
            }
            $ukt[$key] = $u;
        }
        
        return view('landingpage.ukt.index', compact('ukt'));
    }

    public function index()
    {
        $ukt = KeringananUKT::all();
        return view('pages.ukt.index', compact('ukt'));
    }

    public function show(Request $request)
    {
        $ukt = KeringananUKT::where('jenis', $request->jenis)->first();
        $ukt = $ukt->toArray();
        $ukt['id'] = encodeId($ukt['id']);
        return response()->json(['status' => true, 'data' => $ukt], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => ['required'],
            'persyaratan' => ['required'],
            'pengajuan' => ['required'],
            'verif_fakultas' => ['required'],
            'verif_univ' => ['required'],
        ]);

        try {
            $id = decodeId($id);
            KeringananUKT::findOrFail($id)->update($request->input());
            return response()->json(['status' => true, 'message' => 'Data berhasil diperbarui!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'terjadi kesalahan'], 500);
        }
    }
}
