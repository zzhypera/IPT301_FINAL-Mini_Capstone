<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = Technician::withCount('repairJobs')->orderBy('last_name')->get();

        return view('technician', ['mode' => 'index', 'technicians' => $technicians]);
    }

    public function create()
    {
        $technician = new Technician;

        return view('technician', ['mode' => 'form', 'technician' => $technician]);
    }

    private function rules(): array
    {
        return ['first_name' => 'required|string|max:100', 'middle_name' => 'nullable|string|max:100', 'last_name' => 'required|string|max:100', 'specialization' => 'required|in:Hardware Repair,Software Troubleshooting,Data Recovery,Networking,Laptop Repair,Other', 'hire_date' => 'required|date|before_or_equal:today', 'phone' => 'nullable|string|max:30'];
    }

    private function messages(): array
    {
        return [
            'specialization.in' => 'Choose one of the listed technician specializations.',
            'hire_date.before_or_equal' => 'The hire date cannot be in the future.',
        ];
    }

    public function store(Request $request)
    {
        Technician::create($request->validate($this->rules(), $this->messages()));

        return redirect()->route('technicians.index')->with('status', 'Technician created successfully.');
    }

    public function edit(Technician $technician)
    {
        return view('technician', ['mode' => 'form', 'technician' => $technician]);
    }

    public function show(Technician $technician)
    {
        $technician->load('repairJobs.device.customer');

        return view('technician', ['mode' => 'detail', 'technician' => $technician]);
    }

    public function update(Request $request, Technician $technician)
    {
        $technician->update($request->validate($this->rules(), $this->messages()));

        return redirect()->route('technicians.index')->with('status', 'Technician updated successfully.');
    }

    public function destroy(Technician $technician)
    {
        $technician->delete();

        return redirect()->route('technicians.index')->with('status', 'Technician deleted; assigned jobs are now unassigned.');
    }
}
