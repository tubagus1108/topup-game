<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DsController extends Controller
{
    public function dashboard()
    {
        $data = Cache::remember('dashboard_data:' . Auth::user()->username, 3600, function () {
            return \App\Models\Pembelian::where('username', Auth::user()->username)->get();
        });

        return view('template.dashboard', [
            'data' => $data,
            'logoheader' => Cache::remember('logoheader_data', 3600, function () {
                return Berita::where('tipe', 'logoheader')->latest()->first();
            }),
            'logofooter' => Cache::remember('logofooter_data', 3600, function () {
                return Berita::where('tipe', 'logofooter')->latest()->first();
            }),
        ]);
    }

    public function editProfile()
    {
        return view('template.profile', [
            'logoheader' => Cache::remember('logoheader_data', 3600, function () {
                return Berita::where('tipe', 'logoheader')->latest()->first();
            }),
            'logofooter' => Cache::remember('logofooter_data', 3600, function () {
                return Berita::where('tipe', 'logofooter')->latest()->first();
            }),
        ]);
    }
 
    public function saveEditProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|min:3|max:255|unique:users,username,'.Auth()->user()->id,
            'password' => 'nullable|min:6|max:255',
            'no_wa' => 'required|numeric|unique:users,no_wa,'.Auth()->user()->id
        ], [
            'nama.required' => 'Harap isi kolom nama!',
            'username.required' => 'Harap isi kolom username!',
            'username.min' => 'Panjang username minimal 3 huruf',
            'username.unique' => 'Username telah digunakan',
            'username.max' => 'Panjang username maximal 255 huruf',
            'password.min' => 'Panjang password minimal 6 huruf',
            'password.max' => 'Panjang password maximal 255 huruf',
            'no_wa.required' => 'Harap isi no whatsapp!',
            'no_wa.numeric' => 'No whatsapp tidak valid!',
            'no_wa.unique' => 'No whatsapp telah digunakan'
        ]);

        
        $data = [
          'name' => $request->name,
          'username' => $request->username,
          'no_wa' => $request->no_wa
        ];
        
        if(!empty($request->password)){
            
            $data['password'] = bcrypt($request->password);
            
        }
        // Update the user's profile
        \App\Models\User::where('id', Auth()->user()->id)->update($data);

        // Clear the cache for the user's dashboard data
        Cache::forget('dashboard_data:' . Auth::user()->username);

        return redirect()->back()->with('success', 'Berhasil mengedit profile!');

    }
    
    
}