<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingWebsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_webs', function (Blueprint $table) {
            $table->id();
            $table->text('judul_web')->default("WEBSITE TOP UP GAMING");
            $table->text('deskripsi_web')->default("Top Up Games, Voucher & PPOB Online 24 Jam");
            $table->text('logo_header')->nullable();
            $table->text('logo_footer')->nullable();
            $table->text('logo_favicon')->nullable();
            $table->text('url_wa');
            $table->text('url_ig');
            $table->text('url_tiktok');
            $table->text('url_youtube');
            $table->text('url_fb');
            $table->text('topupindo_api');
            $table->text('warna1')->default("#141414");
            $table->text('warna2')->default("#141414");
            $table->text('warna3')->default("#212121");
            $table->text('warna4')->default("#E7E7E7");
            $table->text('tripay_api')->nullable();
            $table->text('tripay_merchant_code')->nullable();
            $table->text('tripay_private_key')->nullable();
            $table->text('username_digi')->nullable();
            $table->text('api_key_digi')->nullable();
            $table->text('apigames_secret')->nullable();
            $table->text('apigames_merchant')->nullable();
            $table->text('vip_apiid')->nullable();
            $table->text('vip_apikey')->nullable();
            $table->text('nomor_admin')->nullable();
            $table->text('wa_key')->nullable();
            $table->text('wa_number')->nullable();
            $table->text('ovo_admin')->nullable();
            $table->text('ovo1_admin')->nullable();
            $table->text('gopay_admin')->nullable();
            $table->text('gopay1_admin')->nullable();
            $table->text('dana_admin')->nullable();
            $table->text('shopeepay_admin')->nullable();
            $table->text('bca_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_webs');
    }
}
