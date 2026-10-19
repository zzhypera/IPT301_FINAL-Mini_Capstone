@extends('app')

@section('title', $mode === 'form' ? ($customer->exists ? 'Edit Customer' : 'Add Customer') : 'Customers')

@section('content')
@if ($mode === 'index')
<div class="d-flex justify-content-between align-items-end gap-3 mb-4">
<div>
<div class="eyebrow">Customer records</div>
<h1 class="fw-bold">Customers</h1>
<p class="text-secondary mb-0">Manage customer contact details and registered devices.</p>
</div>
<a class="btn btn-primary" href="{{ route('customers.create') }}">＋ Add customer</a>
</div>
<div class="card">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead>
<tr>
<th class="ps-4">Name</th>
<th>Contact</th>
<th>Address</th>
<th>Devices</th>
<th class="text-end pe-4">Actions</th>
</tr>
</thead>
<tbody>
@forelse($customers as $customer)<tr>
<td class="ps-4 fw-semibold">{{ $customer->full_name }}</td>
<td>{{ $customer->phone }}<div class="small text-secondary">{{ $customer->email ?: 'No email' }}</div>
</td>
<td>{{ $customer->full_address ?: '—' }}</td>
<td>
<span class="badge text-bg-light">{{ $customer->devices_count }}</span>
</td>
<td class="text-end pe-4">
<div class="d-flex justify-content-end gap-2">
<a class="btn btn-sm btn-outline-primary" href="{{ route('customers.show',$customer) }}">View</a>
<a class="btn btn-sm btn-outline-primary" href="{{ route('devices.create',['customer_id'=>$customer->customer_id]) }}">Add device</a>
<a class="btn btn-sm btn-outline-secondary" href="{{ route('customers.edit',$customer) }}">Edit</a>
<form method="POST" action="{{ route('customers.destroy',$customer) }}" onsubmit="return confirm('Delete this customer and all related devices, repair jobs, and payments?')">
@csrf 
@method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
</form>
</div>
</td>
</tr>
@empty<tr>
<td colspan="5" class="empty-state">No customers found. Add a customer to begin.</td>
</tr>
@endforelse</tbody>
</table>
</div>
</div>
@elseif ($mode === 'form')
<div class="mb-4">
<div class="eyebrow">Customer records</div>
<h1 class="fw-bold">{{ $customer->exists?'Edit customer':'Add customer' }}</h1>
<p class="text-secondary">Enter accurate contact information for repair updates.</p>
</div>
<form class="card" method="POST" action="{{ $customer->exists?route('customers.update',$customer):route('customers.store') }}">
<div class="card-body p-4">
@csrf 
@if($customer->exists) 
@method('PUT') 
@endif<div class="row g-3">
<div class="col-md-4">
<label class="form-label required" for="first_name">First name</label>
<input class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name',$customer->first_name) }}" maxlength="100" required>
<x-field-error field="first_name" />
</div>
<div class="col-md-4">
<label class="form-label" for="middle_name">Middle name</label>
<input class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" name="middle_name" value="{{ old('middle_name',$customer->middle_name) }}" maxlength="100">
<x-field-error field="middle_name" />
</div>
<div class="col-md-4">
<label class="form-label required" for="last_name">Last name</label>
<input class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name',$customer->last_name) }}" maxlength="100" required>
<x-field-error field="last_name" />
</div>
<div class="col-md-6">
<label class="form-label required" for="phone">Phone</label>
<input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone',$customer->phone) }}" maxlength="30" required>
<x-field-error field="phone" />
</div>
<div class="col-md-6">
<label class="form-label" for="email">Email</label>
<input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email',$customer->email) }}" maxlength="255">
<x-field-error field="email" />
</div>
<div class="col-md-4">
<label class="form-label" for="street">Street / Barangay</label>
<input class="form-control @error('street') is-invalid @enderror" id="street" name="street" value="{{ old('street',$customer->street) }}" maxlength="255">
<x-field-error field="street" />
</div>
<div class="col-md-4">
<label class="form-label" for="city">City / Municipality</label>
<input class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city',$customer->city) }}" maxlength="100">
<x-field-error field="city" />
</div>
<div class="col-md-4">
<label class="form-label" for="province">Province</label>
<input class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province',$customer->province) }}" maxlength="100">
<x-field-error field="province" />
</div>
</div>
</div>
<div class="card-footer bg-white p-3 d-flex gap-2">
<button class="btn btn-primary">Save customer</button>
<a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">Cancel</a>
</div>
</form>
@elseif ($mode === 'detail')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <div class="eyebrow">Customer record</div>
        <h1 class="fw-bold">{{ $customer->full_name }}</h1>
        <p class="text-secondary mb-0">Customer #{{ $customer->customer_id }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">Back to customers</a>
        <a class="btn btn-primary" href="{{ route('customers.edit', $customer) }}">Edit customer</a>
        <a class="btn btn-success" href="{{ route('devices.create', ['customer_id' => $customer->customer_id]) }}">Add device</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Contact information</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $customer->phone }}</dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $customer->email ?: 'Not provided' }}</dd>
                <dt class="col-sm-4">Address</dt><dd class="col-sm-8">{{ $customer->full_address ?: 'Not provided' }}</dd>
            </dl>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Record dates</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">Created</dt><dd class="col-sm-7">{{ $customer->created_at?->format('M d, Y') }}</dd>
                <dt class="col-sm-5">Last updated</dt><dd class="col-sm-7">{{ $customer->updated_at?->format('M d, Y') }}</dd>
            </dl>
        </div></div>
    </div>
</div>
<div class="card">
    <div class="card-header bg-white p-3"><h2 class="h5 fw-bold mb-0">Registered devices</h2></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th class="ps-4">Device</th><th>Serial number</th><th>Repair jobs</th><th class="text-end pe-4">Action</th></tr></thead>
        <tbody>
            @forelse ($customer->devices as $device)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $device->label }}</td>
                    <td>{{ $device->serial_number ?: 'Not recorded' }}</td>
                    <td>{{ $device->repair_jobs_count ?? $device->repairJobs->count() }}</td>
                    <td class="text-end pe-4"><a class="btn btn-sm btn-outline-primary" href="{{ route('devices.show', $device) }}">View device</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-state">This customer has no registered devices.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endif
@endsection

