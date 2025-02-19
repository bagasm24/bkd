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
        Schema::create('sdms', function (Blueprint $table) {
            $table->uuid('id_sdm')->primary();
            $table->string('nama_sdm');
            $table->string('nidn')->nullable();
            $table->string('nip')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('nama_status_aktif');
            $table->string('nama_status_pegawai');
            $table->string('jenis_sdm');
            $table->string('jabatan_fungsional')->nullable();
            $table->decimal('angka_kredit')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->string('sk')->nullable();
            $table->string('rekomendasi')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sdms');
    }
};
