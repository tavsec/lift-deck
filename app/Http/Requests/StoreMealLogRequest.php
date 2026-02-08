<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isClient();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'meal_id' => ['nullable', 'exists:meals,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'meal_type' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'calories' => ['required', 'integer', 'min:0'],
            'protein' => ['required', 'numeric', 'min:0'],
            'carbs' => ['required', 'numeric', 'min:0'],
            'fat' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
