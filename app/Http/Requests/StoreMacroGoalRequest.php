<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMacroGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isCoach();
    }

    public function rules(): array
    {
        return [
            'calories' => ['required', 'integer', 'min:0'],
            'protein' => ['required', 'numeric', 'min:0'],
            'carbs' => ['required', 'numeric', 'min:0'],
            'fat' => ['required', 'numeric', 'min:0'],
            'effective_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
