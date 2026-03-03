<!DOCTYPE html>
<html>

<head>
    <title>NRML Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
        }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #0B8F3C;
            padding-top: 20px;
        }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            display: block;
            padding: 14px 20px;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-link-custom:hover {
            background-color: #06632A;
            color: white;
        }

        .active-link {
            border-left: 5px solid #F4C430;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-wrapper {
            margin-left: 220px;
        }

        .content-area {
            padding: 30px;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        /* TOP NAVBAR */
        .top-navbar {
            height: 60px;
            border-bottom: 4px solid #0B8F3C;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        .brand-title {
            font-weight: 600;
            font-size: 18px;
            color: #1E63B6;
        }

        .content-area {
            padding: 30px;
            background: #f8f9fa;
            min-height: calc(100vh - 60px);
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
            margin-right: 10px;
        }

        .card {
            border-radius: 10px;
            border: 1px solid #E5E7EB;
        }

        .card h3 {
            color: #0B8F3C;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column justify-content-between">

        <div>

            <a href="/dashboard" class="nav-link-custom {{ request()->is('dashboard') ? 'active-link' : '' }}">
                <span class="nav-text">Overview</span>
            </a>

            @foreach($programs as $program)
                <a href="/dashboard/{{ strtolower($program->code) }}"
                    class="nav-link-custom {{ request()->is('dashboard/' . strtolower($program->code)) ? 'active-link' : '' }}">
                    <span class="nav-text">{{ $program->code }}</span>
                </a>
            @endforeach

        </div>

        <div class="mb-3">
            <a href="#" class="nav-link-custom">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">Settings</span>
            </a>
        </div>

    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Top Navbar -->
        <div class="top-navbar">

            <img src="{{ asset('images/nrml-logo.png') }}" class="brand-logo" alt="NRML Logo">

            <div class="brand-title">
                National Reference Medical Laboratory Surveillance Dashboard
            </div>

            <div class="ms-auto text-muted small">
                Status: Active Surveillance
            </div>

        </div>

        <!-- Page Content -->
        <div class="content-area">
            @yield('content')
        </div>

    </div>

</body>

</html>