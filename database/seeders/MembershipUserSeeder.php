<?php

namespace Database\Seeders;

use App\Models\Member;  // This should point to your actual model class
use Illuminate\Database\Seeder;

class MembershipUserSeeder extends Seeder
{
    public function run()
    {
        Member::factory(25)->create();  // Use the actual model name (Members)
    }
}
