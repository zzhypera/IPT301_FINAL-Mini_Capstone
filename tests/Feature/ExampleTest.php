<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJob;
use App\Models\Technician;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk()->assertSee('Repair Shop Dashboard');
    }

    public function test_customer_can_be_created_and_listed(): void
    {
        $this->post(route('customers.store'), [
            'first_name' => 'Alex', 'last_name' => 'Santos', 'phone' => '09170000000',
            'email' => 'alex@example.test', 'city' => 'Tagbilaran', 'province' => 'Bohol',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', ['email' => 'alex@example.test']);
        $this->get(route('customers.index'))->assertOk()->assertSee('Alex Santos');
    }

    public function test_repair_job_and_payment_are_connected_and_overpayment_is_rejected(): void
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->customer_id]);
        $technician = Technician::factory()->create();
        $this->post(route('repair-jobs.store'), [
            'device_id' => $device->device_id, 'technician_id' => $technician->technician_id,
            'issue_description' => 'Screen does not turn on', 'status' => 'Pending',
            'diagnosis' => 'Needs testing', 'labor_cost' => 500, 'parts_cost' => 1000,
            'received_date' => now()->toDateString(), 'completed_date' => '',
        ])->assertRedirect(route('repair-jobs.index'));

        $job = RepairJob::firstOrFail();
        $this->assertSame($technician->technician_id, $job->technician_id);
        $this->post(route('payments.store'), [
            'job_id' => $job->job_id, 'payment_date' => now()->toDateString(),
            'amount_paid' => 300, 'payment_method' => 'GCash', 'reference_number' => 'TEST-1',
        ])->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('payments', ['job_id' => $job->job_id, 'amount_paid' => 300]);

        $this->from(route('payments.create'))->post(route('payments.store'), [
            'job_id' => $job->job_id, 'payment_date' => now()->toDateString(),
            'amount_paid' => 1300, 'payment_method' => 'Cash',
        ])->assertSessionHasErrors('amount_paid');
    }
}
