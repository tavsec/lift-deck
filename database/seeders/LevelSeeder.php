<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['level_number' => 1, 'name' => 'Beginner', 'xp_required' => 0],
            ['level_number' => 2, 'name' => 'Bronze', 'xp_required' => 100],
            ['level_number' => 3, 'name' => 'Silver', 'xp_required' => 500],
            ['level_number' => 4, 'name' => 'Gold', 'xp_required' => 1500],
            ['level_number' => 5, 'name' => 'Platinum', 'xp_required' => 5000],
            ['level_number' => 6, 'name' => 'Diamond', 'xp_required' => 10000],
        ];

        foreach ($levels as $level) {
            Level::updateOrCreate(
                ['level_number' => $level['level_number']],
                $level,
            );
        }
    }
}
