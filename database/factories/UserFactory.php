<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        $firstname = $this->faker->firstName();
        $lastname = $this->faker->lastName();
        $username = strtolower($firstname . '.' . $lastname . $this->faker->randomNumber(3));
        $roles = ['admin', 'receptionist', 'cashier', 'kitchen'];

        return [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'role' => $this->faker->randomElement($roles),
            'contact_number' => $this->faker->phoneNumber(),
            'username' => $username,
            'password' => Hash::make('password'), // default password for seeded users
            'status' => 'Active',
        ];
    }
}
