<?php

use App\Models\OnboardingField;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $coaches = User::where('role', 'coach')->get();

        foreach ($coaches as $coach) {
            $this->seedDefaultFields($coach->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only remove fields that exactly match the defaults
        // This is a best-effort rollback
    }

    private function seedDefaultFields(int $coachId): void
    {
        $fields = [
            [
                'label' => 'What is your primary goal?',
                'type' => 'select',
                'options' => ['Fat Loss', 'Build Strength', 'General Fitness'],
                'is_required' => true,
                'order' => 1,
            ],
            [
                'label' => 'What is your experience level?',
                'type' => 'select',
                'options' => ['Beginner', 'Intermediate', 'Advanced'],
                'is_required' => true,
                'order' => 2,
            ],
            [
                'label' => 'Any injuries or limitations?',
                'type' => 'textarea',
                'options' => null,
                'is_required' => false,
                'order' => 3,
            ],
            [
                'label' => 'What equipment do you have access to?',
                'type' => 'textarea',
                'options' => null,
                'is_required' => false,
                'order' => 4,
            ],
        ];

        foreach ($fields as $field) {
            OnboardingField::create(array_merge($field, ['coach_id' => $coachId]));
        }
    }
};
