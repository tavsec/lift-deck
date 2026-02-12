<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_workout_id' => ['nullable', 'exists:program_workouts,id'],
            'custom_name' => ['required_without:program_workout_id', 'nullable', 'string', 'max:255'],
            'completed_at' => ['nullable', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.workout_exercise_id' => ['nullable', 'exists:workout_exercises,id'],
            'exercises.*.exercise_id' => ['required', 'exists:exercises,id'],
            'exercises.*.sets' => ['required', 'array', 'min:0'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'exercises.*.sets.*.reps' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'exercises.required' => 'Add at least one exercise.',
            'exercises.min' => 'Add at least one exercise.',
            'exercises.*.sets.*.reps.required' => 'Reps are required for each set.',
            'exercises.*.sets.*.reps.integer' => 'Reps must be a whole number.',
            'exercises.*.sets.*.weight.numeric' => 'Weight must be a number.',
            'custom_name.required_without' => 'A workout name is required for custom workouts.',
        ];
    }
}
