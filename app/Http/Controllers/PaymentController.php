<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\RepairJob;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('repairJob.device.customer')->latest('payment_date')->get();

        return view('payment', ['mode' => 'index', 'payments' => $payments]);
    }

    public function create()
    {
        $payment = new Payment(['job_id' => request('job_id'), 'payment_date' => now()->toDateString(), 'payment_method' => 'Cash']);
        $jobs = RepairJob::with('device.customer')->get();

        return view('payment', ['mode' => 'form', 'payment' => $payment, 'jobs' => $jobs]);
    }

    private function rules(): array
    {
        return ['job_id' => 'required|exists:repair_jobs,job_id', 'payment_date' => 'required|date|before_or_equal:today', 'amount_paid' => 'required|numeric|min:0.01|max:99999999.99'];
    }

    private function messages(): array
    {
        return [
            'job_id.exists' => 'Select a repair job that is still available.',
            'payment_date.before_or_equal' => 'The payment date cannot be in the future.',
            'amount_paid.min' => 'Enter a payment amount greater than zero.',
            'amount_paid.numeric' => 'Enter a valid payment amount.',
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());
        $job = RepairJob::findOrFail($data['job_id']);
        $paid = (float) $job->payments()->sum('amount_paid');
        if ($paid + (float) $data['amount_paid'] > $job->total_cost + 0.009) {
            return back()->withInput()->withErrors(['amount_paid' => 'Payment exceeds the remaining balance of ₱'.number_format(max(0, $job->total_cost - $paid), 2).'.']);
        } Payment::create($data);

        return redirect()->route('payments.index')->with('status', 'Payment recorded successfully.');
    }

    public function edit(Payment $payment)
    {
        $jobs = RepairJob::with('device.customer')->get();

        return view('payment', ['mode' => 'form', 'payment' => $payment, 'jobs' => $jobs]);
    }

    public function show(Payment $payment)
    {
        $payment->load('repairJob.device.customer');

        return view('payment', ['mode' => 'detail', 'payment' => $payment]);
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate($this->rules(), $this->messages());
        $job = RepairJob::findOrFail($data['job_id']);
        $paid = (float) $job->payments()->where('payment_id', '!=', $payment->payment_id)->sum('amount_paid');
        if ($paid + (float) $data['amount_paid'] > $job->total_cost + 0.009) {
            return back()->withInput()->withErrors(['amount_paid' => 'Payment exceeds the remaining balance of ₱'.number_format(max(0, $job->total_cost - $paid), 2).'.']);
        } $payment->update($data);

        return redirect()->route('payments.index')->with('status', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('status', 'Payment deleted successfully.');
    }
}
