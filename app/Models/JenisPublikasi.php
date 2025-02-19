<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   JenisPublikasi extends Model
{
    use HasFactory;

    protected $table = 'jenis_publikasis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'nama',
    ];
}
