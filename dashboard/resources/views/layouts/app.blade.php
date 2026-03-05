<!DOCTYPE html>
<html>

<head>
    <title>NRML Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        body {
            margin: 0;
        }

        /* HEADER */
        .top-navbar {
            height: 60px;
            background: #0B8F3C;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 25px;
        }

        .brand-title {
            font-weight: 600;
            font-size: 18px;
        }

        /* NAV BAR */
        .nav-bar {
            display: flex;
            background: white;
            border-bottom: 1px solid #dcdcdc;
            padding-left: 15px;
        }

        /* NAV ITEMS */
        .nav-item {
            padding: 12px 18px;
            text-decoration: none;
            color: #262626;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            font-size:14px;
        }

        .nav-item:hover {
            background: #cce0d4;
        }

        /* ACTIVE TAB */
        .active-tab {
            color: #0B8F3C;
            border-bottom: 3px solid #0B8F3C;
            background: #e5efe8;
        }

        /* CONTENT */
        .content-area {
            padding: 25px;
        }

        .brand-title {
            font-weight: 600;
            font-size: 18px;
            color: #f8f9fa;
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

    <!-- TOP HEADER -->
    <div class="top-navbar">

        <div class="brand-title">
            National Reference Medical Laboratory Surveillance Dashboard
        </div>

        <div class="ms-auto  small">
            Last update: 12:05 |
            Data latency: 5–10 min |
            User: National - Read Only
        </div>

    </div>

    <!-- NAVIGATION BAR -->
    <div class="nav-bar">

        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active-tab' : '' }}">
            Overview
        </a>

        @foreach($programs as $program)
            <a href="/dashboard/{{ strtolower($program->code) }}"
                class="nav-item {{ request()->is('dashboard/' . strtolower($program->code)) ? 'active-tab' : '' }}">
                {{ $program->code }}
            </a>
        @endforeach

    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">


        <!-- Page Content -->
        <div class="content-area">
            @yield('content')
        </div>

    </div>

</body>

</html>