<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Payment;
use App\Models\RepairJob;
use App\Models\Technician;
use Illuminate\Database\Seeder;

class RepairShopSeeder extends Seeder
{
    public function run(): void
    {
        /** Create the technicians and customers used by the sample repair data. */
        $technicians = Technician::factory()->count(5)->create();
        $customers = Customer::factory()->count(10)->create();

        $customers->each(function (Customer $customer) use ($technicians): void {
            /** Give each customer one or two devices to build a varied sample dataset. */
            $devices = Device::factory()
                ->count(fake()->numberBetween(1, 2))
                ->for($customer, 'customer')
                ->create();

            $devices->each(function (Device $device) use ($technicians): void {
                $jobCount = fake()->numberBetween(1, 2);

                for ($index = 0; $index < $jobCount; $index++) {
                    /** Assign each repair job to a random technician. */
                    $job = RepairJob::factory()
                        ->for($device, 'device')
                        ->for($technicians->random(), 'technician')
                        ->create();

                    /** Keep the payment date on or after the job's received date. */
                    Payment::factory()
                        ->for($job, 'repairJob')
                        ->create([
                            'payment_date' => fake()
                                ->dateTimeBetween($job->received_date->format('Y-m-d'), 'now')
                                ->format('Y-m-d'),
                        ]);
                }
            });
        });
    }
}
