<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Pembayaran;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function create()
    {
        $date = now();
        $dayPlusOne = Carbon::Create($date)->addDays(1);
        $pastWeek = Carbon::create($date)->subWeeks(1);
        $parsingDate = Carbon::parse($date);

        $grafik = Pembelian::whereBetween('created_at', [$pastWeek, $dayPlusOne])->orderBy('created_at', 'asc')->get();
        $morrisData = $this->getMorrisData($grafik);

        $totalPembelian = $this->calculatePembelian($parsingDate, 'sum');
        $banyakPembelian = $this->calculatePembelian($parsingDate, 'count');

        $totalPembelianSuccess = $this->calculatePembelian($parsingDate, 'sum', 'Sukses');
        $banyakPembelianSuccess = $this->calculatePembelian($parsingDate, 'count', 'Sukses');

        $totalPembelianBatal = $this->calculatePembelian($parsingDate, 'sum', 'Batal');
        $banyakPembelianBatal = $this->calculatePembelian($parsingDate, 'count', 'Batal');

        $totalPembelianPending = $this->calculatePembelian($parsingDate, 'sum', 'Pending');
        $banyakPembelianPending = $this->calculatePembelian($parsingDate, 'count', 'Pending');

        $totalPembayaran = $this->calculatePembayaran($parsingDate, 'sum', 'Lunas');
        $banyakPembayaran = $this->calculatePembayaran($parsingDate, 'count', 'Lunas');

    return view('components.admin.dashboard', [
            'total_pembelian' => $totalPembelian,
            'total_pembelian_batal' => $totalPembelianBatal,
            'total_pembelian_pending' => $totalPembelianPending,
            'total_pembelian_success' => $totalPembelianSuccess,
            'banyak_pembelian' => $banyakPembelian,
            'banyak_pembelian_batal' => $banyakPembelianBatal,
            'banyak_pembelian_pending' => $banyakPembelianPending,
            'banyak_pembelian_success' => $banyakPembelianSuccess,
            'total_deposit' => $totalPembayaran,
            'banyak_deposit' => $banyakPembayaran,
            'total_keseluruhan_pembelian' => Pembelian::sum('harga'),
            'banyak_keseluruhan_pembelian' => Pembelian::count(),
            'total_keseluruhan_pembelian_berhasil' => Pembelian::where('status', 'Sukses')->sum('harga'),
            'banyak_keseluruhan_pembelian_berhasil' => Pembelian::where('status', 'Sukses')->count(),
            'total_keseluruhan_pembelian_pending' => Pembelian::where('status', 'Pending')->sum('harga'),
            'banyak_keseluruhan_pembelian_pending' => Pembelian::where('status', 'Pending')->count(),
            'total_keseluruhan_pembelian_batal' => Pembelian::where('status', 'Batal')->sum('harga'),
            'banyak_keseluruhan_pembelian_batal' => Pembelian::where('status', 'Batal')->count(),
            'total_keseluruhan_deposit' => Pembayaran::where('status', 'Lunas')->sum('harga'),
            'banyak_keseluruhan_deposit' => Pembayaran::where('status', 'Lunas')->count(),
            'keuntungan_bersih' => Pembelian::where('status', 'Sukses')->sum('profit'),
            'morris_data' => json_encode($morrisData)
        ]);
    }

    protected function calculatePembelian($date, $type, $status = null)
    {
        // Key for the cache
        $key = "calculatePembelian_{$date->day}_{$date->month}_{$date->year}_{$type}_{$status}";

        // If the key exists in the cache, return the cached value
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $query = Pembelian::whereDay('created_at', $date->day)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year);

        if ($status) {
            $query = $query->where('status', $status);
        }

        // Compute the value and store it in the cache for 60 minutes
        $value = $query->$type('harga');
        Cache::put($key, $value, 60);

        return $value;
    }

    protected function calculatePembayaran($date, $type, $status)
    {
        // Key for the cache
        $key = "calculatePembayaran_{$date->day}_{$date->month}_{$date->year}_{$type}_{$status}";

        // If the key exists in the cache, return the cached value
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        // Compute the value and store it in the cache for 60 minutes
        $value = Pembayaran::whereDay('created_at', $date->day)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->where('status', $status)
            ->$type('harga');
        Cache::put($key, $value, 60);

        return $value;
    }

    protected function getMorrisData($grafik)
    {
        // Key for the cache
        $key = "getMorrisData_" . md5($grafik->toJson());

        // If the key exists in the cache, return the cached value
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $arrayDate = [];
        foreach($grafik as $data){
            $pembelian = Pembelian::whereDate('created_at', $data->created_at)->count();
            array_push($arrayDate,array('y' => Carbon::parse($data->created_at)->isoFormat('D/M/Y'), 'a' => $pembelian));
        }

        // Store the array in the cache for 60 minutes
        Cache::put($key, $arrayDate, 60);

        return $arrayDate;
    }

}
