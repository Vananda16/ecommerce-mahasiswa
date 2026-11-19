@php $v = (float) ($value ?? 0); @endphp
<span class="text-warning" title="{{ number_format($v, 1) }} / 5">
    @for($i = 1; $i <= 5; $i++)
        <i class="bi bi-star{{ $v >= $i ? '-fill' : ($v >= $i - 0.5 ? '-half' : '') }}"></i>
    @endfor
</span>
