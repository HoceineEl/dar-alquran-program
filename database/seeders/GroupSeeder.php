<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $managers = User::all()->pluck('id')->toArray();
        for ($i = 0; $i < 3; $i++) {
            $group = Group::create([
                'name' => 'المجموعة '.$i + 1,
                'type' => rand(0, 1) ? 'half_page' : 'two_lines',
            ]);
            $group->managers()->attach(Arr::random($managers, rand(1, 3)));
        }
    }
}
