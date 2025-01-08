<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membership;

class membershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $memberships = [
            [
                'name' => 'Basic',
                'description' => '30-day membership with standard gym access.',
                'duration' => 30,
                'price' => 290.0,
            ],
            [
                'name' => 'Standard',
                'description' => '90-day membership with standard gym access and additional perks (e.g., discounted personal training sessions).',
                'duration' => 90,
                'price' => 520.0,
            ],
            [
                'name' => 'Premium',
                'description' => '180-day membership with premium gym access, priority booking, and exclusive fitness classes.',
                'duration' => 180,
                'price' => 800.0,
            ],
            [
                'name' => 'Platinum',
                'description' => '360-day membership with ultimate gym access, personalized training plans, and priority spa services.',
                'duration' => 360,
                'price' => 1200.0,
            ],
        ];

        foreach ($memberships as $membership) {
            Membership::create($membership);
        }
    }
}
