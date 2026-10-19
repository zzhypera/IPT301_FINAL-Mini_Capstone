<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::with('customer')->latest()->get();

        return view('device', ['mode' => 'index', 'devices' => $devices]);
    }

    public function create()
    {
        $device = new Device(['customer_id' => request('customer_id')]);
        $customers = Customer::orderBy('last_name')->get();

        return view('device', ['mode' => 'form', 'device' => $device, 'customers' => $customers]);
    }

    private function rules(): array
    {
        return ['customer_id' => 'required|exists:customers,customer_id', 'brand' => 'required|string|max:100', 'model' => 'required|string|max:100', 'serial_number' => 'nullable|string|max:100|unique:devices,serial_number'.(request()->route('device') ? ','.request()->route('device')->device_id.',device_id' : ''), 'accessories' => 'nullable|string|max:2000'];
    }

    private function messages(): array
    {
        return [
            'customer_id.exists' => 'Select a customer that is still registered.',
            'serial_number.unique' => 'That serial number is already assigned to another device.',
        ];
    }

    public function store(Request $request)
    {
        Device::create($request->validate($this->rules(), $this->messages()));

        return redirect()->route('devices.index')->with('status', 'Device created successfully.');
    }

    public function edit(Device $device)
    {
        $customers = Customer::orderBy('last_name')->get();

        return view('device', ['mode' => 'form', 'device' => $device, 'customers' => $customers]);
    }

    public function show(Device $device)
    {
        $device->load(['customer', 'repairJobs']);

        return view('device', ['mode' => 'detail', 'device' => $device]);
    }

    public function update(Request $request, Device $device)
    {
        $rules = $this->rules();
        $rules['serial_number'] = 'nullable|string|max:100|unique:devices,serial_number,'.$device->device_id.',device_id';
        $device->update($request->validate($rules, $this->messages()));

        return redirect()->route('devices.index')->with('status', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')->with('status', 'Device and related repair jobs/payments deleted.');
    }
}
