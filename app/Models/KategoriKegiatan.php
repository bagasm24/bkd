<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKegiatan extends Model
{
    use HasFactory;

    protected $table = 'kategori_kegiatans'; // Nama tabel dalam database

    protected $fillable = [
        'id',
        'parent_id',
        'nama',
    ];

    public function parent()
    {
        return $this->belongsTo(KategoriKegiatan::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(KategoriKegiatan::class, 'parent_id');
    }
}
