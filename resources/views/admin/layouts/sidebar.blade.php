@php
    $menu = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Users', 'route' => null],
        ['label' => 'Car Brands', 'route' => null],
        ['label' => 'Car Models', 'route' => null],
        ['label' => 'Service Categories', 'route' => null],
        ['label' => 'Services', 'route' => null],
        ['label' => 'Workshops', 'route' => null],
        ['label' => 'Workshop Verifications', 'route' => null],
        ['label' => 'Diagnoses', 'route' => null],
        ['label' => 'Bookings', 'route' => null],
        ['label' => 'SOS Service Types', 'route' => null],
        ['label' => 'SOS Providers', 'route' => null],
        ['label' => 'SOS Requests', 'route' => null],
        ['label' => 'Reviews', 'route' => null],
        ['label' => 'Maintenance Items', 'route' => null],
        ['label' => 'Service Ledgers', 'route' => null],
        ['label' => 'Leads', 'route' => null],
        ['label' => 'Plans', 'route' => null],
        ['label' => 'Subscriptions', 'route' => null],
        ['label' => 'Payments', 'route' => null],
        ['label' => 'Featured Placements', 'route' => null],
        ['label' => 'Complaints', 'route' => null],
        ['label' => 'Static Pages', 'route' => null],
        ['label' => 'App Settings', 'route' => null],
    ];
@endphp

<aside class="admin-sidebar p-3 text-white">
    <div class="mb-4">
        <div class="fs-4 fw-bold">ElWarsha</div>
        <div class="small text-secondary">Super Admin</div>
    </div>
    <nav class="nav flex-column gap-1">
        @foreach ($menu as $item)
            @php
                $isActive = $item['route'] && request()->routeIs($item['route']);
            @endphp
            <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item['route'] ? route($item['route']) : '#' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</aside>
