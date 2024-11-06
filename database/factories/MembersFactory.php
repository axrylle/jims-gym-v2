<?php

namespace Database\Factories;

use App\Models\Members;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembersFactory extends Factory
{
    protected $model = Members::class;

    public function definition()
    {
        return [
            'membership_id' => $this->faker->numberBetween(1, 4),
            'last_name' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'middle_initial' => strtoupper($this->faker->randomLetter()),  // Single uppercase letter for middle initial
            'contact_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'status' => $this->faker->boolean(80),  // 80% chance of being active (true)
            'expiry' => $this->faker->optional()->dateTimeBetween('now', '+1 year')?->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
