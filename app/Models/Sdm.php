<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sdm extends Model
{
    use HasFactory;

    // Nama tabel (opsional jika nama model tidak jamak)
    protected $table = 'sdms';

    // Primary key
    protected $primaryKey = 'id_sdm';

    // Jika primary key bukan auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang dapat diisi
    protected $fillable = [
        'id_sdm',
        'nama_sdm',
        'nidn',
        'nip',
        'nuptk',
        'nama_status_aktif',
        'nama_status_pegawai',
        'jenis_sdm',
        'jabatan_fungsional',
        'angka_kredit',
        'rekomendasi',
        'keterangan',
        'tanggal_mulai',
        'sk',
    ];
}
