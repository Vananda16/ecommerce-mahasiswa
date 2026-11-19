{{-- Tabel item pesanan. Variabel: $order --}}
<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Produk</th>
                <th class="text-end">Harga</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        @if($item->product)
                            <a href="{{ route('products.show', $item->product) }}" class="text-decoration-none">{{ $item->product_name }}</a>
                        @else
                            {{ $item->product_name }} <span class="badge text-bg-secondary">dihapus</span>
                        @endif
                    </td>
                    <td class="text-end">@rupiah($item->price)</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">@rupiah($item->subtotal)</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end text-primary">@rupiah($order->total)</td>
            </tr>
        </tfoot>
    </table>
</div>
