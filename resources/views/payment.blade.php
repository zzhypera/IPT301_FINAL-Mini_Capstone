@extends('app')

@section('title', $mode === 'form' ? ($payment->exists ? 'Edit Payment' : 'Record Payment') : 'Payments')

@section('content')
@if ($mode === 'index')
<div class="d-flex justify-content-between align-items-end gap-3 mb-4">
    <div>
        <div class="eyebrow">Accounts</div>
        <h1 class="fw-bold">Payments</h1>
        <p class="text-secondary mb-0">Record and review payments made toward repair jobs.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('payments.create') }}">＋ Record payment</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Date</th>
                    <th>Repair job / Customer</th>
                    <th class="text-end">Amount paid</th>
                    <th class="text-end">Job balance</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td class="ps-4">{{ $payment->payment_date?->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('repair-jobs.show', $payment->repairJob) }}" class="fw-semibold">Job #{{ $payment->job_id }}</a>
                            <div class="small text-secondary">
                                {{ $payment->repairJob->device->customer->full_name }}
                                <span aria-hidden="true">·</span>
                                {{ $payment->repairJob->device->label }}
                            </div>
                        </td>
                        <td class="text-end fw-semibold">₱{{ number_format($payment->amount_paid, 2) }}</td>
                        <td class="text-end">₱{{ number_format(max(0, $payment->repairJob->balance_remaining), 2) }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('payments.show', $payment) }}">View</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('payments.edit', $payment) }}">Edit</a>
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Delete this payment record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No payments recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@elseif ($mode === 'form')
<div class="mb-4">
    <div class="eyebrow">Accounts</div>
    <h1 class="fw-bold">{{ $payment->exists ? 'Edit payment' : 'Record payment' }}</h1>
    <p class="text-secondary">Each payment belongs to one repair job. The amount cannot exceed that job's remaining balance.</p>
</div>

<form class="card" method="POST" action="{{ $payment->exists ? route('payments.update', $payment) : route('payments.store') }}">
    <div class="card-body p-4">
        @csrf
        @if ($payment->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label required" for="job_id">Repair job</label>
                <select class="form-select @error('job_id') is-invalid @enderror" id="job_id" name="job_id" required>
                    <option value="">Choose a repair job</option>
                    @foreach ($jobs as $job)
                        <option value="{{ $job->job_id }}" @selected(old('job_id', $payment->job_id) == $job->job_id)>
                            #{{ $job->job_id }} · {{ $job->device->customer->full_name }} · {{ $job->device->label }}
                            (total ₱{{ number_format($job->total_cost, 2) }}, balance ₱{{ number_format(max(0, $job->balance_remaining + ($payment->exists && $payment->job_id == $job->job_id ? $payment->amount_paid : 0)), 2) }})
                        </option>
                    @endforeach
                </select>
                <x-field-error field="job_id" />
            </div>

            <div class="col-md-6">
                <label class="form-label required" for="payment_date">Payment date</label>
                <input class="form-control @error('payment_date') is-invalid @enderror" type="date" id="payment_date" name="payment_date"
                    value="{{ old('payment_date', optional($payment->payment_date)->format('Y-m-d') ?: now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}" required>
                <x-field-error field="payment_date" />
            </div>
            <div class="col-md-6">
                <label class="form-label required" for="amount_paid">Amount paid (₱)</label>
                <input class="form-control @error('amount_paid') is-invalid @enderror" type="number" id="amount_paid" name="amount_paid" min="0.01" max="99999999.99" step="0.01"
                    value="{{ old('amount_paid', $payment->amount_paid) }}" required>
                <x-field-error field="amount_paid" />
            </div>
        </div>

        @if ($jobs->isEmpty())
            <div class="alert alert-warning mt-3 mb-0">Create a repair job before recording a payment.</div>
        @endif
    </div>
    <div class="card-footer bg-white p-3 d-flex gap-2">
        <button class="btn btn-primary" @disabled($jobs->isEmpty())>Save payment</button>
        <a class="btn btn-outline-secondary" href="{{ route('payments.index') }}">Cancel</a>
    </div>
</form>
@elseif ($mode === 'detail')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <div class="eyebrow">Payment record</div>
        <h1 class="fw-bold">Payment #{{ $payment->payment_id }}</h1>
        <p class="text-secondary mb-0">Recorded {{ $payment->payment_date?->format('M d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('payments.index') }}">Back to payments</a>
        <a class="btn btn-primary" href="{{ route('payments.edit', $payment) }}">Edit payment</a>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Payment details</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">Amount paid</dt><dd class="col-sm-7 fw-semibold">&#8369;{{ number_format($payment->amount_paid, 2) }}</dd>
                <dt class="col-sm-5">Current job balance</dt><dd class="col-sm-7">&#8369;{{ number_format(max(0, $payment->repairJob->balance_remaining), 2) }}</dd>
            </dl>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Repair job</h2>
            <p><a class="fw-semibold" href="{{ route('repair-jobs.show', $payment->repairJob) }}">Job #{{ $payment->job_id }} · {{ $payment->repairJob->device->label }}</a></p>
            <dl class="row mb-0">
                <dt class="col-sm-5">Customer</dt><dd class="col-sm-7">{{ $payment->repairJob->device->customer->full_name }}</dd>
                <dt class="col-sm-5">Job total</dt><dd class="col-sm-7">&#8369;{{ number_format($payment->repairJob->total_cost, 2) }}</dd>
                <dt class="col-sm-5">Current balance</dt><dd class="col-sm-7">&#8369;{{ number_format(max(0, $payment->repairJob->balance_remaining), 2) }}</dd>
            </dl>
        </div></div>
    </div>
</div>
@endif
@endsection

