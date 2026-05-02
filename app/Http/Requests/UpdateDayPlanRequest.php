<?php

namespace App\Http\Requests;

use App\Models\DayPlan;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDayPlanRequest extends FormRequest
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

        /** @var DayPlan|null $dayPlan */
        $dayPlan = $this->route('dayPlan');
        if (! $dayPlan instanceof DayPlan || $dayPlan->coach_id !== $user->id || $dayPlan->client_id !== $client->id) {
            return false;
        }

        $libraryMealIds = collect($this->input('items', []))
            ->filter(fn ($item): bool => is_array($item) && ($item['source'] ?? null) === 'library')
            ->pluck('meal_id')
            ->filter()
            ->unique()
            ->values();

        if ($libraryMealIds->isEmpty()) {
            return true;
        }

        $owned = Meal::query()
            ->whereIn('id', $libraryMealIds)
            ->where('coach_id', $user->id)
            ->count();

        return $owned === $libraryMealIds->count();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.source' => ['required', Rule::in(['library', 'custom', 'off', 'macros'])],
            'items.*.meal_type' => ['required', 'string', 'max:50'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.calories' => ['required', 'integer', 'min:0'],
            'items.*.protein' => ['required', 'numeric', 'min:0'],
            'items.*.carbs' => ['required', 'numeric', 'min:0'],
            'items.*.fat' => ['required', 'numeric', 'min:0'],
            'items.*.meal_id' => ['nullable', 'required_if:items.*.source,library', 'integer', 'exists:meals,id'],
            'items.*.off_code' => ['nullable', 'required_if:items.*.source,off', 'string', 'max:64'],
            'items.*.portion_grams' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
