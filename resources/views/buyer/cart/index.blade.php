@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
    <h3 class="fw-bold mb-4">Keranjang Belanja</h3>

    @if($groups->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-cart3 fs-1"></i>
                <p class="mt-2">Keranjangmu masih kosong.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Mulai belanja</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach($groups as $items)
                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-shop"></i> {{ $items->first()->product->store->name }}</div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td style="width:64px">
                                                @if($item->product->image_url)
                                                    <img src="{{ $item->product->image_url }}" class="thumb" alt="">
                                                @else
                                                    <span class="thumb-placeholder"><i class="bi bi-image"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('products.show', $item->product) }}" class="text-decoration-none fw-semibold">{{ $item->product->name }}</a>
                                                <div class="small text-muted">@rupiah($item->product->price) &middot; stok {{ $item->product->stock }}</div>
                                            </td>
                                            <td style="width:150px">
                                                <form method="POST" action="{{ route('cart.update', $item) }}" class="input-group input-group-sm">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control">
                                                    <button class="btn btn-outline-secondary" title="Perbarui"><i class="bi bi-check2"></i></button>
                                                </form>
                                            </td>
                                            <td class="text-end fw-semibold" style="width:130px">@rupiah($item->subtotal)</td>
                                            <td style="width:50px">
                                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
                @if($groups->count() > 1)
                    <p class="small text-muted"><i class="bi bi-info-circle"></i> Produk dari toko berbeda akan dipecah menjadi satu pesanan per toko.</p>
                @endif
            </div>

            <div class="col-lg-4">
                <form method="POST" action="{{ route('orders.store') }}" class="card">
                    @csrf
                    <div class="card-header">Checkout</div>
                    <div class="card-body">
                        <x-textarea name="shipping_address" label="Alamat / lokasi pengantaran" :value="auth()->user()->address" :required="true" rows="2"
                                    help="Contoh: Asrama Putra Blok B No. 12, atau 'COD di kantin gedung A'." />
                        <x-textarea name="note" label="Catatan untuk penjual" rows="2" />

                        <div class="mb-3">
                            <label class="form-label">Metode pembayaran <span class="text-danger">*</span></label>
                            @foreach(\App\Models\Payment::METHODS as $key => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pm-{{ $key }}" value="{{ $key }}"
                                           @checked(old('payment_method', 'transfer') === $key)>
                                    <label class="form-check-label" for="pm-{{ $key }}">{{ $label }}</label>
                                </div>
                            @endforeach
                            @error('payment_method')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                            <span>Total</span>
                            <span class="text-primary">@rupiah($total)</span>
                        </div>

                        <button class="btn btn-primary w-100" type="submit">Buat Pesanan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
