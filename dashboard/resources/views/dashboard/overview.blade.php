@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Dashboard Overview</h3>
                <small class="text-muted">National surveillance summary</small>
            </div>
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">


                    <select id="trend_range" class="form-select w-auto">
                        <option value="8" selected>Last 8 weeks</option>
                        <option value="12">Last 12 weeks</option>
                        <option value="26">Last 26 weeks</option>
                        <option value="custom">Custom range</option>
                    </select>

                    <div id="custom_range_container" style="display:none;" class="align-items-center gap-1">

                        <select id="start_year" class="form-select"></select>
                        <select id="start_week" class="form-select"></select>

                        <span class="mx-1">to</span>

                        <select id="end_year" class="form-select"></select>
                        <select id="end_week" class="form-select"></select>

                    </div>

                </div>
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

                        <div class="mb-3">
                            <h5 class="fw-bold mb-1">Epidemic Trend</h5>
                            <p class="text-muted small mb-0">
                                Weekly reported cases by surveillance program
                            </p>
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
                        
                        <div id="provinceMap" style="height:420px;"></div>
                        <div class="d-flex gap-3 mb-2 small">
                            <span><span style="color:#2563eb; font-size:2rem;">●</span> SARI</span>
                            <span><span style="color:#10b981; font-size:2rem;">●</span> ILI</span>
                            <span><span style="color:#9333ea; font-size:2rem;">●</span> LBM</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script>

        let trendChart;

        function loadSummary() {

            const today = new Date();
            const past = new Date();

            past.setDate(today.getDate() - 7);

            const dateFrom = past.toISOString().split('T')[0];
            const dateTo = today.toISOString().split('T')[0];

            fetch(`/api/dashboard/summary?date_from=${dateFrom}&date_to=${dateTo}`)
                .then(res => res.json())
                .then(data => {

                    let html = '';

                    data.forEach(item => {

                        let trendColor = 'text-secondary';

                        if (item.percent_change > 0) trendColor = 'text-danger';
                        if (item.percent_change < 0) trendColor = 'text-success';

                        html += `<div class="col-md-2 mb-3">
                                                    <div class="card shadow-sm h-100">
                                                        <div class="card-body">

                                                            <div class="d-flex justify-content-between">

                                                                <div>
                                                                    <h6 class="fw-bold">${item.code}</h6>
                                                                    <h3 class="mb-1">${item.current_total}</h3>
                                                                    <small class="text-muted">Last 7 days</small>
                                                                </div>

                                                                <div class="text-end">

                                                                    <div class="${trendColor} fw-bold">
                                                                        ${item.percent_change > 0 ? '▲' : item.percent_change < 0 ? '▼' : '–'}
                                                                        ${Math.abs(item.percent_change)}%
                                                                    </div>

                                                                    <small class="text-muted">
                                                                        ${item.previous_total ?? 0} last week
                                                                    </small>

                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                    });

                    document.getElementById('summary_cards').innerHTML = html;

                });
        }
        function getCurrentEpiWeek() {

            const today = new Date();
            const firstJan = new Date(today.getFullYear(), 0, 1);

            const days = Math.floor((today - firstJan) / 86400000);
            const week = Math.ceil((days + firstJan.getDay() + 1) / 7);

            return {
                year: today.getFullYear(),
                week: week
            };
        }
        function calculateRange(weeksBack) {

            const current = getCurrentEpiWeek();

            let endYear = current.year;
            let endWeek = current.week;

            let startYear = endYear;
            let startWeek = endWeek - weeksBack + 1;

            while (startWeek <= 0) {
                startWeek += 52;
                startYear--;
            }

            return {
                startYear,
                startWeek,
                endYear,
                endWeek
            };
        }
        function loadTrend(periodType, startYear, startWeek, endYear, endWeek) {

            fetch(`/api/dashboard/trend?period_type=${periodType}&start_year=${startYear}&start_week=${startWeek}&end_year=${endYear}&end_week=${endWeek}`)
                .then(res => res.json())
                .then(data => {

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

                    const labels = Array.from(labelsSet).sort((a, b) => {

                        const [yearA, weekA] = a.split('-').map(Number);
                        const [yearB, weekB] = b.split('-').map(Number);

                        if (yearA !== yearB) return yearA - yearB;

                        return weekA - weekB;
                    });

                    const colors = {
                        SARI: '#2563eb',
                        ILI: '#10b981',
                        LBM: '#9333ea',
                        NDS: '#f59e0b',
                    };

                    const datasets = [];

                    const allowedPrograms = ['SARI', 'ILI', 'LBM', 'NDS'];

                    Object.keys(data).forEach(code => {

                        if (!allowedPrograms.includes(code)) return;

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
                            backgroundColor: colors[code],
                            borderWidth: 3,
                            pointRadius: 4,
                            fill: false,
                            tension: 0.3
                        });
                    });
                    const displayLabels = labels.map(l => {
                        const [year, week] = l.split('-');
                        return `W${String(week).padStart(2, '0')}`;
                    });
                    trendChart = new Chart(document.getElementById('trendChart'), {
                        type: 'line',
                        data: {
                            labels: displayLabels,
                            datasets: datasets
                        },
                        options: {

                            responsive: true,

                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            },

                            interaction: {
                                mode: 'index',
                                intersect: false
                            },

                            scales: {

                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                },

                                x: {
                                    grid: {
                                        display: false
                                    }
                                }

                            }

                        }
                    });
                    document.getElementById('trendChart').style.opacity = 1;
                });

        }

        function toggleCustomInputs(show) {

            const container = document.getElementById('custom_range_container');

            if (!container) return;

            container.style.display = show ? 'flex' : 'none';

        }
        function refreshTrend() {


            const startYear = document.getElementById('start_year').value;
            const startWeek = document.getElementById('start_week').value;

            const endYear = document.getElementById('end_year').value;
            const endWeek = document.getElementById('end_week').value;
            loadProvinceMap();
            loadTrend('week', startYear, startWeek, endYear, endWeek);

        }

        function populateFilters() {

            const currentYear = new Date().getFullYear();

            const startYear = document.getElementById('start_year');
            const endYear = document.getElementById('end_year');

            for (let y = currentYear - 10 ; y <= currentYear; y++) {

                startYear.innerHTML += `<option value="${y}">${y}</option>`;
                endYear.innerHTML += `<option value="${y}">${y}</option>`;
            }

            const startWeek = document.getElementById('start_week');
            const endWeek = document.getElementById('end_week');

            for (let w = 1; w <= 53; w++) {

                startWeek.innerHTML += `<option value="${w}">W${w}</option>`;
                endWeek.innerHTML += `<option value="${w}">W${w}</option>`;
            }

        }

        document.addEventListener('DOMContentLoaded', function () {

            populateFilters();
            toggleCustomInputs(false);
            document.getElementById('custom_range_container').style.display = 'none';
            loadProvinceMap();

            const defaultRange = calculateRange(8);

            document.getElementById('start_year').value = defaultRange.startYear;
            document.getElementById('start_week').value = defaultRange.startWeek;

            document.getElementById('end_year').value = defaultRange.endYear;
            document.getElementById('end_week').value = defaultRange.endWeek;

            loadTrend(
                'week',
                defaultRange.startYear,
                defaultRange.startWeek,
                defaultRange.endYear,
                defaultRange.endWeek
            );

            loadSummary();
            document.getElementById('trend_range')
                .addEventListener('change', function () {

                    const value = this.value;

                    if (value === 'custom') {

                        toggleCustomInputs(true);
                        return;

                    }

                    toggleCustomInputs(false);

                    const range = calculateRange(parseInt(value));

                    document.getElementById('start_year').value = range.startYear;
                    document.getElementById('start_week').value = range.startWeek;

                    document.getElementById('end_year').value = range.endYear;
                    document.getElementById('end_week').value = range.endWeek;
                    document.getElementById('trendChart').style.opacity = 0.4;
                    refreshTrend();


                });
            ['start_year', 'start_week', 'end_year', 'end_week']
                .forEach(id => {
                    document.getElementById(id)
                        .addEventListener('change', refreshTrend);
                });

        });

        let map;

        function getColor(value) {

            if (value > 50) return '#b91c1c';
            if (value > 25) return '#dc2626';
            if (value > 10) return '#f97316';
            if (value > 5) return '#facc15';

            return '#e5e7eb';
        }
        const colors = {
            1: '#2563eb', // SARI
            2: '#10b981', // ILI
            3: '#9333ea'  // LBM
        };

        function loadSentinelMap() {

            fetch(`/api/dashboard/sentinel-map?...`)
                .then(r => r.json())
                .then(data => {

                    data.forEach(site => {

                        L.circleMarker(
                            [site.lat, site.lng],
                            {
                                radius: Math.sqrt(site.total) * 3,
                                fillColor: colors[site.surveillance_id],
                                color: '#fff',
                                weight: 1,
                                fillOpacity: 0.9
                            }
                        )
                            .bindTooltip(`
                                ${site.sentinel_site_name}<br>
                                Cases: ${site.total}
                            `)
                            .addTo(map);

                    });

                });
        }
        function loadProvinceMap() {

            if (map) map.remove();

            map = L.map('provinceMap').setView([12.7, 104.9], 7);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const startYear = document.getElementById('start_year').value;
            const startWeek = document.getElementById('start_week').value;

            const endYear = document.getElementById('end_year').value;
            const endWeek = document.getElementById('end_week').value;

            const colors = {
                1: '#2563eb', // SARI
                2: '#10b981', // ILI
                3: '#9333ea'  // LBM
            };

            Promise.all([
                fetch('/geo/cambodia_provinces.geojson').then(r => r.json()),
                fetch(`/api/dashboard/province-circles?start_year=${startYear}&start_week=${startWeek}&end_year=${endYear}&end_week=${endWeek}`).then(r => r.json())
            ])
                .then(([geojson, data]) => {

                    L.geoJSON(geojson, {
                        style: {
                            fillOpacity: 0,
                            color: '#ccc',
                            weight: 1
                        },

                        onEachFeature: function (feature, layer) {

                            const province = feature.properties.ADM1_EN;

                            const center = layer.getBounds().getCenter();

                            const rows = data.filter(d => d.site_province_name === province);
                            const offsets = {
                                1: -0.15, // SARI left
                                2: 0,     // ILI center
                                3: 0.15   // LBM right
                            };

                            rows.forEach(row => {

                                const radius = Math.sqrt(row.total) * 4;

                                const lat = center.lat;
                                const lng = center.lng + offsets[row.surveillance_id];

                                L.circleMarker([lat, lng], {
                                    radius: radius,
                                    fillColor: colors[row.surveillance_id],
                                    color: '#fff',
                                    weight: 1,
                                    fillOpacity: 0.9
                                })
                                    .bindTooltip(`
                                            <strong>${province}</strong><br>
                                            ${row.surveillance_id === 1 ? 'SARI' :
                                            row.surveillance_id === 2 ? 'ILI' : 'LBM'}<br>
                                            Cases: ${row.total}
                                        `)
                                    .addTo(map);

                            });
                        }

                    }).addTo(map);

                });

        }
    </script>

@endsection