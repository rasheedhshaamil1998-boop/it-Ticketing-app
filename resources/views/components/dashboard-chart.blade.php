@props(['title', 'subtitle', 'type', 'data', 'filter' => null])
<section {{ $attributes->merge(['class' => 'overview-panel dashboard-chart-panel']) }} data-dashboard-chart data-chart-type="{{ $type }}" data-chart-labels="{{ json_encode($data['labels']) }}" data-chart-values="{{ json_encode($data['values']) }}">
    <div class="panel-heading"><div><h2>{{ $title }}</h2><p>{{ $subtitle }}</p></div><span class="chart-total">{{ array_sum($data['values']) }} <small>tickets</small></span></div>
    <div class="chart-canvas-wrap">
        <div class="chart-surface" data-chart-surface role="img" aria-label="{{ $title }}" @if (array_sum($data['values']) === 0) hidden @endif></div>
        @if (array_sum($data['values']) === 0)
            <div class="chart-empty"><i class="bi bi-bar-chart" aria-hidden="true"></i><span>No ticket data for this period.</span></div>
        @else
            <span class="chart-loading" data-chart-loading>Loading chart...</span>
        @endif
    </div>
    <ul class="chart-data-list">
        @foreach ($data['labels'] as $index => $label)
            <li>
                @if ($filter)
                    <a href="{{ route('admin.tickets.index', [$filter => $label]) }}">{{ $label }}</a>
                @else
                    <span>{{ $label }}</span>
                @endif
                <strong>{{ $data['values'][$index] }}</strong>
            </li>
        @endforeach
    </ul>
    <noscript><p class="text-muted small">JavaScript is disabled. All chart values are listed above.</p></noscript>
</section>
