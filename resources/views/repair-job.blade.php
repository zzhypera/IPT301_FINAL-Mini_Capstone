@extends('app')

@section('title', $mode === 'form' ? ($job->exists ? 'Edit Repair Job' : 'New Repair Job') : ($mode === 'detail' ? 'Repair Job #' . $job->job_id : 'Repair Jobs'))

@section('content')
@if ($mode === 'index')
<div class="d-flex justify-content-between align-items-end gap-3 mb-4">
<div>
<div class="eyebrow">Work orders</div>
<h1 class="fw-bold">Repair Jobs</h1>
<p class="text-secondary mb-0">Monitor diagnosis, technician assignments, costs, and payment balances.</p>
</div>
<a class="btn btn-primary" href="{{ route('repair-jobs.create') }}">＋ New repair job</a>
</div>
<div class="card">
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
<th class="text-end pe-4">Actions</th>
</tr>
</thead>
<tbody>
@forelse($jobs as $job)<tr>
<td class="ps-4">
<a class="fw-bold" href="{{ route('repair-jobs.show',$job) }}">#{{ $job->job_id }}</a>
<div class="small text-secondary">{{ $job->received_date->format('M d, Y') }}</div>
</td>
<td>{{ $job->device->customer->full_name }}<div class="small text-secondary">{{ $job->device->label }}</div>
</td>
<td>{{ $job->technician?->full_name ?? 'Unassigned' }}</td>
<td>
<span class="badge text-bg-{{ in_array($job->status,['Completed','Released'])?'success':($job->status==='Cancelled'?'secondary':'primary') }}">{{ $job->status }}</span>
</td>
<td>₱{{ number_format($job->total_cost,2) }}</td>
<td class="fw-semibold">₱{{ number_format(max(0,$job->balance_remaining),2) }}</td>
<td class="text-end pe-4">
<div class="d-flex justify-content-end gap-2">
<a class="btn btn-sm btn-outline-primary" href="{{ route('repair-jobs.show',$job) }}">Open</a>
<a class="btn btn-sm btn-outline-secondary" href="{{ route('repair-jobs.edit',$job) }}">Edit</a>
<form method="POST" action="{{ route('repair-jobs.destroy',$job) }}" onsubmit="return confirm('Delete this job and all its payments?')">
@csrf 
@method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
</form>
</div>
</td>
</tr>
@empty<tr>
<td colspan="7" class="empty-state">No repair jobs yet.</td>
</tr>
@endforelse</tbody>
</table>
</div>
</div>
@elseif ($mode === 'form')
<div class="mb-4">
<div class="eyebrow">Work orders</div>
<h1 class="fw-bold">{{ $job->exists?'Edit repair job #'.$job->job_id:'Create repair job' }}</h1>
<p class="text-secondary">Record the issue, assign a technician, and track repair costs.</p>
</div>
<form class="card" method="POST" action="{{ $job->exists?route('repair-jobs.update',$job):route('repair-jobs.store') }}">
<div class="card-body p-4">
@csrf 
@if($job->exists) 
@method('PUT') 
@endif<div class="row g-3">
<div class="col-md-6">
<label class="form-label required" for="device_id">Device</label>
<select class="form-select @error('device_id') is-invalid @enderror" id="device_id" name="device_id" required>
<option value="">Choose device</option>
@foreach($devices as $d)<option value="{{ $d->device_id }}" @selected(old('device_id',$job->device_id)==$d->device_id)>{{ $d->label }} — {{ $d->customer->full_name }}</option>
@endforeach</select>
<x-field-error field="device_id" />
</div>
<div class="col-md-6">
<label class="form-label" for="technician_id">Assigned technician</label>
<select class="form-select @error('technician_id') is-invalid @enderror" id="technician_id" name="technician_id">
<option value="">Unassigned</option>
@foreach($technicians as $t)<option value="{{ $t->technician_id }}" @selected(old('technician_id',$job->technician_id)==$t->technician_id)>{{ $t->full_name }} · {{ $t->specialization }}</option>
@endforeach</select>
<x-field-error field="technician_id" />
</div>
<div class="col-12">
<label class="form-label required" for="issue_description">Reported issue</label>
<textarea class="form-control @error('issue_description') is-invalid @enderror" id="issue_description" name="issue_description" rows="3" maxlength="1000" required>{{ old('issue_description',$job->issue_description) }}</textarea>
<x-field-error field="issue_description" />
</div>
<div class="col-md-6">
<label class="form-label" for="diagnosis">Diagnosis / work notes</label>
<textarea class="form-control @error('diagnosis') is-invalid @enderror" id="diagnosis" name="diagnosis" rows="3" maxlength="5000">{{ old('diagnosis',$job->diagnosis) }}</textarea>
<x-field-error field="diagnosis" />
</div>
<div class="col-md-6">
<label class="form-label required" for="status">Status</label>
<select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
@foreach(['Pending','Diagnosing','In Progress','Waiting for Parts','Completed','Released','Cancelled'] as $s)<option @selected(old('status',$job->status)===$s)>{{ $s }}</option>
@endforeach</select>
<x-field-error field="status" />
<div class="form-text">Setting status to Completed fills today's completion date if none is entered.</div>
</div>
<div class="col-md-3">
<label class="form-label required" for="labor_cost">Labor cost (₱)</label>
<input class="form-control @error('labor_cost') is-invalid @enderror" type="number" id="labor_cost" name="labor_cost" min="0" max="99999999.99" step="0.01" value="{{ old('labor_cost',$job->labor_cost ?? 0) }}" required>
<x-field-error field="labor_cost" />
</div>
<div class="col-md-3">
<label class="form-label required" for="parts_cost">Parts cost (₱)</label>
<input class="form-control @error('parts_cost') is-invalid @enderror" type="number" id="parts_cost" name="parts_cost" min="0" max="99999999.99" step="0.01" value="{{ old('parts_cost',$job->parts_cost ?? 0) }}" required>
<x-field-error field="parts_cost" />
</div>
<div class="col-md-3">
<label class="form-label required" for="received_date">Received date</label>
<input class="form-control @error('received_date') is-invalid @enderror" type="date" id="received_date" name="received_date" max="{{ now()->toDateString() }}" value="{{ old('received_date',optional($job->received_date)->format('Y-m-d') ?: now()->toDateString()) }}" required>
<x-field-error field="received_date" />
</div>
<div class="col-md-3">
<label class="form-label" for="completed_date">Completed date</label>
<input class="form-control @error('completed_date') is-invalid @enderror" type="date" id="completed_date" name="completed_date" max="{{ now()->toDateString() }}" value="{{ old('completed_date',optional($job->completed_date)->format('Y-m-d')) }}">
<x-field-error field="completed_date" />
</div>
</div>
</div>
<div class="card-footer bg-white p-3 d-flex gap-2">
<button class="btn btn-primary" @disabled($devices->isEmpty())>Save repair job</button>
<a class="btn btn-outline-secondary" href="{{ $job->exists?route('repair-jobs.show',$job):route('repair-jobs.index') }}">Cancel</a>
</div>
</form>
@if($devices->isEmpty())<div class="alert alert-warning mt-3">Add a customer and device before creating a repair job.</div>
@endif
@elseif ($mode === 'detail')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
<div>
<div class="eyebrow">Work order details</div>
<h1 class="fw-bold">Repair Job #{{ $job->job_id }}</h1>
<p class="text-secondary mb-0">Received {{ $job->received_date->format('M d, Y') }}</p>
</div>
<div class="d-flex gap-2">
<a class="btn btn-outline-secondary" href="{{ route('repair-jobs.index') }}">Back</a>
<a class="btn btn-primary" href="{{ route('repair-jobs.edit',$job) }}">Edit job</a>
<a class="btn btn-success" href="{{ route('payments.create',['job_id'=>$job->job_id]) }}">＋ Record payment</a>
</div>
</div>
<div class="row g-3 mb-4">
<div class="col-lg-8">
<div class="card h-100">
<div class="card-body p-4">
<div class="d-flex justify-content-between gap-3">
<h2 class="h5 fw-bold">Issue and repair</h2>
<span class="badge text-bg-primary align-self-start">{{ $job->status }}</span>
</div>
<p>{{ $job->issue_description }}</p>
<div class="text-secondary small">Diagnosis / work notes</div>
<p class="mb-4">{{ $job->diagnosis ?: 'No diagnosis recorded yet.' }}</p>
<div class="row g-3">
<div class="col-sm-6">
<div class="text-secondary small">Customer</div>
<div class="fw-semibold">{{ $job->device->customer->full_name }}</div>
<div class="small">{{ $job->device->customer->phone }}</div>
</div>
<div class="col-sm-6">
<div class="text-secondary small">Device</div>
<div class="fw-semibold">{{ $job->device->label }}</div>
<div class="small">Serial: {{ $job->device->serial_number ?: 'Not recorded' }}</div>
</div>
<div class="col-sm-6">
<div class="text-secondary small">Assigned technician</div>
<div class="fw-semibold">{{ $job->technician?->full_name ?? 'Unassigned' }}</div>
<div class="small">{{ $job->technician?->specialization ?? '' }}</div>
</div>
<div class="col-sm-6">
<div class="text-secondary small">Completed date</div>
<div class="fw-semibold">{{ $job->completed_date?->format('M d, Y') ?? 'Not completed' }}</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-4">
<div class="card h-100">
<div class="card-body p-4">
<h2 class="h5 fw-bold">Payment summary</h2>
<div class="d-flex justify-content-between py-2">
<span>Labor</span>
<span>₱{{ number_format($job->labor_cost,2) }}</span>
</div>
<div class="d-flex justify-content-between py-2 border-bottom">
<span>Parts</span>
<span>₱{{ number_format($job->parts_cost,2) }}</span>
</div>
<div class="d-flex justify-content-between py-3 fw-bold">
<span>Total</span>
<span>₱{{ number_format($job->total_cost,2) }}</span>
</div>
<div class="d-flex justify-content-between pb-2">
<span>Paid</span>
<span class="text-success">₱{{ number_format($job->total_paid,2) }}</span>
</div>
<div class="d-flex justify-content-between fs-5 fw-bold">
<span>Balance</span>
<span>₱{{ number_format(max(0,$job->balance_remaining),2) }}</span>
</div>
</div>
</div>
</div>
</div>
<div class="card">
<div class="card-header bg-white p-3 d-flex justify-content-between align-items-center">
<h2 class="h5 fw-bold mb-0">Payment history</h2>
<a href="{{ route('payments.create',['job_id'=>$job->job_id]) }}" class="btn btn-sm btn-outline-primary">Add payment</a>
</div>
<div class="table-responsive">
<table class="table mb-0 align-middle">
<thead>
<tr>
<th class="ps-4">Date</th>
<th>Method</th>
<th>Reference</th>
<th class="text-end pe-4">Amount</th>
</tr>
</thead>
<tbody>
@forelse($job->payments->sortByDesc('payment_date') as $payment)<tr>
<td class="ps-4">{{ $payment->payment_date->format('M d, Y') }}</td>
<td>{{ $payment->payment_method }}</td>
<td>{{ $payment->reference_number ?: '—' }}</td>
<td class="text-end pe-4">₱{{ number_format($payment->amount_paid,2) }} <a class="ms-2" href="{{ route('payments.show',$payment) }}">View</a> <a class="ms-2" href="{{ route('payments.edit',$payment) }}">Edit</a>
</td>
</tr>
@empty<tr>
<td colspan="4" class="empty-state">No payments recorded for this job.</td>
</tr>
@endforelse</tbody>
</table>
</div>
</div>
@endif
@endsection

