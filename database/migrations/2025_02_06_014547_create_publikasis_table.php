<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publikasis', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_publikasi');
            $table->uuid('id_sdm');
            $table->text('judul');
            $table->string('nama_jurnal')->nullable();
            $table->date('tanggal')->nullable();
            $table->foreignId('jenis_publikasi')->constrained('jenis_publikasis')->onDelete('cascade');
            $table->integer('urutan_penulis');
            $table->foreignId('kategori_kegiatan')->constrained('kategori_kegiatans')->onDelete('cascade');
            $table->string('penerbit')->nullable();
            $table->string('asal_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publikasis');
    }
};
