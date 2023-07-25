<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_id');
            $table->string('layanan')->nullable();
            $table->string('provider_id')->nullable();
            $table->unsignedBigInteger('harga')->nullable();
            $table->unsignedBigInteger('harga_member')->nullable();
            $table->unsignedBigInteger('harga_platinum')->nullable();
            $table->unsignedBigInteger('harga_gold')->nullable();
            $table->integer('profit')->nullable();
            $table->integer('profit_member')->nullable();
            $table->integer('profit_platinum')->nullable();
            $table->integer('profit_gold')->nullable();
            $table->longText('catatan')->nullable();
            $table->string('status')->nullable();
            $table->string('provider')->nullable();
            $table->string('product_logo')->nullable();
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
        Schema::dropIfExists('layanans');
    }
}
