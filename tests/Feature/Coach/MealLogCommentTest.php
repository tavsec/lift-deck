<?php

use App\Models\MealLog;
use App\Models\MealLogComment;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->mealLog = MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->format('Y-m-d'),
    ]);
});

it('lets a coach post a comment on their clients meal log', function () {
    $response = $this->actingAs($this->coach)
        ->post(route('coach.clients.meal-logs.comments.store', [$this->client, $this->mealLog]), [
            'body' => 'Great macro split — keep it up!',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('meal_log_comments', [
        'meal_log_id' => $this->mealLog->id,
        'author_id' => $this->coach->id,
        'body' => 'Great macro split — keep it up!',
    ]);
});

it('forbids a coach from commenting on another coachs clients meal log', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);
    $otherMealLog = MealLog::factory()->create(['client_id' => $otherClient->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.meal-logs.comments.store', [$otherClient, $otherMealLog]), [
            'body' => 'Hello!',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('meal_log_comments', 0);
});

it('rejects an empty comment body', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.clients.meal-logs.comments.store', [$this->client, $this->mealLog]), [
            'body' => '',
        ])
        ->assertSessionHasErrors('body');

    $this->assertDatabaseCount('meal_log_comments', 0);
});

it('lets a coach delete their own comment', function () {
    $comment = MealLogComment::factory()->create([
        'meal_log_id' => $this->mealLog->id,
        'author_id' => $this->coach->id,
    ]);

    $this->actingAs($this->coach)
        ->delete(route('coach.meal-log-comments.destroy', $comment))
        ->assertRedirect();

    $this->assertDatabaseMissing('meal_log_comments', ['id' => $comment->id]);
});

it('forbids a coach from deleting another coachs comment', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $comment = MealLogComment::factory()->create([
        'meal_log_id' => $this->mealLog->id,
        'author_id' => $otherCoach->id,
    ]);

    $this->actingAs($this->coach)
        ->delete(route('coach.meal-log-comments.destroy', $comment))
        ->assertForbidden();

    $this->assertDatabaseHas('meal_log_comments', ['id' => $comment->id]);
});

it('cascades comment deletion when meal log is deleted', function () {
    $comment = MealLogComment::factory()->create([
        'meal_log_id' => $this->mealLog->id,
        'author_id' => $this->coach->id,
    ]);

    $this->mealLog->delete();

    $this->assertDatabaseMissing('meal_log_comments', ['id' => $comment->id]);
});

it('lets a client mark all their meal log comments as read in one call', function () {
    MealLogComment::factory()->count(3)->create([
        'meal_log_id' => $this->mealLog->id,
        'author_id' => $this->coach->id,
        'read_at' => null,
    ]);

    // Another client's comments should not be touched
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $otherMealLog = MealLog::factory()->create(['client_id' => $otherClient->id]);
    $otherComment = MealLogComment::factory()->create([
        'meal_log_id' => $otherMealLog->id,
        'author_id' => $this->coach->id,
        'read_at' => null,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.meal-log-comments.mark-read'))
        ->assertOk()
        ->assertJson(['updated' => 3]);

    expect(MealLogComment::query()
        ->whereHas('mealLog', fn ($q) => $q->where('client_id', $this->client->id))
        ->whereNotNull('read_at')
        ->count()
    )->toBe(3);

    expect($otherComment->fresh()->read_at)->toBeNull();
});

it('shows the unread badge count on the client nutrition page and clears after mark-all-read', function () {
    MealLogComment::factory()->count(2)->create([
        'meal_log_id' => $this->mealLog->id,
        'author_id' => $this->coach->id,
        'read_at' => null,
    ]);

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('unreadCommentCount', 2);

    $this->actingAs($this->client)
        ->post(route('client.meal-log-comments.mark-read'))
        ->assertOk();

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('unreadCommentCount', 0);
});
