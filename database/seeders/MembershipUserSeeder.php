<?php

namespace Database\Seeders;

use App\Models\Members;  // This should point to your actual model class
use Illuminate\Database\Seeder;

class MembershipUserSeeder extends Seeder
{
    public function run()
    {
        Members::factory(150)->create();  // Use the actual model name (Members)
    }
}
