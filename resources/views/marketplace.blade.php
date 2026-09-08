<div class="product-grid" id="productGrid">
    @forelse($products as $product)
        @php
            $discount = 0;
            if ($product->original_price && $product->discounted_price) {
                $discount = round(
                    (($product->original_price - $product->discounted_price)
                    / $product->original_price) * 100
                );
            }

            $expiry     = $product->expiry_time ? strtotime($product->expiry_time) : 0;
            $now        = time();
            $hours_left = $expiry > 0 ? max(0, round(($expiry - $now) / 3600)) : 0;
        @endphp

        <div class="product-card"
             data-category="{{ $product->category }}"
             data-price="{{ $product->discounted_price }}">
            <h3>{{ $product->name }}</h3>
            <p>Harga: Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</p>
            <p>Diskon: {{ $discount }}%</p>
            <p>Sisa waktu: {{ $hours_left }} jam</p>
        </div>
    @empty
        <p>Belum ada produk tersedia.</p>
    @endforelse
</div>
