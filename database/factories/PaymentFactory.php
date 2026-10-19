<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\RepairJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'job_id' => RepairJob::factory(),
            'payment_date' => fake()->dateTimeBetween('-90 days', 'now')->format('Y-m-d'),
            // Repair jobs cost at least ₱250, so this single sample payment stays within the job total.
            'amount_paid' => fake()->randomFloat(2, 50, 150),
            'payment_method' => fake()->randomElement(['Cash', 'GCash', 'Bank Transfer', 'Card', 'Other']),
            'reference_number' => fake()->optional()->bothify('REF-########'),
        ];
    }
}
