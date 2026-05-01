<?php

use App\Models\Program;
use App\Models\User;

beforeEach(function () {
    // Professional plan so the branding step is included; non-Pro coverage is
    // exercised explicitly in the dedicated tests below.
    $this->coach = User::factory()->coach()->create([
        'onboarding_checklist_dismissed_at' => null,
        'gym_name' => null,
        'selected_plan' => 'professional',
    ]);
});

test('checklist is shown when coach has no clients, programs, or branding', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.onboarding_checklist.heading'));
});

test('checklist is hidden when all steps are complete', function () {
    User::factory()->client()->create(['coach_id' => $this->coach->id]);
    Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->coach->update(['gym_name' => 'Iron Forge Gym']);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.onboarding_checklist.heading'));
});

test('checklist is hidden when coach has dismissed it', function () {
    $this->coach->update(['onboarding_checklist_dismissed_at' => now()]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.onboarding_checklist.heading'));
});

test('subscribe step is always shown as complete', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.onboarding_checklist.steps.subscribe'));
});

test('invite client step shows action link when incomplete', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.onboarding_checklist.actions.invite_client'));
});

test('invite client step shows no action link when a client exists', function () {
    User::factory()->client()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.onboarding_checklist.actions.invite_client'));
});

test('create program step shows action link when incomplete', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.onboarding_checklist.actions.create_program'));
});

test('create program step shows no action link when a program exists', function () {
    Program::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.onboarding_checklist.actions.create_program'));
});

test('branding step shows action link when gym_name is empty', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.onboarding_checklist.actions.setup_branding'));
});

test('branding step shows no action link when gym_name is set', function () {
    $this->coach->update(['gym_name' => 'Power Gym']);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.onboarding_checklist.actions.setup_branding'));
});

test('dismiss sets onboarding_checklist_dismissed_at and redirects to dashboard', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.onboarding-checklist.dismiss'))
        ->assertRedirect(route('coach.dashboard'));

    $this->coach->refresh();
    expect($this->coach->onboarding_checklist_dismissed_at)->not->toBeNull();
});

test('dismiss route requires authentication', function () {
    $this->post(route('coach.onboarding-checklist.dismiss'))
        ->assertRedirect(route('login'));
});

test('client cannot access coach dismiss route', function () {
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($client)
        ->post(route('coach.onboarding-checklist.dismiss'))
        ->assertRedirect(route('client.dashboard'));
});

test('branding step is hidden for coaches on Basic plan', function () {
    $basicCoach = User::factory()->coach()->create([
        'onboarding_checklist_dismissed_at' => null,
        'gym_name' => null,
        'selected_plan' => 'basic',
    ]);

    $this->actingAs($basicCoach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.onboarding_checklist.heading'))
        ->assertDontSee(__('coach.onboarding_checklist.steps.setup_branding'))
        ->assertDontSee(__('coach.onboarding_checklist.actions.setup_branding'));
});

test('branding step is hidden for coaches on Advanced plan', function () {
    $advancedCoach = User::factory()->coach()->create([
        'onboarding_checklist_dismissed_at' => null,
        'gym_name' => null,
        'selected_plan' => 'advanced',
    ]);

    $this->actingAs($advancedCoach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.onboarding_checklist.heading'))
        ->assertDontSee(__('coach.onboarding_checklist.steps.setup_branding'));
});

test('checklist hides for Basic coaches once non-branding steps are complete', function () {
    $basicCoach = User::factory()->coach()->create([
        'onboarding_checklist_dismissed_at' => null,
        'gym_name' => null,
        'selected_plan' => 'basic',
    ]);

    User::factory()->client()->create(['coach_id' => $basicCoach->id]);
    \App\Models\Program::factory()->create(['coach_id' => $basicCoach->id]);

    $this->actingAs($basicCoach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.onboarding_checklist.heading'));
});
