<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // Chest
            ['name' => 'Barbell Bench Press', 'muscle_group' => 'chest', 'description' => 'Lie on a flat bench, grip the bar slightly wider than shoulder-width. Lower the bar to your chest, then press up.', 'video_url' => 'https://www.youtube.com/watch?v=rT7DgCr-3pg'],
            ['name' => 'Incline Dumbbell Press', 'muscle_group' => 'chest', 'description' => 'Set bench to 30-45 degree incline. Press dumbbells up from chest level.', 'video_url' => 'https://www.youtube.com/watch?v=8iPEnn-ltC8'],
            ['name' => 'Dumbbell Flyes', 'muscle_group' => 'chest', 'description' => 'Lie flat with dumbbells above chest. Lower arms out to sides with slight bend in elbows, then bring back together.', 'video_url' => 'https://www.youtube.com/watch?v=eozdVDA78K0'],
            ['name' => 'Push-Ups', 'muscle_group' => 'chest', 'description' => 'Standard push-up from plank position. Lower chest to floor and push back up.', 'video_url' => 'https://www.youtube.com/watch?v=IODxDxX7oi4'],
            ['name' => 'Cable Crossover', 'muscle_group' => 'chest', 'description' => 'Stand between cable stations with handles at high position. Bring handles together in front of chest.', 'video_url' => 'https://www.youtube.com/watch?v=taI4XduLpTk'],

            // Back
            ['name' => 'Barbell Deadlift', 'muscle_group' => 'back', 'description' => 'Stand with feet hip-width apart, grip bar outside legs. Keep back flat, drive through heels to stand.', 'video_url' => 'https://www.youtube.com/watch?v=op9kVnSso6Q'],
            ['name' => 'Pull-Ups', 'muscle_group' => 'back', 'description' => 'Hang from bar with overhand grip. Pull body up until chin clears bar, lower with control.', 'video_url' => 'https://www.youtube.com/watch?v=eGo4IYlbE5g'],
            ['name' => 'Barbell Bent-Over Row', 'muscle_group' => 'back', 'description' => 'Hinge at hips with flat back, pull barbell to lower chest, squeeze shoulder blades together.', 'video_url' => 'https://www.youtube.com/watch?v=FWJR5Ve8bnQ'],
            ['name' => 'Lat Pulldown', 'muscle_group' => 'back', 'description' => 'Sit at lat pulldown machine, grip bar wide. Pull bar to upper chest, squeeze lats at bottom.', 'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc'],
            ['name' => 'Seated Cable Row', 'muscle_group' => 'back', 'description' => 'Sit at cable row, pull handle to midsection while keeping back straight. Squeeze shoulder blades together.', 'video_url' => 'https://www.youtube.com/watch?v=GZbfZ033f74'],
            ['name' => 'Single-Arm Dumbbell Row', 'muscle_group' => 'back', 'description' => 'Place one knee and hand on bench, row dumbbell to hip with other arm.', 'video_url' => 'https://www.youtube.com/watch?v=pYcpY20QaE8'],

            // Shoulders
            ['name' => 'Overhead Press', 'muscle_group' => 'shoulders', 'description' => 'Stand with barbell at shoulders, press overhead until arms are fully extended.', 'video_url' => 'https://www.youtube.com/watch?v=2yjwXTZQDDI'],
            ['name' => 'Dumbbell Lateral Raise', 'muscle_group' => 'shoulders', 'description' => 'Stand with dumbbells at sides. Raise arms out to sides until parallel with floor.', 'video_url' => 'https://www.youtube.com/watch?v=3VcKaXpzqRo'],
            ['name' => 'Face Pull', 'muscle_group' => 'shoulders', 'description' => 'Use rope attachment on cable. Pull toward face, separating rope ends at ears.', 'video_url' => 'https://www.youtube.com/watch?v=rep-qVOkqgk'],
            ['name' => 'Arnold Press', 'muscle_group' => 'shoulders', 'description' => 'Start with dumbbells at chest, palms facing you. Rotate palms out as you press overhead.', 'video_url' => 'https://www.youtube.com/watch?v=6Z15_WdXmVw'],
            ['name' => 'Rear Delt Fly', 'muscle_group' => 'shoulders', 'description' => 'Bend forward at hips, raise dumbbells out to sides, squeezing rear delts.', 'video_url' => 'https://www.youtube.com/watch?v=EA7u4Q_8HQ0'],

            // Biceps
            ['name' => 'Barbell Curl', 'muscle_group' => 'biceps', 'description' => 'Stand with barbell at thighs, curl up to shoulders keeping elbows stationary.', 'video_url' => 'https://www.youtube.com/watch?v=kwG2ipFRgfo'],
            ['name' => 'Dumbbell Hammer Curl', 'muscle_group' => 'biceps', 'description' => 'Curl dumbbells with palms facing each other throughout the movement.', 'video_url' => 'https://www.youtube.com/watch?v=zC3nLlEvin4'],
            ['name' => 'Incline Dumbbell Curl', 'muscle_group' => 'biceps', 'description' => 'Sit on incline bench, let arms hang straight down, curl dumbbells up.', 'video_url' => 'https://www.youtube.com/watch?v=soxrZlIl35U'],
            ['name' => 'Preacher Curl', 'muscle_group' => 'biceps', 'description' => 'Use preacher bench to isolate biceps. Curl weight up with controlled movement.', 'video_url' => 'https://www.youtube.com/watch?v=fIWP-FRFNU0'],
            ['name' => 'Cable Curl', 'muscle_group' => 'biceps', 'description' => 'Stand facing cable machine, curl handle up from low pulley position.', 'video_url' => 'https://www.youtube.com/watch?v=NFzTWp2qpiE'],

            // Triceps
            ['name' => 'Tricep Pushdown', 'muscle_group' => 'triceps', 'description' => 'Stand at cable machine, push bar down until arms are fully extended.', 'video_url' => 'https://www.youtube.com/watch?v=2-LAMcpzODU'],
            ['name' => 'Skull Crusher', 'muscle_group' => 'triceps', 'description' => 'Lie on bench, lower barbell toward forehead by bending elbows, then extend.', 'video_url' => 'https://www.youtube.com/watch?v=d_KZxkY_0cM'],
            ['name' => 'Close-Grip Bench Press', 'muscle_group' => 'triceps', 'description' => 'Bench press with hands closer than shoulder-width to emphasize triceps.', 'video_url' => 'https://www.youtube.com/watch?v=nEF0bv2FW94'],
            ['name' => 'Overhead Tricep Extension', 'muscle_group' => 'triceps', 'description' => 'Hold dumbbell overhead with both hands, lower behind head, then extend.', 'video_url' => 'https://www.youtube.com/watch?v=YbX7Wd8jQ-Q'],
            ['name' => 'Dips', 'muscle_group' => 'triceps', 'description' => 'Support body on parallel bars, lower by bending elbows, push back up.', 'video_url' => 'https://www.youtube.com/watch?v=2z8JmcrW-As'],

            // Quadriceps
            ['name' => 'Barbell Back Squat', 'muscle_group' => 'quadriceps', 'description' => 'Bar on upper back, squat down until thighs are parallel, drive up through heels.', 'video_url' => 'https://www.youtube.com/watch?v=ultWZbUMPL8'],
            ['name' => 'Front Squat', 'muscle_group' => 'quadriceps', 'description' => 'Bar rests on front deltoids, squat down keeping torso upright.', 'video_url' => 'https://www.youtube.com/watch?v=m4ytaCJZpl0'],
            ['name' => 'Leg Press', 'muscle_group' => 'quadriceps', 'description' => 'Sit in leg press machine, lower weight by bending knees, press back up.', 'video_url' => 'https://www.youtube.com/watch?v=IZxyjW7MPJQ'],
            ['name' => 'Leg Extension', 'muscle_group' => 'quadriceps', 'description' => 'Sit in leg extension machine, extend legs fully, squeeze quads at top.', 'video_url' => 'https://www.youtube.com/watch?v=YyvSfVjQeL0'],
            ['name' => 'Walking Lunges', 'muscle_group' => 'quadriceps', 'description' => 'Step forward into lunge, alternate legs while walking forward.', 'video_url' => 'https://www.youtube.com/watch?v=L8fvypPrzzs'],
            ['name' => 'Bulgarian Split Squat', 'muscle_group' => 'quadriceps', 'description' => 'Rear foot elevated on bench, squat down on front leg.', 'video_url' => 'https://www.youtube.com/watch?v=2C-uNgKwPLE'],

            // Hamstrings
            ['name' => 'Romanian Deadlift', 'muscle_group' => 'hamstrings', 'description' => 'Hinge at hips with slight knee bend, lower barbell along legs, feel hamstring stretch.', 'video_url' => 'https://www.youtube.com/watch?v=7j-2w4-P14I'],
            ['name' => 'Lying Leg Curl', 'muscle_group' => 'hamstrings', 'description' => 'Lie face down on leg curl machine, curl weight toward glutes.', 'video_url' => 'https://www.youtube.com/watch?v=1Tq3QdYUuHs'],
            ['name' => 'Seated Leg Curl', 'muscle_group' => 'hamstrings', 'description' => 'Sit in leg curl machine, curl weight under seat by bending knees.', 'video_url' => 'https://www.youtube.com/watch?v=Orxowest56U'],
            ['name' => 'Good Mornings', 'muscle_group' => 'hamstrings', 'description' => 'Bar on upper back, hinge at hips keeping back flat, return to standing.', 'video_url' => 'https://www.youtube.com/watch?v=YA-h3n9L4YU'],

            // Glutes
            ['name' => 'Hip Thrust', 'muscle_group' => 'glutes', 'description' => 'Upper back on bench, barbell on hips, drive hips up squeezing glutes at top.', 'video_url' => 'https://www.youtube.com/watch?v=SEdqd1n0cvg'],
            ['name' => 'Glute Bridge', 'muscle_group' => 'glutes', 'description' => 'Lie on back, feet flat on floor, raise hips and squeeze glutes.', 'video_url' => 'https://www.youtube.com/watch?v=wPM8icPu6H8'],
            ['name' => 'Cable Kickback', 'muscle_group' => 'glutes', 'description' => 'Attach ankle cuff to low cable, kick leg back while squeezing glute.', 'video_url' => 'https://www.youtube.com/watch?v=mJFhR7q13jM'],
            ['name' => 'Sumo Deadlift', 'muscle_group' => 'glutes', 'description' => 'Wide stance deadlift with toes pointed out, emphasizes glutes and inner thighs.', 'video_url' => 'https://www.youtube.com/watch?v=widx6FDhT7U'],

            // Calves
            ['name' => 'Standing Calf Raise', 'muscle_group' => 'calves', 'description' => 'Stand on calf raise machine, raise up onto toes, lower with control.', 'video_url' => 'https://www.youtube.com/watch?v=gwLzBJYoWlI'],
            ['name' => 'Seated Calf Raise', 'muscle_group' => 'calves', 'description' => 'Sit at calf raise machine, raise heels as high as possible.', 'video_url' => 'https://www.youtube.com/watch?v=JbyjNymZOt0'],

            // Core
            ['name' => 'Plank', 'muscle_group' => 'core', 'description' => 'Hold push-up position on forearms, keep body straight, engage core.', 'video_url' => 'https://www.youtube.com/watch?v=pSHjTRCQxIw'],
            ['name' => 'Hanging Leg Raise', 'muscle_group' => 'core', 'description' => 'Hang from bar, raise legs until parallel to floor or higher.', 'video_url' => 'https://www.youtube.com/watch?v=Pr1ieGZ5atk'],
            ['name' => 'Cable Crunch', 'muscle_group' => 'core', 'description' => 'Kneel facing cable machine, crunch down bringing elbows toward knees.', 'video_url' => 'https://www.youtube.com/watch?v=ToJeyhydUxU'],
            ['name' => 'Russian Twist', 'muscle_group' => 'core', 'description' => 'Sit with knees bent, lean back slightly, rotate torso side to side.', 'video_url' => 'https://www.youtube.com/watch?v=wkD8rjkodUI'],
            ['name' => 'Ab Wheel Rollout', 'muscle_group' => 'core', 'description' => 'Kneel with ab wheel, roll forward keeping core tight, roll back.', 'video_url' => 'https://www.youtube.com/watch?v=rqiTPdK1c_I'],
            ['name' => 'Dead Bug', 'muscle_group' => 'core', 'description' => 'Lie on back, extend opposite arm and leg while keeping lower back pressed to floor.', 'video_url' => 'https://www.youtube.com/watch?v=I5xbsA71v1A'],

            // Forearms
            ['name' => 'Wrist Curl', 'muscle_group' => 'forearms', 'description' => 'Sit with forearms on thighs, curl barbell up using only wrists.', 'video_url' => 'https://www.youtube.com/watch?v=7mof43sP1Ko'],
            ['name' => 'Reverse Wrist Curl', 'muscle_group' => 'forearms', 'description' => 'Same as wrist curl but with palms facing down.', 'video_url' => 'https://www.youtube.com/watch?v=L3hGYaw7OqY'],
            ['name' => 'Farmers Walk', 'muscle_group' => 'forearms', 'description' => 'Hold heavy dumbbells at sides, walk for distance or time.', 'video_url' => 'https://www.youtube.com/watch?v=Fkzk_RqlYig'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}
