{{-- Ringkasan pesanan. Variabel: $order --}}
<dl class="row mb-0">
    <dt class="col-sm-4 text-muted fw-normal">Kode pesanan</dt>
    <dd class="col-sm-8 fw-semibold">{{ $order->code }}</dd>

    <dt class="col-sm-4 text-muted fw-normal">Tanggal</dt>
    <dd class="col-sm-8">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</dd>

    <dt class="col-sm-4 text-muted fw-normal">Status pesanan</dt>
    <dd class="col-sm-8">
        <span class="badge text-bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
        @if($order->rejection_reason)
            <div class="small text-danger mt-1">Alasan: {{ $order->rejection_reason }}</div>
        @endif
    </dd>

    <dt class="col-sm-4 text-muted fw-normal">Pembayaran</dt>
    <dd class="col-sm-8">
        {{ $order->payment->method_label }}
        <span class="badge text-bg-{{ $order->payment->status_color }} ms-1">{{ $order->payment->status_label }}</span>
        @if($order->payment->note && $order->payment->status === 'rejected')
            <div class="small text-danger mt-1">Catatan penjual: {{ $order->payment->note }}</div>
        @endif
    </dd>

    <dt class="col-sm-4 text-muted fw-normal">Alamat / lokasi</dt>
    <dd class="col-sm-8">{{ $order->shipping_address }}</dd>

    @if($order->note)
        <dt class="col-sm-4 text-muted fw-normal">Catatan</dt>
        <dd class="col-sm-8">{{ $order->note }}</dd>
    @endif

    @if($order->completed_at)
        <dt class="col-sm-4 text-muted fw-normal">Selesai pada</dt>
        <dd class="col-sm-8">{{ $order->completed_at->translatedFormat('d F Y, H:i') }}</dd>
    @endif
</dl>
