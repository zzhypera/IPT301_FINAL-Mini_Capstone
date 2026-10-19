@extends('app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="eyebrow">Operations overview</div>
            <h1 class="fw-bold mb-1">Repair Shop Dashboard</h1>
            <p class="text-secondary mb-0">Track customers, devices, repair progress, and payments in one place.</p>
        </div>

        <a class="btn btn-primary" href="{{ route('repair-jobs.create') }}">＋ New repair job</a>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Customers', $customers, 'customers.index', '👥'],
            ['Devices', $devices, 'devices.index', '💻'],
            ['Technicians', $technicians, 'technicians.index', '🧰'],
            ['Open jobs', $openJobs, 'repair-jobs.index', '🔧'],
            ['Total jobs', $jobs, 'repair-jobs.index', '📋'],
            ['Collected', '₱' . number_format($collected, 2), 'payments.index', '₱'],
        ] as [$label, $value, $route, $icon])
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route($route) }}" class="text-decoration-none text-reset">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-secondary">{{ $icon }} {{ $label }}</div>
                            <div class="stat-number mt-2">{{ $value }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
            <div>
                <h2 class="h5 fw-bold mb-1">Recent repair jobs</h2>
                <p class="text-secondary small mb-0">Latest work orders and outstanding balances</p>
            </div>

            <a href="{{ route('repair-jobs.index') }}" class="btn btn-outline-secondary btn-sm">View all</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Job</th>
                        <th>Customer / Device</th>
                        <th>Technician</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Balance</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($recentJobs as $job)
                        <tr>
                            <td class="ps-4">
                                <a class="fw-bold" href="{{ route('repair-jobs.show', $job) }}">#{{ $job->job_id }}</a>
                            </td>
                            <td>
                                {{ $job->device->customer->full_name }}
                                <div class="small text-secondary">{{ $job->device->label }}</div>
                            </td>
                            <td>{{ $job->technician?->full_name ?? 'Unassigned' }}</td>
                            <td>
                                <span class="badge text-bg-{{ in_array($job->status, ['Completed', 'Released']) ? 'success' : ($job->status === 'Cancelled' ? 'secondary' : 'primary') }}">
                                    {{ $job->status }}
                                </span>
                            </td>
                            <td>₱{{ number_format($job->total_cost, 2) }}</td>
                            <td class="fw-semibold">₱{{ number_format(max(0, $job->balance_remaining), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No repair jobs yet. Create your first work order to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
