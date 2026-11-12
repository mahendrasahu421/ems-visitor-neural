<?php

namespace Database\Factories;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'first_name'   => $this->faker->firstName(),
            'last_name'    => $this->faker->lastName(),
            'username'     => $this->faker->unique()->userName(),
            'email'        => $this->faker->unique()->safeEmail(),
            'password'     => Hash::make('password'), // sabke liye default password
            'phone'        => $this->faker->phoneNumber(),
            'company_name' => $this->faker->company(),
            'profile'      => 'clients/default.png', // ya placeholder
            'status'       => $this->faker->randomElement(['Active', 'Inactive']),
            'created_at'   => now()->subDays(rand(0, 60)), // random old date
        ];
    }
}
