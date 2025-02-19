<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Publikasi extends Model
{
    use HasFactory;

    protected $table = 'publikasis';
    public $incrementing = true; // Matikan AUTO_INCREMENT
    protected $keyType = 'int'; // ID berupa string (UUID)

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id_publikasi) {
                $model->id_publikasi = (string) Str::uuid(); // Generate UUID sebelum disimpan
            }
        });
    }

    protected $fillable = [
        'id',
        'id_publikasi',
        'id_sdm',
        'judul',
        'nama_jurnal',
        'tanggal',
        'jenis_publikasi',
        'urutan_penulis',
        'kategori_kegiatan',
        'penerbit',
        'asal_data',
    ];

    // Relasi ke Jenis Publikasi
    public function jenisPublikasi()
    {
        return $this->belongsTo(JenisPublikasi::class, 'jenis_publikasi', 'id');
    }

    // Relasi ke Kategori Publikasi
    public function kategoriPublikasi()
    {
        return $this->belongsTo(KategoriKegiatan::class, 'kategori_kegiatans');
    }
}
