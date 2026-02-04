<?php

namespace App\Notifications;

use App\Models\WorkoutLog;
use App\Models\WorkoutLogComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkoutLogCommented extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public WorkoutLogComment $comment,
        public WorkoutLog $workoutLog,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $commenterName = $this->comment->user->name;
        $workoutName = $this->workoutLog->displayName();

        $url = $notifiable->isCoach()
            ? route('coach.clients.workout-log', [$this->workoutLog->client_id, $this->workoutLog])
            : route('client.history.show', $this->workoutLog);

        return (new MailMessage)
            ->subject("New comment on {$workoutName}")
            ->line("{$commenterName} commented on the workout log for \"{$workoutName}\":")
            ->line("\"{$this->comment->body}\"")
            ->action('View Workout Log', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'workout_log_id' => $this->workoutLog->id,
            'comment_id' => $this->comment->id,
            'commenter_id' => $this->comment->user_id,
            'commenter_name' => $this->comment->user->name,
            'workout_name' => $this->workoutLog->displayName(),
            'body_preview' => str()->limit($this->comment->body, 80),
        ];
    }
}
