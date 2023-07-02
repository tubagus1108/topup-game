<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kategori;
use App\Http\Controllers\digiFlazzController;
use Str;
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

        foreach($res['data'] as $game){
            if($game['category'] == "Games"){
                array_push($arrGame,$game['brand']);
            }else if($game['category'] == "Pulsa"){
                array_push($arrPulsa, $game['brand']);
            }
        }
        // dd($arrGame,$arrPulsa);
        foreach(array_unique($arrGame) as $game){
            try{
                $category = new Kategori();
                $category->nama = $game;
                $category->brand = Str::lower($game);
                $category->kode = $game = str_replace(' ', '-', Str::lower($game));
                $category->thumbnail = null;
                $category->tipe = "game";
                // dd($category);
                $category->save();
            }catch(\Exception $e){
                continue;
            }
        }

        foreach(array_unique($arrPulsa) as $pulsa){
            try{
                $category = new Kategori();
                $category->nama = $pulsa;
                $category->brand = Str::lower($pulsa);
                $category->kode = $pulsa = str_replace(' ', '-', Str::lower($pulsa));
                $category->thumbnail = null;
                $category->tipe = "pulsa";
                // dd($category);
                $category->save();
            }catch(\Exception $e){
                continue;
            }
        }
    }
}
