<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('devices')->orderBy('last_name')->get();

        return view('customer', ['mode' => 'index', 'customers' => $customers]);
    }

    public function create()
    {
        $customer = new Customer;

        return view('customer', ['mode' => 'form', 'customer' => $customer]);
    }

    private function rules($id = null): array
    {
        return ['first_name' => 'required|string|max:100', 'middle_name' => 'nullable|string|max:100', 'last_name' => 'required|string|max:100', 'phone' => 'required|string|max:30', 'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($id, 'customer_id')], 'street' => 'nullable|string|max:255', 'city' => 'nullable|string|max:100', 'province' => 'nullable|string|max:100'];
    }

    private function messages(): array
    {
        return [
            'email.unique' => 'That email address is already registered to another customer.',
            'email.email' => 'Enter a valid email address, or leave the email field blank.',
        ];
    }

    public function store(Request $request)
    {
        Customer::create($request->validate($this->rules(), $this->messages()));

        return redirect()->route('customers.index')->with('status', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customer', ['mode' => 'form', 'customer' => $customer]);
    }

    public function show(Customer $customer)
    {
        $customer->load('devices.repairJobs');

        return view('customer', ['mode' => 'detail', 'customer' => $customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->validate($this->rules($customer->customer_id), $this->messages()));

        return redirect()->route('customers.index')->with('status', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Customer and related devices/jobs/payments deleted.');
    }
}
