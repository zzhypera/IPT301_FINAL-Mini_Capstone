<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') &#183; FixDesk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid main-wrap">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                &#128295; FixDesk <span class="badge text-bg-info ms-1">Repair Shop</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <div class="navbar-nav ms-auto">
                    @foreach ([
                        ['dashboard', 'Dashboard', 'dashboard'],
                        ['customers.index', 'Customers', 'customers.*'],
                        ['devices.index', 'Devices', 'devices.*'],
                        ['technicians.index', 'Technicians', 'technicians.*'],
                        ['repair-jobs.index', 'Repair Jobs', 'repair-jobs.*'],
                        ['payments.index', 'Payments', 'payments.*'],
                    ] as [$route, $label, $pattern])
                        <a class="nav-link px-lg-3 {{ request()->routeIs($pattern) ? 'active' : '' }}"
                            href="{{ route($route) }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>

    <main class="container-fluid main-wrap py-4 py-lg-5">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

        <footer class="text-center text-secondary small py-4">
            FixDesk &#183; Computer Repair Shop Management &#183; Laravel + Blade + Bootstrap
        </footer>
    </main>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/form-drafts.js') }}"></script>
</body>
</html>

