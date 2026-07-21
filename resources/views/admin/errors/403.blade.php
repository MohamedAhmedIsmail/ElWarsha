<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - ElWarsha Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="text-center">
        <div class="display-3 fw-bold text-danger">403</div>
        <h1 class="h3">Access denied</h1>
        <p class="text-muted">This area is available only for admin and super admin accounts.</p>
        <a href="{{ route('admin.login') }}" class="btn btn-primary">Back to login</a>
    </div>
</main>
</body>
</html>
