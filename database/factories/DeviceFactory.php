<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Device> */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        [$brand, $model] = fake()->randomElement([
            ['Acer', 'Aspire 5'],
            ['ASUS', 'VivoBook 14'],
            ['Dell', 'Inspiron 15'],
            ['HP', 'Pavilion 15'],
            ['Lenovo', 'ThinkPad E14'],
        ]);

        return [
            'customer_id' => Customer::factory(),
            'brand' => $brand,
            'model' => $model,
            'serial_number' => fake()->unique()->bothify('SN-########'),
            'accessories' => fake()->randomElement(['Charger', 'Charger, laptop bag', 'Charger, mouse']),
        ];
    }
}
