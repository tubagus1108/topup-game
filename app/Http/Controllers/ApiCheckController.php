<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ApiCheckController extends Controller
{
    public function check($user_id = null, $zone_id = null, $game = null)
    {
        $api = DB::table('setting_webs')->where('id', 1)->first();
        $params = [
            'game' => $game,
            'user_id' => $user_id,
            'zone_id' => $zone_id
        ];

        // Create a unique cache key based on the parameters
        $cacheKey = 'check_' . implode('_', $params);

        // Check if data exists in Redis cache
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        } else {
            $result = $this->connect($params);

            // Store the data in Redis cache with a specific expiration time (in seconds)
            Cache::put($cacheKey, $result, 3600); // Cache data for 1 hour (3600 seconds)
        }

        if ($result['status'] == true) {
            return array(
                'status' => array('code' => 200),
                'data' => array('userNameGame' => $result['data']['username'])
            );
        } else {
            return array(
                'status' => array('code' => 1)
            );
        }
    }

    public function connect($data = null)
    {
        if (!is_array($data)) {
            return null; // Mengembalikan null jika $data bukan array
        }

        $game = isset($data['game']) ? $data['game'] : null;
        $user_id = isset($data['user_id']) ? $data['user_id'] : null;
        $zone_id = isset($data['zone_id']) ? $data['zone_id'] : null;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-cek-id-game.vercel.app/api/game/{$game}?id={$user_id}&zone={$zone_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $json_result = json_decode($response, true);
        return $json_result;
    }

}
