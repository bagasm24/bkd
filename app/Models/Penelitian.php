<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Penelitian extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'penelitians';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID tidak auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_penelitian',
        'id_sdm',
        'judul',
        'bidang_keilmuan',
        'tahun_pelaksanaan',
        'lama_kegiatan'
    ];

    protected $casts = [
        'bidang_keilmuan' => 'array', // Konversi otomatis JSON ke array
        'tahun_pelaksanaan' => 'integer',
        'lama_kegiatan' => 'integer',
    ];
}
