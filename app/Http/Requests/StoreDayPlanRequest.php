<?php

namespace App\Http\Requests;

use App\Models\Meal;
use Illuminate\Foundation\Http\FormRequest;

class StoreDayPlanRequest extends FormRequest
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

        $mealIds = collect($this->input('items', []))
            ->pluck('meal_id')
            ->filter()
            ->unique()
            ->values();

        if ($mealIds->isEmpty()) {
            return true;
        }

        $owned = Meal::query()
            ->whereIn('id', $mealIds)
            ->where('coach_id', $user->id)
            ->count();

        return $owned === $mealIds->count();
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
            'items.*.meal_id' => ['required', 'integer', 'exists:meals,id'],
            'items.*.meal_type' => ['required', 'string', 'max:50'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
