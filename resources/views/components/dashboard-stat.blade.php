@props(['label', 'value', 'note', 'icon', 'tone' => 'blue', 'href'])
<a {{ $attributes->merge(['class' => 'overview-metric tone-'.$tone]) }} href="{{ $href }}">
    <div class="metric-top"><span>{{ $label }}</span><span class="metric-symbol"><i class="bi bi-{{ $icon }}" aria-hidden="true"></i></span></div>
    <div class="metric-value">{{ number_format($value) }}</div>
    <div class="metric-note"><span>{{ $note }}</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></div>
</a>
