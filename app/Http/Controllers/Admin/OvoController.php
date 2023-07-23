<?php

namespace App\Http\Controllers\Admin;

require('Ovo.php');

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Namdevel\Ovo as Login;
use App\Models\Ovo;
use App\Models\History_Ovo;
use Exception;
use Illuminate\Support\Facades\Cache;

class OvoController extends Controller
{
    public function create()
    {
        $dataOVO = History_Ovo::select('*')->orderByDesc('tanggal_transaksi')->limit(10)->get();
        return view('components.admin.ovo-settings', [
            'user' => Auth::User(),
            'transaksi' => $dataOVO
        ]);
    }

    public function store(Request $request)
    {
        Ovo::insert(
            [
                'RefId' => $request->refID,
                'UpdateAccessToken' => $request->update_token,
                'AuthToken' => $request->auth_token,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return back()->with('status', 'Berhasil memasukkan ke database!');
    }

    public function GetOTP($no)
    {
        $init = new Login();
        $sendOTP = json_decode($init->sendOtp('+'.$no), true);
        $refId = $sendOTP['data']['otp']['otp_ref_id'];

        return response()->json([
            'status' => "True",
            'refID' => $refId
        ]);
    }

    public function VerifOTP(Request $request)
    {
        $init = new Login();
        $verifOTP = json_decode($init->OTPVerify('+'.$request->nomor, $request->refID, $request->otp), true);
        $accToken = $verifOTP['data']['otp']['otp_token'];

        return response()->json([
            'status' => "True",
            'updateToken' => $accToken
        ]);
    }

    public function VerifPIN(Request $request)
    {
        $init = new Login();
        $verifPIN = json_decode($init->getAuthToken('+'.$request->nomor, $request->refID, $request->update_token, $request->pin), true);
        $authToken = $verifPIN['data']['auth']['refresh_token'];

        return response()->json([
            'status' => "True",
            'auth_token' => $authToken,
        ]);
    }

    public function getTransaction()
    {
        $authToken = Cache::remember('ovo_auth_token', 3600, function () {
            return Ovo::select('AuthToken')->latest()->first();
        });

        $init = new Login($authToken->AuthToken);
        $menghapusTransaksiLama = History_Ovo::truncate();

        $dataHistory = Cache::remember('ovo_transaction_history', 3600, function () use ($init) {
            $history = [];
            foreach ($init->transactionHistory() as $data) {
                try {
                    foreach ($data->orders[0]->complete as $transaction => $key) {
                        $data = json_decode(json_encode($key), true);
                        $incomingTransfer = $data['transaction_type'];
                        if ($incomingTransfer == "INCOMING TRANSFER") { //cek apakah ada status incoming transfer jika ada push ke array
                            $history[] = [
                                'tanggal_transaksi' => $data['transaction_date'],
                                'jumlah_transaksi' => $data['transaction_amount'],
                                'tipe_transaksi' => $data['transaction_type'],
                                'keterangan' => $data['desc1']
                            ];
                        }
                    }
                } catch (Exception $e) {
                    // Log or handle the exception, if needed
                }
            }
            return $history;
        });

        // Store the processed transaction history in the database
        History_Ovo::insert($dataHistory);

        return back()->with('status', 'Berhasil refresh data');
    }

}