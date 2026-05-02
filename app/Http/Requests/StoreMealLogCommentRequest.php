<?php

namespace App\Http\Requests;

use App\Models\MealLog;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreMealLogCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * The route-bound `client` user must belong to the authenticated coach,
     * AND the route-bound `mealLog` must belong to that client.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user || ! $user->isCoach()) {
            return false;
        }

        $client = $this->route('client');
        $mealLog = $this->route('mealLog');

        if (! $client instanceof User || ! $mealLog instanceof MealLog) {
            return false;
        }

        if ($client->coach_id !== $user->id) {
            return false;
        }

        if ($mealLog->client_id !== $client->id) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.required' => 'Please enter a comment.',
        ];
    }
}
