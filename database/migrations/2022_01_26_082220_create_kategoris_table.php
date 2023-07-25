<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKategorisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama');
            $table->string('sub_nama')->nullable();
            $table->string('kode')->nullable();
            $table->text('brand')->nullable();
            $table->tinyInteger('server_id')->default(0);
            $table->string('status')->default('active');
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();
            $table->string('tipe')->default('game');
            $table->string('petunjuk')->nullable();
            $table->text('deskripsi_game')->nullable();
            $table->text('deskripsi_field')->nullable();
            $table->text('placeholder_id')->nullable()->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->text('placeholder_server')->nullable()->charset('utf8mb4')->collation('utf8mb4_general_ci');
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
        Schema::dropIfExists('kategoris');
    }
}

