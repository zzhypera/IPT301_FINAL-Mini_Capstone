@extends('app')

@section('title', $mode === 'form' ? ($technician->exists ? 'Edit Technician' : 'Add Technician') : 'Technicians')

@section('content')
@if ($mode === 'index')
<div class="d-flex justify-content-between align-items-end gap-3 mb-4">
<div>
<div class="eyebrow">Staff directory</div>
<h1 class="fw-bold">Technicians</h1>
<p class="text-secondary mb-0">Manage staff skills and work assignments.</p>
</div>
<a class="btn btn-primary" href="{{ route('technicians.create') }}">＋ Add technician</a>
</div>
<div class="card">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead>
<tr>
<th class="ps-4">Name</th>
<th>Specialization</th>
<th>Hire date</th>
<th>Assigned jobs</th>
<th class="text-end pe-4">Actions</th>
</tr>
</thead>
<tbody>
@forelse($technicians as $technician)<tr>
<td class="ps-4 fw-semibold">{{ $technician->full_name }}<div class="small text-secondary">{{ $technician->phone ?: 'No phone' }}</div>
</td>
<td>{{ $technician->specialization }}</td>
<td>{{ $technician->hire_date->format('M d, Y') }}</td>
<td>{{ $technician->repair_jobs_count }}</td>
<td class="text-end pe-4">
<div class="d-flex justify-content-end gap-2">
<a class="btn btn-sm btn-outline-primary" href="{{ route('technicians.show',$technician) }}">View</a>
<a class="btn btn-sm btn-outline-secondary" href="{{ route('technicians.edit',$technician) }}">Edit</a>
<form method="POST" action="{{ route('technicians.destroy',$technician) }}" onsubmit="return confirm('Delete this technician? Assigned jobs will become unassigned.')">
@csrf 
@method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
</form>
</div>
</td>
</tr>
@empty<tr>
<td colspan="5" class="empty-state">No technicians added yet.</td>
</tr>
@endforelse</tbody>
</table>
</div>
</div>
@elseif ($mode === 'form')
<div class="mb-4">
<div class="eyebrow">Staff directory</div>
<h1 class="fw-bold">{{ $technician->exists?'Edit technician':'Add technician' }}</h1>
</div>
<form class="card" method="POST" action="{{ $technician->exists?route('technicians.update',$technician):route('technicians.store') }}">
<div class="card-body p-4">
@csrf 
@if($technician->exists) 
@method('PUT') 
@endif<div class="row g-3">
<div class="col-md-4">
<label class="form-label required" for="first_name">First name</label>
<input class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name',$technician->first_name) }}" maxlength="100" required>
<x-field-error field="first_name" />
</div>
<div class="col-md-4">
<label class="form-label" for="middle_name">Middle name</label>
<input class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" name="middle_name" value="{{ old('middle_name',$technician->middle_name) }}" maxlength="100">
<x-field-error field="middle_name" />
</div>
<div class="col-md-4">
<label class="form-label required" for="last_name">Last name</label>
<input class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name',$technician->last_name) }}" maxlength="100" required>
<x-field-error field="last_name" />
</div>
<div class="col-md-6">
<label class="form-label required" for="specialization">Specialization</label>
<select class="form-select @error('specialization') is-invalid @enderror" id="specialization" name="specialization" required>
@foreach(['Hardware Repair','Software Troubleshooting','Data Recovery','Networking','Laptop Repair','Other'] as $s)<option @selected(old('specialization',$technician->specialization)===$s)>{{ $s }}</option>
@endforeach</select>
<x-field-error field="specialization" />
</div>
<div class="col-md-3">
<label class="form-label required" for="hire_date">Hire date</label>
<input class="form-control @error('hire_date') is-invalid @enderror" type="date" id="hire_date" name="hire_date" value="{{ old('hire_date',optional($technician->hire_date)->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" required>
<x-field-error field="hire_date" />
</div>
<div class="col-md-3">
<label class="form-label" for="phone">Phone</label>
<input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone',$technician->phone) }}" maxlength="30">
<x-field-error field="phone" />
</div>
</div>
</div>
<div class="card-footer bg-white p-3 d-flex gap-2">
<button class="btn btn-primary">Save technician</button>
<a class="btn btn-outline-secondary" href="{{ route('technicians.index') }}">Cancel</a>
</div>
</form>
@elseif ($mode === 'detail')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <div class="eyebrow">Staff record</div>
        <h1 class="fw-bold">{{ $technician->full_name }}</h1>
        <p class="text-secondary mb-0">Technician #{{ $technician->technician_id }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('technicians.index') }}">Back to technicians</a>
        <a class="btn btn-primary" href="{{ route('technicians.edit', $technician) }}">Edit technician</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Technician information</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">Specialization</dt><dd class="col-sm-7">{{ $technician->specialization }}</dd>
                <dt class="col-sm-5">Phone</dt><dd class="col-sm-7">{{ $technician->phone ?: 'Not provided' }}</dd>
                <dt class="col-sm-5">Hire date</dt><dd class="col-sm-7">{{ $technician->hire_date->format('M d, Y') }}</dd>
                <dt class="col-sm-5">Years of service</dt><dd class="col-sm-7">{{ $technician->hire_date->diffInYears(now()) }}</dd>
            </dl>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Record dates</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">Created</dt><dd class="col-sm-7">{{ $technician->created_at?->format('M d, Y') }}</dd>
                <dt class="col-sm-5">Last updated</dt><dd class="col-sm-7">{{ $technician->updated_at?->format('M d, Y') }}</dd>
            </dl>
        </div></div>
    </div>
</div>
<div class="card">
    <div class="card-header bg-white p-3"><h2 class="h5 fw-bold mb-0">Assigned repair jobs</h2></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th class="ps-4">Job</th><th>Device / Customer</th><th>Status</th><th class="text-end pe-4">Action</th></tr></thead>
        <tbody>
            @forelse ($technician->repairJobs as $job)
                <tr>
                    <td class="ps-4"><a class="fw-semibold" href="{{ route('repair-jobs.show', $job) }}">Job #{{ $job->job_id }}</a></td>
                    <td>{{ $job->device->label }}<div class="small text-secondary">{{ $job->device->customer->full_name }}</div></td>
                    <td><span class="badge text-bg-primary">{{ $job->status }}</span></td>
                    <td class="text-end pe-4"><a class="btn btn-sm btn-outline-primary" href="{{ route('repair-jobs.show', $job) }}">View job</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-state">No repair jobs are assigned to this technician.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endif
@endsection

