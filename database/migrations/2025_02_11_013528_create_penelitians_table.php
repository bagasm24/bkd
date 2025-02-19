<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penelitians', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Menggunakan UUID sebagai primary key
            $table->uuid('id_penelitian');
            $table->uuid('id_sdm');
            $table->string('judul');
            $table->json('bidang_keilmuan'); // Disimpan dalam format JSON
            $table->integer('tahun_pelaksanaan');
            $table->integer('lama_kegiatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penelitians');
    }
};
