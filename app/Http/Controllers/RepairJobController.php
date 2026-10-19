<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\RepairJob;
use App\Models\Technician;
use Illuminate\Http\Request;

class RepairJobController extends Controller
{
    public function index()
    {
        $jobs = RepairJob::with(['device.customer', 'technician'])->withSum('payments', 'amount_paid')->latest()->get();

        return view('repair-job', ['mode' => 'index', 'jobs' => $jobs]);
    }

    public function create()
    {
        $job = new RepairJob(['device_id' => request('device_id'), 'received_date' => now()->toDateString(), 'status' => 'Pending']);
        $devices = Device::with('customer')->get();
        $technicians = Technician::orderBy('last_name')->get();

        return view('repair-job', ['mode' => 'form', 'job' => $job, 'devices' => $devices, 'technicians' => $technicians]);
    }

    private function rules(): array
    {
        return ['device_id' => 'required|exists:devices,device_id', 'technician_id' => 'nullable|exists:technicians,technician_id', 'issue_description' => 'required|string|max:1000', 'status' => 'required|in:Pending,Diagnosing,In Progress,Waiting for Parts,Completed,Released,Cancelled', 'diagnosis' => 'nullable|string|max:5000', 'labor_cost' => 'required|numeric|min:0|max:99999999.99', 'parts_cost' => 'required|numeric|min:0|max:99999999.99', 'received_date' => 'required|date|before_or_equal:today', 'completed_date' => 'nullable|date|after_or_equal:received_date|before_or_equal:today'];
    }

    private function messages(): array
    {
        return ['device_id.exists' => 'Select a device that is still registered.', 'technician_id.exists' => 'Select a technician that is still on the staff list.', 'received_date.before_or_equal' => 'The received date cannot be in the future.', 'completed_date.after_or_equal' => 'The completion date must be on or after the received date.', 'completed_date.before_or_equal' => 'The completion date cannot be in the future.', 'labor_cost.numeric' => 'Enter a valid labor cost.', 'parts_cost.numeric' => 'Enter a valid parts cost.'];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());
        if ($data['status'] === 'Completed' && empty($data['completed_date'])) {
            $data['completed_date'] = now()->toDateString();
        } RepairJob::create($data);

        return redirect()->route('repair-jobs.index')->with('status', 'Repair job created successfully.');
    }

    public function show(RepairJob $repairJob)
    {
        $repairJob->load(['device.customer', 'technician', 'payments']);

        return view('repair-job', ['mode' => 'detail', 'job' => $repairJob]);
    }

    public function edit(RepairJob $repairJob)
    {
        $job = $repairJob;
        $devices = Device::with('customer')->get();
        $technicians = Technician::orderBy('last_name')->get();

        return view('repair-job', ['mode' => 'form', 'job' => $job, 'devices' => $devices, 'technicians' => $technicians]);
    }

    public function update(Request $request, RepairJob $repairJob)
    {
        $data = $request->validate($this->rules(), $this->messages());
        if ($data['status'] === 'Completed' && empty($data['completed_date'])) {
            $data['completed_date'] = now()->toDateString();
        } $repairJob->update($data);

        return redirect()->route('repair-jobs.show', $repairJob)->with('status', 'Repair job updated successfully.');
    }

    public function destroy(RepairJob $repairJob)
    {
        $repairJob->delete();

        return redirect()->route('repair-jobs.index')->with('status', 'Repair job and related payments deleted.');
    }
}
