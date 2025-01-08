<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'membership_id' => $this->faker->numberBetween(1, 4),
            'last_name' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'middle_initial' => strtoupper($this->faker->randomLetter()), // Single uppercase letter
            'contact_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'status' => $this->faker->boolean(100), // 80% chance of being true
            'expiry' => $this->faker->optional()->dateTimeBetween('now', '+1 year')?->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
