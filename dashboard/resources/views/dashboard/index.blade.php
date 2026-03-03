<!DOCTYPE html>
<html>
<head>
    <title>NRML Surveillance Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container-fluid py-4">

    <h3 class="mb-4">NRML Surveillance Dashboard</h3>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body row g-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label">Surveillance Program</label>
                <select id="surveillance_id" class="form-select">
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">
                            {{ $program->code }} - {{ $program->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Period</label>
                <select id="period_type" class="form-select">
                    <option value="week">Epiweek</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" id="date_from" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" id="date_to" class="form-control">
            </div>

            <div class="col-md-2">
                <button onclick="loadDashboard()" class="btn btn-primary w-100">
                    Apply
                </button>
            </div>

        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row" id="summary_cards"></div>

    <!-- Trend Chart -->
    <div class="card mt-4">
        <div class="card-body">
            <h5>Epidemic Trend</h5>
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Province Table -->
    <div class="card mt-4">
        <div class="card-body">
            <h5>Cases by Province</h5>
            <table class="table table-bordered" id="provinceTable">
                <thead>
                    <tr>
                        <th>Province</th>
                        <th>Total Cases</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<script>

let trendChart;

function loadDashboard() {

    const surveillanceId = document.getElementById('surveillance_id').value;
    const periodType = document.getElementById('period_type').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;

    loadSummary(dateFrom, dateTo);
    loadTrend(periodType, dateFrom, dateTo);
    loadProvince(surveillanceId, dateFrom, dateTo);
}

function loadSummary(dateFrom, dateTo) {

    fetch(`/api/dashboard/summary?date_from=${dateFrom}&date_to=${dateTo}`)
        .then(res => res.json())
        .then(data => {

            let html = '';

            data.forEach(item => {
                html += `
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6>${item.code}</h6>
                                <h4>${item.current_total}</h4>
                                <small class="${item.percent_change >= 0 ? 'text-success' : 'text-danger'}">
                                    ${item.percent_change}%
                                </small>
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

            if (trendChart) trendChart.destroy();

            const labelsSet = new Set();

            Object.values(data).forEach(program => {
                program.forEach(row => {
                    labelsSet.add(`${row.year ?? ''}-${row.period}`);
                });
            });

            const labels = Array.from(labelsSet).sort();

            const datasets = [];

            const colors = {
                SARI: 'red',
                ILI: 'blue',
                LBM: 'green'
            };

            Object.keys(data).forEach(code => {

                const values = labels.map(label => {
                    const found = data[code].find(row =>
                        `${row.year ?? ''}-${row.period}` === label
                    );
                    return found ? found.total : 0;
                });

                datasets.push({
                    label: code,
                    data: values,
                    borderColor: colors[code] || 'black',
                    fill: false
                });
            });

            trendChart = new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                }
            });
        });
}

function loadProvince(surveillanceId, dateFrom, dateTo) {

    fetch(`/api/dashboard/province?surveillance_id=${surveillanceId}&date_from=${dateFrom}&date_to=${dateTo}`)
        .then(res => res.json())
        .then(data => {

            let html = '';

            data.forEach(item => {
                html += `
                    <tr>
                        <td>${item.site_province_name}</td>
                        <td>${item.total}</td>
                    </tr>
                `;
            });

            document.querySelector('#provinceTable tbody').innerHTML = html;
        });
}

document.addEventListener('DOMContentLoaded', function() {

    const today = new Date().toISOString().split('T')[0];
    const past = new Date();
    past.setDate(past.getDate() - 30);

    document.getElementById('date_from').value = past.toISOString().split('T')[0];
    document.getElementById('date_to').value = today;

    loadDashboard();
});

</script>

</body>
</html>