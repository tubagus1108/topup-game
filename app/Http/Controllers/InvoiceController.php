<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Berita;
use Illuminate\Support\Carbon;
use App\Http\Controllers\TriPayController;
use App\Http\Controllers\TriPayCallbackController;
use Illuminate\Support\Facades\Cache;

class InvoiceController extends Controller
{
    public function create($order)
    {
        $cacheKey = 'pembelian_data:' . $order;

        $data = Cache::remember($cacheKey, 3600, function () use ($order) {
            return Pembelian::where('pembayarans.order_id', $order)
                ->join('pembayarans', 'pembelians.order_id', 'pembayarans.order_id')
                ->select(
                    'pembayarans.status AS status_pembayaran',
                    'pembayarans.metode AS metode_pembayaran',
                    'pembayarans.no_pembayaran',
                    'pembayarans.reference',
                    'pembelians.order_id AS id_pembelian',
                    'user_id',
                    'zone',
                    'nickname',
                    'layanan',
                    'pembayarans.harga AS harga_pembayaran',
                    'pembelians.created_at AS created_at',
                    'pembelians.status AS status_pembelian',
                    'pembayarans.reference',
                    'pembayarans.status AS status_pembayaran',
                    'pembelians.tipe_transaksi as status_transaksi'
                )
                ->first();
        });

        $expired = Carbon::create($data->created_at)->addDay();

        return view('template.invoice', [
            'data' => $data,
            'expired' => $expired,
            'logoheader' => Cache::remember('logoheader_data', 3600, function () {
                return Berita::where('tipe', 'logoheader')->latest()->first();
            }),
            'logofooter' => Cache::remember('logofooter_data', 3600, function () {
                return Berita::where('tipe', 'logofooter')->latest()->first();
            }),
        ]);
    }

}
