<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - ElWarsha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .admin-sidebar { width: 280px; min-height: 100vh; background: #111827; }
        .admin-sidebar .nav-link { color: #cbd5e1; border-radius: .5rem; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { color: #fff; background: #2563eb; }
        .admin-content { min-width: 0; }
        .kpi-card { border: 0; box-shadow: 0 10px 25px rgba(15, 23, 42, .06); }
        .table-card { border: 0; box-shadow: 0 10px 25px rgba(15, 23, 42, .05); }
    </style>
</head>
<body>
<div class="d-flex">
    @include('admin.layouts.sidebar')
    <div class="admin-content flex-grow-1">
        @include('admin.layouts.header')
        <main class="p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
