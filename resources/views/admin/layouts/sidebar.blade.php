@php
    $menu = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Users', 'route' => 'admin.users.index'],
        ['label' => 'Car Brands', 'route' => 'admin.car-brands.index'],
        ['label' => 'Car Models', 'route' => 'admin.car-models.index'],
        ['label' => 'Service Categories', 'route' => 'admin.service-categories.index'],
        ['label' => 'Services', 'route' => 'admin.services.index'],
        ['label' => 'Workshops', 'route' => 'admin.workshops.index'],
        ['label' => 'Workshop Verifications', 'route' => 'admin.workshop-verifications.index'],
        ['label' => 'Diagnoses', 'route' => 'admin.diagnoses.index'],
        ['label' => 'Bookings', 'route' => 'admin.bookings.index'],
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
                $isActive = $item['route'] && request()->routeIs(str_replace('.index', '.*', $item['route']));
            @endphp
            <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item['route'] ? route($item['route']) : '#' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</aside>
