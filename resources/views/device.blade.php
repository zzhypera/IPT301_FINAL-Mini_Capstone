@extends('app')

@section('title', $mode === 'form' ? ($device->exists ? 'Edit Device' : 'Add Device') : 'Devices')

@section('content')
@if ($mode === 'index')
<div class="d-flex justify-content-between align-items-end gap-3 mb-4">
<div>
<div class="eyebrow">Inventory</div>
<h1 class="fw-bold">Customer Devices</h1>
<p class="text-secondary mb-0">Devices registered for inspection and repair.</p>
</div>
<a class="btn btn-primary" href="{{ route('devices.create') }}">＋ Add device</a>
</div>
<div class="card">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead>
<tr>
<th class="ps-4">Device</th>
<th>Owner</th>
<th>Serial number</th>
<th>Accessories</th>
<th class="text-end pe-4">Actions</th>
</tr>
</thead>
<tbody>
@forelse($devices as $device)<tr>
<td class="ps-4 fw-semibold">{{ $device->label }}</td>
<td>{{ $device->customer->full_name }}</td>
<td>{{ $device->serial_number ?: '—' }}</td>
<td>{{ $device->accessories ?: '—' }}</td>
<td class="text-end pe-4">
<div class="d-flex justify-content-end gap-2">
<a class="btn btn-sm btn-outline-primary" href="{{ route('devices.show',$device) }}">View</a>
<a class="btn btn-sm btn-outline-primary" href="{{ route('repair-jobs.create',['device_id'=>$device->device_id]) }}">New job</a>
<a class="btn btn-sm btn-outline-secondary" href="{{ route('devices.edit',$device) }}">Edit</a>
<form method="POST" action="{{ route('devices.destroy',$device) }}" onsubmit="return confirm('Delete this device and its repair history/payments?')">
@csrf 
@method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
</form>
</div>
</td>
</tr>
@empty<tr>
<td colspan="5" class="empty-state">No devices registered yet.</td>
</tr>
@endforelse</tbody>
</table>
</div>
</div>
@elseif ($mode === 'form')
<div class="mb-4">
        <div class="eyebrow">Inventory</div>
        <h1 class="fw-bold">{{ $device->exists ? 'Edit device' : 'Add device' }}</h1>
        <p class="text-secondary">Connect each device to its registered owner.</p>
    </div>

    
@if ($customers->isEmpty())
        <div class="alert alert-warning d-flex align-items-start" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 mb-1">
</i>
            <div>
                <strong>No customers found.</strong> You must create a customer before registering a device.
                <a href="{{ route('customers.create') }}" class="alert-link fw-semibold">Create a customer now</a>
            </div>
        </div>
    
@endif

    <form class="card" method="POST"
          action="{{ $device->exists ? route('devices.update', $device) : route('devices.store') }}">
        <div class="card-body p-4">
            
@csrf

            
@if ($device->exists)
                
@method('PUT')
            
@endif

            <div class="row g-3">
                {{-- Owner --}}
                <div class="col-12">
                    <label class="form-label required" for="customer_id">Owner</label>
                    <select
                        class="form-select 
@error('customer_id') is-invalid 
@enderror"
                        id="customer_id"
                        name="customer_id"
                        required>
                        <option value="">Choose a customer</option>
                        
@foreach ($customers as $c)
                            <option value="{{ $c->customer_id }}"
                                @selected(old('customer_id', $device->customer_id) == $c->customer_id)>
                                {{ $c->full_name }} — {{ $c->phone }}
                            </option>
                        
@endforeach
                    </select>
                    
@error('customer_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    
@enderror
                    
@if ($customers->isEmpty())
                        <div class="form-text">Create a customer before adding a device.</div>
                    
@endif
                </div>

                {{-- Brand --}}
                <div class="col-md-6">
                    <label class="form-label required" for="brand">Brand</label>
                    <div class="input-group">
                        <span class="input-group-text">
<i class="bi bi-box-seam">
</i>
</span>
                        <input
                            class="form-control 
@error('brand') is-invalid 
@enderror"
                            id="brand"
                            name="brand"
                            value="{{ old('brand', $device->brand) }}"
                            maxlength="100"
                            placeholder="e.g. Apple, Dell, HP"
                            required>
                        
@error('brand')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        
@enderror
                    </div>
                </div>

                {{-- Model --}}
                <div class="col-md-6">
                    <label class="form-label required" for="model">Model</label>
                    <div class="input-group">
                        <span class="input-group-text">
<i class="bi bi-tag">
</i>
</span>
                        <input
                            class="form-control 
@error('model') is-invalid 
@enderror"
                            id="model"
                            name="model"
                            value="{{ old('model', $device->model) }}"
                            maxlength="100"
                            placeholder="e.g. MacBook Pro, XPS 15"
                            required>
                        
@error('model')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        
@enderror
                    </div>
                </div>

                {{-- Serial number --}}
                <div class="col-md-6">
                    <label class="form-label" for="serial_number">Serial number</label>
                    <div class="input-group">
                        <span class="input-group-text">
<i class="bi bi-key">
</i>
</span>
                        <input
                            class="form-control 
@error('serial_number') is-invalid 
@enderror"
                            id="serial_number"
                            name="serial_number"
                            value="{{ old('serial_number', $device->serial_number) }}"
                            maxlength="100"
                            placeholder="Manufacturer serial">
                        
@error('serial_number')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        
@enderror
                    </div>
                    <div class="form-text">Unique identifier found on the device label.</div>
                </div>

                {{-- Accessories --}}
                <div class="col-md-6">
                    <label class="form-label" for="accessories">Accessories received</label>
                    <div class="input-group">
                        <span class="input-group-text">
<i class="bi bi-tools">
</i>
</span>
                        <input
                            class="form-control 
@error('accessories') is-invalid 
@enderror"
                            id="accessories"
                            name="accessories"
                            value="{{ old('accessories', $device->accessories) }}"
                            maxlength="2000"
                            placeholder="Charger, bag, mouse, etc.">
                        
@error('accessories')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        
@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white p-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary" @disabled($customers->isEmpty())>
                <i class="bi bi-save">
</i> {{ $device->exists ? 'Update device' : 'Save device' }}
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('devices.index') }}">
                <i class="bi bi-x-lg">
</i> Cancel
            </a>
        </div>
    </form>
@elseif ($mode === 'detail')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <div class="eyebrow">Device record</div>
        <h1 class="fw-bold">{{ $device->label }}</h1>
        <p class="text-secondary mb-0">Device #{{ $device->device_id }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('devices.index') }}">Back to devices</a>
        <a class="btn btn-primary" href="{{ route('devices.edit', $device) }}">Edit device</a>
        <a class="btn btn-success" href="{{ route('repair-jobs.create', ['device_id' => $device->device_id]) }}">New repair job</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Device information</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">Brand</dt><dd class="col-sm-7">{{ $device->brand }}</dd>
                <dt class="col-sm-5">Model</dt><dd class="col-sm-7">{{ $device->model }}</dd>
                <dt class="col-sm-5">Serial number</dt><dd class="col-sm-7">{{ $device->serial_number ?: 'Not recorded' }}</dd>
                <dt class="col-sm-5">Accessories</dt><dd class="col-sm-7">{{ $device->accessories ?: 'None recorded' }}</dd>
            </dl>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 fw-bold">Registered owner</h2>
            <p class="mb-1"><a class="fw-semibold" href="{{ route('customers.show', $device->customer) }}">{{ $device->customer->full_name }}</a></p>
            <p class="text-secondary mb-1">{{ $device->customer->phone }}</p>
            <p class="text-secondary mb-0">{{ $device->customer->email ?: 'No email provided' }}</p>
        </div></div>
    </div>
</div>
<div class="card">
    <div class="card-header bg-white p-3"><h2 class="h5 fw-bold mb-0">Repair history</h2></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th class="ps-4">Job</th><th>Reported issue</th><th>Status</th><th class="text-end pe-4">Total</th></tr></thead>
        <tbody>
            @forelse ($device->repairJobs as $job)
                <tr>
                    <td class="ps-4"><a class="fw-semibold" href="{{ route('repair-jobs.show', $job) }}">Job #{{ $job->job_id }}</a></td>
                    <td>{{ $job->issue_description }}</td>
                    <td><span class="badge text-bg-primary">{{ $job->status }}</span></td>
                    <td class="text-end pe-4">&#8369;{{ number_format($job->total_cost, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-state">No repair jobs have been recorded for this device.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endif
@endsection

