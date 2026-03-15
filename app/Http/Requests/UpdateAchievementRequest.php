<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAchievementRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:automatic,manual'],
            'condition_type' => ['nullable', 'string', 'required_if:type,automatic'],
            'condition_value' => ['nullable', 'integer', 'min:1', 'required_if:type,automatic'],
            'xp_reward' => ['nullable', 'integer', 'min:0'],
            'points_reward' => ['nullable', 'integer', 'min:0'],
            'icon' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
