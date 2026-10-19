<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        [$city, $province] = fake()->randomElement([
            ['Tagbilaran City', 'Bohol'],
            ['Cebu City', 'Cebu'],
            ['Iloilo City', 'Iloilo'],
            ['Davao City', 'Davao del Sur'],
            ['Quezon City', 'Metro Manila'],
        ]);

        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional(0.5)->lastName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->numerify('09#########'),
            'email' => fake()->unique()->safeEmail(),
            'street' => fake()->buildingNumber().' '.fake()->streetName(),
            'city' => $city,
            'province' => $province,
        ];
    }
}
