<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientWorkoutLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isCoach();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'custom_name' => ['nullable', 'string', 'max:255'],
            'completed_at' => ['nullable', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'exercises' => ['nullable', 'array'],
            'exercises.*.workout_exercise_id' => ['nullable', 'exists:workout_exercises,id'],
            'exercises.*.exercise_id' => ['required_with:exercises', 'exists:exercises,id'],
            'exercises.*.sets' => ['nullable', 'array'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'exercises.*.sets.*.reps' => ['required_with:exercises.*.sets', 'integer', 'min:0', 'max:999'],
        ];
    }
}
