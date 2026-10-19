<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Payment;
use App\Models\RepairJob;
use App\Models\Technician;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $recentJobs = RepairJob::with(['device.customer', 'technician'])->withSum('payments', 'amount_paid')->latest()->take(8)->get();

        return view('dashboard', ['customers' => Customer::count(), 'devices' => Device::count(), 'technicians' => Technician::count(), 'jobs' => RepairJob::count(), 'collected' => (float) Payment::sum('amount_paid'), 'recentJobs' => $recentJobs, 'openJobs' => RepairJob::whereNotIn('status', ['Completed', 'Released', 'Cancelled'])->count()]);
    }
}
