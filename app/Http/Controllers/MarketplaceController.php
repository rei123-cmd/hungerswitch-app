<?php

namespace App\Http\Controllers;

use App\Models\Product;

class MarketplaceController extends Controller
{
    public function index()
    {
        // Ambil semua produk dari tabel products lewat model Product
        $products = Product::all();

        // Data tambahan untuk view
        $current_role = session('current_role', 'customer');
        $active_menu  = 'marketplace';

        // Kirim ke view resources/views/marketplace.blade.php
        return view('marketplace', compact('products', 'current_role', 'active_menu'));
    }
}
