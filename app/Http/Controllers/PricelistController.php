<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Berita;
use Illuminate\Support\Facades\Cache;

class PricelistController extends Controller
{
    public function create()
    {
        $datas = Cache::remember('pricelist_data', 3600, function () {
            return Layanan::join('kategoris', 'layanans.kategori_id', 'kategoris.id')
                ->where('kategoris.status', 'active')
                ->orderBy('created_at', 'desc')
                ->select('layanans.*', 'kategoris.nama AS nama_kategori')
                ->get();
        });

        $kategori = Cache::remember('kategori_data', 3600, function () {
            return Kategori::get();
        });

        return view('template.pricelist', [
            'datas' => $datas,
            'kategoris' => $kategori,
            'logoheader' => Cache::remember('logoheader_data', 3600, function () {
                return Berita::where('tipe', 'logoheader')->latest()->first();
            }),
            'logofooter' => Cache::remember('logofooter_data', 3600, function () {
                return Berita::where('tipe', 'logofooter')->latest()->first();
            }),
            'pay_method' => Cache::remember('pay_method_data', 3600, function () {
                return \App\Models\Method::all();
            })
        ]);
    }


}