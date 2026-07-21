<header class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
    <div>
        <div class="fw-semibold">@yield('page_title', 'Dashboard')</div>
        <div class="text-muted small">Manage ElWarsha operations</div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="text-end">
            <div class="small fw-semibold">{{ auth()->user()?->name }}</div>
            <div class="small text-muted">{{ auth()->user()?->role?->value }}</div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="btn btn-outline-danger btn-sm" type="submit">Logout</button>
        </form>
    </div>
</header>
