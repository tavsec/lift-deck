<?php

namespace App\Http\Requests;

use App\Models\DayPlan;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientDayAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user || ! $user->isCoach()) {
            return false;
        }

        /** @var User|null $client */
        $client = $this->route('client');
        if (! $client instanceof User || $client->coach_id !== $user->id) {
            return false;
        }

        $dayPlanId = $this->input('day_plan_id');
        if (! $dayPlanId) {
            return true;
        }

        return DayPlan::query()
            ->where('id', $dayPlanId)
            ->where('coach_id', $user->id)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'day_plan_id' => ['required', 'integer', 'exists:day_plans,id'],
            'date' => ['required', 'date'],
        ];
    }
}
