@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Dashboard Overview</h3>
                <small class="text-muted">National surveillance summary</small>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4" id="summary_cards"></div>

        <div class="row">

            <!-- LEFT COLUMN -->
            <div class="col-lg-8 d-flex flex-column">

                <!-- Trend Chart -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Epidemic Trend</h5>

                            <select id="period_type" class="form-select w-auto">
                                <option value="week">Epiweek</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                            </select>
                        </div>

                        <canvas id="trendChart" height="110"></canvas>

                    </div>
                </div>

                <!-- Alerts -->
                <div class="card shadow-sm flex-grow-1">
                    <div class="card-body">
                        <h5 class="fw-bold">Recent Alerts & Notifications</h5>

                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item">
                                ⚠ Monitoring influenza increase in selected provinces.
                            </li>
                            <li class="list-group-item">
                                🔔 SARS-CoV-2 positivity rate under review.
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">

                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold">Cases by Provinces</h5>
                        <p class="text-muted small">(% change vs last period)</p>

                        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                            <span class="text-muted">Province heatmap coming next</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script>

        let trendChart;

        function loadSummary(dateFrom, dateTo) {

            fetch(`/api/dashboard/summary?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(res => res.json())
                .then(data => {

                    let html = '';

                    data.forEach(item => {

                        const colorClass = item.percent_change >= 0
                            ? 'text-danger'
                            : 'text-success';

                        html += `
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold">${item.code}</h6>
                                        <h3 class="mb-1">${item.current_total}</h3>
                                        <small class="${colorClass}">
                                            ${item.percent_change}% vs last period
                                        </small>
                                        <div class="small text-muted mt-1">
                                            +${item.last_24h} in 24h
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    document.getElementById('summary_cards').innerHTML = html;
                });
        }

        function loadTrend(periodType, dateFrom, dateTo) {

            fetch(`/api/dashboard/trend?period_type=${periodType}&date_from=${dateFrom}&date_to=${dateTo}`)
                .then(res => res.json())
                .then(data => {
                    console.log(data);

                    if (trendChart) trendChart.destroy();

                    const labelsSet = new Set();

                    Object.values(data).forEach(program => {
                        program.forEach(row => {

                            let label;

                            if (periodType === 'year') {
                                label = row.period.toString();
                            } else {
                                label = `${row.year}-${row.period}`;
                            }

                            labelsSet.add(label);
                        });
                    });

                    const labels = Array.from(labelsSet).sort();

                    const colors = {
                        SARI: '#2563eb',
                        ILI: '#10b981',
                        LBM: '#9333ea'
                    };

                    const datasets = [];

                    Object.keys(data).forEach(code => {

                        const values = labels.map(label => {

                            const found = data[code].find(row => {

                                let rowLabel;

                                if (periodType === 'year') {
                                    rowLabel = row.period.toString();
                                } else {
                                    rowLabel = `${row.year}-${row.period}`;
                                }

                                return rowLabel === label;
                            });

                            return found ? found.total : 0;
                        });

                        datasets.push({
                            label: code,
                            data: values,
                            borderColor: colors[code] || '#000',
                            fill: false,
                            tension: 0.3
                        });
                    });

                    trendChart = new Chart(document.getElementById('trendChart'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                });

        }

        document.addEventListener('DOMContentLoaded', function () {

            const today = new Date().toISOString().split('T')[0];
            const past = new Date();
            past.setDate(past.getDate() - 30);

            const dateFrom = past.toISOString().split('T')[0];
            const dateTo = today;

            loadSummary(dateFrom, dateTo);
            loadTrend('week', dateFrom, dateTo);

            document.getElementById('period_type')
                .addEventListener('change', function () {
                    loadTrend(this.value, dateFrom, dateTo);
                });

        });

    </script>

@endsection