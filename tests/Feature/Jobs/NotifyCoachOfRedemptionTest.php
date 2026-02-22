<?php

use App\Jobs\NotifyCoachOfRedemption;
use App\Mail\RewardRedeemedMail;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('sends reward redeemed email to coach', function () {
    Mail::fake();

    $coach = User::factory()->coach()->create();
    $client = User::factory()->client()->create(['coach_id' => $coach->id]);
    $reward = Reward::factory()->create(['coach_id' => $coach->id]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
    ]);

    (new NotifyCoachOfRedemption($redemption->id))->handle();

    Mail::assertSent(RewardRedeemedMail::class, function ($mail) use ($coach) {
        return $mail->hasTo($coach->email);
    });
});

it('does not send email when redemption does not exist', function () {
    Mail::fake();

    (new NotifyCoachOfRedemption(999))->handle();

    Mail::assertNothingSent();
});

it('does not send email when client has no coach', function () {
    Mail::fake();

    $client = User::factory()->client()->create(['coach_id' => null]);
    $reward = Reward::factory()->create();
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
    ]);

    (new NotifyCoachOfRedemption($redemption->id))->handle();

    Mail::assertNothingSent();
});
