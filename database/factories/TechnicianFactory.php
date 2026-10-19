<?php

namespace Database\Factories;

use App\Models\Technician;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Technician> */
class TechnicianFactory extends Factory
{
    protected $model = Technician::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional(0.5)->lastName(),
            'last_name' => fake()->lastName(),
            'specialization' => fake()->randomElement([
                'Hardware Repair',
                'Software Troubleshooting',
                'Data Recovery',
                'Networking',
                'Laptop Repair',
            ]),
            'hire_date' => fake()->dateTimeBetween('-8 years', '-1 month')->format('Y-m-d'),
            'phone' => fake()->numerify('09#########'),
        ];
    }
}
