<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    // Memberi tahu nama tabel yang benar
    protected $table = 'berita';
    
    // Memberi tahu nama Primary Key-nya
    protected $primaryKey = 'id_berita';
    
    // Mematikan fitur waktu bawaan Laravel karena kita pakai tanggal_ambil
    public $timestamps = false;
    
    // Mengizinkan semua kolom untuk diisi data
    protected $guarded = [];
}