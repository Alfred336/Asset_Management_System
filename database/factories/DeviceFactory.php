<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Device;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'staff_id' => Staff::factory(),
            'asset_tag' => 'AST-'.$this->faker->unique()->numberBetween(1000, 9999),
            'serial_number' => $this->faker->unique()->bothify('SN-####-????'),
            'model' => $this->faker->word().' '.$this->faker->numberBetween(100, 900),
            'manufacturer' => $this->faker->company(),
            'device_type' => $this->faker->randomElement(['Laptop', 'Desktop', 'Server', 'Tablet', 'Mobile']),
            'status' => 'active',
            'purchase_date' => $this->faker->date(),
            'warranty_expiry' => $this->faker->dateTimeBetween('now', '+3 years'),
        ];
    }
}
