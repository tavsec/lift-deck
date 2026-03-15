<?php

use App\Models\Level;
use Database\Seeders\LevelSeeder;

it('seeds all levels', function () {
    $this->seed(LevelSeeder::class);

    expect(Level::count())->toBe(6);
});

it('seeds levels with correct names and xp requirements', function () {
    $this->seed(LevelSeeder::class);

    $expectedLevels = [
        1 => ['name' => 'Beginner', 'xp_required' => 0],
        2 => ['name' => 'Bronze', 'xp_required' => 100],
        3 => ['name' => 'Silver', 'xp_required' => 500],
        4 => ['name' => 'Gold', 'xp_required' => 1500],
        5 => ['name' => 'Platinum', 'xp_required' => 5000],
        6 => ['name' => 'Diamond', 'xp_required' => 10000],
    ];

    foreach ($expectedLevels as $levelNumber => $expected) {
        $level = Level::where('level_number', $levelNumber)->first();
        expect($level->name)->toBe($expected['name'])
            ->and($level->xp_required)->toBe($expected['xp_required']);
    }
});

it('is idempotent when run multiple times', function () {
    $this->seed(LevelSeeder::class);
    $this->seed(LevelSeeder::class);

    expect(Level::count())->toBe(6);
});

it('seeds levels in ascending xp order', function () {
    $this->seed(LevelSeeder::class);

    $levels = Level::orderBy('level_number')->pluck('xp_required')->toArray();

    expect($levels)->toBe([0, 100, 500, 1500, 5000, 10000]);
});
