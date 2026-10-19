<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\RepairJob;
use App\Models\Technician;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RepairJob> */
class RepairJobFactory extends Factory
{
    protected $model = RepairJob::class;

    public function definition(): array
    {
        $receivedDate = fake()->dateTimeBetween('-90 days', 'now');
        $status = fake()->randomElement([
            'Pending',
            'Diagnosing',
            'In Progress',
            'Waiting for Parts',
            'Completed',
            'Released',
        ]);
        $isCompleted = in_array($status, ['Completed', 'Released'], true);

        return [
            'device_id' => Device::factory(),
            'technician_id' => Technician::factory(),
            'issue_description' => fake()->randomElement([
                'Device will not power on.',
                'Battery drains unusually quickly.',
                'Screen flickers during use.',
                'Device overheats under normal load.',
                'Operating system needs troubleshooting.',
            ]),
            'status' => $status,
            'diagnosis' => fake()->optional()->sentence(),
            'labor_cost' => fake()->randomFloat(2, 250, 3000),
            'parts_cost' => fake()->randomFloat(2, 0, 10000),
            'received_date' => $receivedDate->format('Y-m-d'),
            'completed_date' => $isCompleted
                ? fake()->dateTimeBetween($receivedDate->format('Y-m-d'), 'now')->format('Y-m-d')
                : null,
        ];
    }
}
