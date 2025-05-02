<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\PembinaOrmawa;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahuns = Tahun::select('tahun')->orderBy('tahun', 'desc')->get();
        return view('pages.dashboard.index', compact('tahuns'));
    }

    public function search(Request $request)
    {
        $q = $request->q;
        $data = in_array(Auth::user()->role, ['1', '5','7']) ? Layanan::select('id', 'name', 'url_mhs as url') : Layanan::select('id', 'name', 'url_staff as url');
        $data = $data->where('is_active', '1')->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($q) . '%'])->limit(5)->get();
        return response()->json($data, 200);
    }

    function get_mhs(Request $request)
    {
        $q = $request->q;
        if (strlen($q) >= 3) {
            return User::with('prodis')
                ->where('role', '1')
                ->where(function ($query) use ($q) {
                    $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%')
                        ->orWhere(DB::raw('LOWER(nim)'), 'LIKE', '%' . strtolower($q) . '%');
                        // ->orWhereHas('prodis', function ($query) use ($q) {
                        //     $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%');
                        // });
                })
                ->orderBy('nim')
                ->limit(3)
                ->get();
        }
        return null;
    }

    public function get_prodi(Request $request)
    {
        $q = $request->q;
        if (strlen($q) >= 3) {
            return Prodi::where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%')
                ->orderBy('name')
                ->limit(5)
                ->get();
        }
        return null;
    }

    public function get_dosen(Request $request)
    {
        $q = $request->q;
        if (strlen($q) >= 3) {
            return PembinaOrmawa::with('prodi')
                ->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%')
                ->orderBy('name')
                ->limit(5)
                ->get();
        }
        return null;
    }
}
