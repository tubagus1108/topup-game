<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Berita;
use Illuminate\Support\Facades\Cache;

class CariController extends Controller
{
    
    public function create()
    {
        $logoHeader = Cache::remember('logoheader_data', 3600, function () {
            return Berita::where('tipe', 'logoheader')->latest()->first();
        });

        $logoFooter = Cache::remember('logofooter_data', 3600, function () {
            return Berita::where('tipe', 'logofooter')->latest()->first();
        });

        return view('template.history', [
            'logoheader' => $logoHeader,
            'logofooter' => $logoFooter,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $pembelian = Cache::remember('pembelian_data:' . $request->id, 3600, function () use ($request) {
            return Pembelian::where('order_id', $request->id)->first();
        });

        if ($pembelian) {
            return redirect(route('pembelian', ['order' => $request->id]));
        }

        return back()->with('error', 'Pesanan tidak ditemukan');
    }

}
