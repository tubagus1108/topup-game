<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Berita;
use Illuminate\Support\Facades\Cache;

class indexController extends Controller
{
    public function create()
    {
        // Check if data exists in Redis cache
        if (Cache::has('template_data')) {
            $data = Cache::get('template_data');
        } else {
            $data = [
                'kategori' => Kategori::where('status', 'active')->get(),
                'banner' => Berita::where('tipe', 'banner')->get(),
                'logoheader' => Berita::where('tipe', 'logoheader')->latest()->first(),
                'logofooter' => Berita::where('tipe', 'logofooter')->latest()->first(),
                'popup' => Berita::where('tipe', 'popup')->latest()->first(),
                'pay_method' => \App\Models\Method::all()
            ];
            // Store the data in Redis cache with a specific expiration time (in seconds)
            Cache::put('template_data', $data, 3600); // Cache data for 1 hour (3600 seconds)
        }
        
        return view('template.index', $data);
    }
   
    public function cariIndex(Request $request)
    {
        if ($request->ajax()) {
            $cacheKey = 'search_results_' . $request->data;

            // Check if search results exist in Redis cache
            if (Cache::has($cacheKey)) {
                $res = Cache::get($cacheKey);
            } else {
                $data = Kategori::where('nama', 'LIKE', '%' . $request->data . '%')
                    ->where('status', 'active')
                    ->limit(6)
                    ->get();

                $res = '';
                foreach ($data as $d) {
                    $res .= '
                        <li>
                            <a class="dropdown-item" style="color:#dee2e6" href="' . url("/order") . '/' . $d->kode . '">
                                <div class="row">
                                    <div class="col-3">
                                        <img src="' . $d->thumbnail . '" alt="" class="img-fluid">
                                    </div>
                                    <div class="col-9">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>' . $d->nama . '</b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    ';
                }

                // Store the search results in Redis cache with a specific expiration time (in seconds)
                Cache::put($cacheKey, $res, 3600); // Cache data for 1 hour (3600 seconds)
            }

            return $res;
        }
    }

}
