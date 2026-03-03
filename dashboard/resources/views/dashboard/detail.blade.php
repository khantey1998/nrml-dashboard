@extends('layouts.app')

@section('content')

<h3>{{ $selected->code }} - {{ $selected->name_en }}</h3>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <h6>Total Cases</h6>
            <h4 id="totalCases">-</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>Epidemic Trend</h5>
        <canvas id="trendChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const surveillanceId = {{ $selected->id }};
    const dateFrom = '2026-01-01';
    const dateTo = new Date().toISOString().split('T')[0];

    fetch(`/api/dashboard/trend?surveillance_id=${surveillanceId}&period_type=week&date_from=${dateFrom}&date_to=${dateTo}`)
        .then(res => res.json())
        .then(data => {

            const labels = data.map(d => `${d.year}-${d.period}`);
            const totals = data.map(d => d.total);

            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ $selected->code }}',
                        data: totals,
                        borderColor: 'blue',
                        fill: false
                    }]
                }
            });
        });

});
</script>

@endsection