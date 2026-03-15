<x-mail::message>
# Reward Redeemed

**{{ $redemption->user->name }}** has redeemed the reward **"{{ $redemption->reward->name }}"** for **{{ $redemption->points_spent }} points**.

<x-mail::button :url="url('/coach/redemptions')">
View Redemptions
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
