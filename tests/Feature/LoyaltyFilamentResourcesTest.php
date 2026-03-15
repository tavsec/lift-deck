<?php

use App\Filament\Resources\Achievements\Pages\CreateAchievement;
use App\Filament\Resources\Achievements\Pages\EditAchievement;
use App\Filament\Resources\Achievements\Pages\ListAchievements;
use App\Filament\Resources\Levels\Pages\CreateLevel;
use App\Filament\Resources\Levels\Pages\EditLevel;
use App\Filament\Resources\Levels\Pages\ListLevels;
use App\Filament\Resources\Rewards\Pages\CreateReward;
use App\Filament\Resources\Rewards\Pages\EditReward;
use App\Filament\Resources\Rewards\Pages\ListRewards;
use App\Filament\Resources\XpEventTypes\Pages\CreateXpEventType;
use App\Filament\Resources\XpEventTypes\Pages\EditXpEventType;
use App\Filament\Resources\XpEventTypes\Pages\ListXpEventTypes;
use App\Models\Achievement;
use App\Models\Level;
use App\Models\Reward;
use App\Models\User;
use App\Models\XpEventType;
use Livewire\Livewire;

beforeEach(function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);
});

describe('XpEventType Resource', function () {
    it('can render the list page', function () {
        $xpEventTypes = XpEventType::factory()->count(3)->create();

        Livewire::test(ListXpEventTypes::class)
            ->assertOk()
            ->assertCanSeeTableRecords($xpEventTypes);
    });

    it('can render the create page', function () {
        Livewire::test(CreateXpEventType::class)
            ->assertOk();
    });

    it('can create an xp event type', function () {
        $data = XpEventType::factory()->make();

        Livewire::test(CreateXpEventType::class)
            ->fillForm([
                'key' => $data->key,
                'name' => $data->name,
                'description' => $data->description,
                'xp_amount' => $data->xp_amount,
                'points_amount' => $data->points_amount,
                'cooldown_hours' => $data->cooldown_hours,
                'is_active' => $data->is_active,
            ])
            ->call('create')
            ->assertNotified();

        $this->assertDatabaseHas(XpEventType::class, [
            'key' => $data->key,
            'name' => $data->name,
        ]);
    });

    it('can render the edit page', function () {
        $xpEventType = XpEventType::factory()->create();

        Livewire::test(EditXpEventType::class, ['record' => $xpEventType->getRouteKey()])
            ->assertOk();
    });
});

describe('Level Resource', function () {
    it('can render the list page', function () {
        $levels = Level::factory()->count(3)->create();

        Livewire::test(ListLevels::class)
            ->assertOk()
            ->assertCanSeeTableRecords($levels);
    });

    it('can render the create page', function () {
        Livewire::test(CreateLevel::class)
            ->assertOk();
    });

    it('can create a level', function () {
        $data = Level::factory()->make();

        Livewire::test(CreateLevel::class)
            ->fillForm([
                'name' => $data->name,
                'level_number' => $data->level_number,
                'xp_required' => $data->xp_required,
            ])
            ->call('create')
            ->assertNotified();

        $this->assertDatabaseHas(Level::class, [
            'name' => $data->name,
            'level_number' => $data->level_number,
        ]);
    });

    it('can render the edit page', function () {
        $level = Level::factory()->create();

        Livewire::test(EditLevel::class, ['record' => $level->getRouteKey()])
            ->assertOk();
    });
});

describe('Achievement Resource', function () {
    it('can render the list page with global achievements only', function () {
        $globalAchievements = Achievement::factory()->count(3)->create(['coach_id' => null]);
        $coachAchievements = Achievement::factory()->count(2)->create([
            'coach_id' => User::factory()->create(['role' => 'coach'])->id,
        ]);

        Livewire::test(ListAchievements::class)
            ->assertOk()
            ->assertCanSeeTableRecords($globalAchievements)
            ->assertCanNotSeeTableRecords($coachAchievements);
    });

    it('can render the create page', function () {
        Livewire::test(CreateAchievement::class)
            ->assertOk();
    });

    it('can create an achievement', function () {
        $data = Achievement::factory()->make(['coach_id' => null]);

        Livewire::test(CreateAchievement::class)
            ->fillForm([
                'name' => $data->name,
                'description' => $data->description,
                'type' => $data->type,
                'xp_reward' => $data->xp_reward,
                'points_reward' => $data->points_reward,
                'is_active' => $data->is_active,
            ])
            ->call('create')
            ->assertNotified();

        $this->assertDatabaseHas(Achievement::class, [
            'name' => $data->name,
            'coach_id' => null,
        ]);
    });

    it('can render the edit page', function () {
        $achievement = Achievement::factory()->create(['coach_id' => null]);

        Livewire::test(EditAchievement::class, ['record' => $achievement->getRouteKey()])
            ->assertOk();
    });
});

describe('Reward Resource', function () {
    it('can render the list page with global rewards only', function () {
        $globalRewards = Reward::factory()->count(3)->create(['coach_id' => null]);
        $coachRewards = Reward::factory()->count(2)->create([
            'coach_id' => User::factory()->create(['role' => 'coach'])->id,
        ]);

        Livewire::test(ListRewards::class)
            ->assertOk()
            ->assertCanSeeTableRecords($globalRewards)
            ->assertCanNotSeeTableRecords($coachRewards);
    });

    it('can render the create page', function () {
        Livewire::test(CreateReward::class)
            ->assertOk();
    });

    it('can create a reward', function () {
        $data = Reward::factory()->make(['coach_id' => null]);

        Livewire::test(CreateReward::class)
            ->fillForm([
                'name' => $data->name,
                'description' => $data->description,
                'points_cost' => $data->points_cost,
                'stock' => $data->stock,
                'is_active' => $data->is_active,
            ])
            ->call('create')
            ->assertNotified();

        $this->assertDatabaseHas(Reward::class, [
            'name' => $data->name,
            'coach_id' => null,
        ]);
    });

    it('can render the edit page', function () {
        $reward = Reward::factory()->create(['coach_id' => null]);

        Livewire::test(EditReward::class, ['record' => $reward->getRouteKey()])
            ->assertOk();
    });
});
