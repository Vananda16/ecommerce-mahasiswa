<div class="card product-card h-100">
    <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
        @if($product->image_url)
            <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
        @endif
    </a>
    <div class="card-body d-flex flex-column">
        <div class="mb-1">
            <span class="badge text-bg-light border">{{ $product->type_label }}</span>
            @if($product->category)
                <span class="badge text-bg-light border">{{ $product->category->name }}</span>
            @endif
        </div>
        <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark fw-semibold mb-1">
            {{ \Illuminate\Support\Str::limit($product->name, 50) }}
        </a>
        <div class="text-primary fw-bold">@rupiah($product->price)</div>
        <div class="small text-muted mt-auto pt-2">
            <i class="bi bi-shop"></i> {{ $product->store->name }}
            @if($product->stock < 1)
                <span class="badge bg-secondary ms-1">Habis</span>
            @endif
        </div>
    </div>
</div>
