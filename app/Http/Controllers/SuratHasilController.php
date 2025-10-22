<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuratHasilController extends Controller
{
    // Minimal stub methods so route:list and reflection succeed
    public function index()
    {
        return view('pages.surat_hasil.index');
    }

    public function list(Request $request)
    {
        // return empty JSON for DataTables placeholder
        return response()->json(['data' => []]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['status' => true]);
    }
}
