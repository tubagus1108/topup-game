<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Berita;

class LoginController extends Controller
{
    public function create()
    {
        $logoHeader = Cache::remember('logoheader', now()->addMinutes(60), function () {
            return Berita::where('tipe', 'logoheader')->latest()->first();
        });

        $logoFooter = Cache::remember('logofooter', now()->addMinutes(60), function () {
            return Berita::where('tipe', 'logofooter')->latest()->first();
        });

        return view('template.login', compact('logoHeader', 'logoFooter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $no = $request->username;

        if (is_numeric($no)) {
            if ($no[0] == 0) {
                $no = str_replace($no[0], '62', $no);
            }
        }

        $fieldType = is_numeric($request->username) ? 'no_wa' : 'username';

        if (Auth::attempt([$fieldType => $no, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->with('error', 'Tidak ada kecocokan data yang anda masukkan dengan Database kami!');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
