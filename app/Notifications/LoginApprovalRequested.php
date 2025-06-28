<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginApprovalRequested extends Notification
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail']; // You can also add 'database' if needed
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Login Approval Needed')
            ->greeting('Hello Admin,')
            ->line("User {$this->user->email} attempted to log in.")
            ->action('Approve Login', url("/admin/approve-login/{$this->user->id}"))
            ->line('Click the button above to allow their login.');
    }
}
