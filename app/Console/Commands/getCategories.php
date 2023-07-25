<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kategori;
use App\Http\Controllers\digiFlazzController;
use Illuminate\Support\Str;
class getCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $digiFlazz = new digiFlazzController;
        $res = $digiFlazz->harga();
        // dd($res);
        $arrGame = [];
        $arrPulsa = [];

        foreach ($res['data'] as $game) {
            if ($game['category'] == "Games") {
                $arrGame[] = $game['brand'];
            } else if ($game['category'] == "Pulsa") {
                $arrPulsa[] = $game['brand'];
            }
        }
        // dd($arrGame,$arrPulsa);

        $this->saveCategories(array_unique($arrGame), 'game');
        $this->saveCategories(array_unique($arrPulsa), 'pulsa');
    }

    private function saveCategories(array $brands, string $type)
    {
        foreach ($brands as $brand) {
            // dd([
            //     "nama" => $brand,
            //     'brand' => $brand,
            //     'tipe' => $type,
            //     "kode" => str_replace(' ', '-', Str::lower($brand)),
            //     "thumbnail" => null
            // ]);
            try {
                Kategori::firstOrCreate([
                    'nama' => $brand,
                    'tipe' => $type
                ], [
                    'brand' => Str::lower($brand),
                    'kode' => str_replace(' ', '-', Str::lower($brand)),
                    'thumbnail' => null
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }
    }


}
