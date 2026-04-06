<x-mail::message>
# {{ __('emails.reward_redeemed.heading') }}

{{ __('emails.reward_redeemed.body', ['name' => $redemption->user->name, 'reward' => $redemption->reward->name, 'points' => $redemption->points_spent]) }}

<x-mail::button :url="url('/coach/redemptions')">
{{ __('emails.reward_redeemed.view_redemptions') }}
</x-mail::button>

{{ __('emails.reward_redeemed.thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>
