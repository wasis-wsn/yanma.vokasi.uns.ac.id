<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrafikController extends Controller
{
    public function diluarjadwal($tahun) {
        $data = DB::table('diluar_jadwals as a')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->leftJoin('ref_prodi as c', 'b.prodi', '=', 'c.id')
        ->select(DB::raw('count(a.id) as jumlah_data'), 'c.name as nama_prodi')
        ->whereYear('a.created_at', $tahun)
        ->groupBy('c.id')
        ->get();

        $prodi = [];
        $increment = 1;

        $jumlahData = $data->pluck('jumlah_data')->toArray();
        $data->each(function ($item) use (&$prodi, &$increment) {
            $prodi[$increment] = [
                'nama_prodi' => $item->nama_prodi,
                'jumlah_data' => $item->jumlah_data,
            ];
            $increment++;
        });
        $response = [
            'jumlah_data' => $jumlahData,
            'nama_prodi' => $prodi,
        ];
        return $response;
        // return response()->json($response, 200);
    }

    public function cuti($tahun) {
        $data = DB::table('selang_cutis as a')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->leftJoin('ref_prodi as c', 'b.prodi', '=', 'c.id')
        ->select(DB::raw('count(a.id) as jumlah_data'), 'c.name as nama_prodi')
        ->whereYear('a.created_at', $tahun)
        ->groupBy('c.id')
        ->get();

        $prodi = [];
        $increment = 1;

        $jumlahData = $data->pluck('jumlah_data')->toArray();
        $data->each(function ($item) use (&$prodi, &$increment) {
            $prodi[$increment] = [
                'nama_prodi' => $item->nama_prodi,
                'jumlah_data' => $item->jumlah_data,
            ];
            $increment++;
        });
        $response = [
            'jumlah_data' => $jumlahData,
            'nama_prodi' => $prodi,
        ];
        return $response;
        // return response()->json($response, 200);
    }

    public function surattugas($tahun) {
        $data = DB::table('surat_tugas as a')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->leftJoin('ref_prodi as c', 'b.prodi', '=', 'c.id')
        ->select(DB::raw('count(a.id) as jumlah_data'), 'c.name as nama_prodi')
        ->whereYear('a.created_at', $tahun)
        ->groupBy('c.id')
        ->get();

        $prodi = [];
        $increment = 1;

        $jumlahData = $data->pluck('jumlah_data')->toArray();
        $data->each(function ($item) use (&$prodi, &$increment) {
            $prodi[$increment] = [
                'nama_prodi' => $item->nama_prodi,
                'jumlah_data' => $item->jumlah_data,
            ];
            $increment++;
        });
        $response = [
            'jumlah_data' => $jumlahData,
            'nama_prodi' => $prodi,
        ];
        return $response;
        // return response()->json($response, 200);
    }
}
