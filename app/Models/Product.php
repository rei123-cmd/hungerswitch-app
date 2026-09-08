<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Ganti 'products' dengan nama tabel produk kamu
    protected $table = 'products';

    // Kalau primary key bukan 'id', ganti di sini
    protected $primaryKey = 'id';

    // Set false kalau tabel tidak punya created_at & updated_at
    public $timestamps = false;

    // Kolom yang boleh diisi massal (sesuaikan dengan tabelmu)
    protected $fillable = [
        'name',
        'original_price',
        'discounted_price',
        'category',
        'expiry_time',
        // tambah kolom lain yang ada di tabel produk
    ];
}
