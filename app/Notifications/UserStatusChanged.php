<?php

namespace App\Notifications;

use App\Enums\UserStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserStatusChanged extends Notification implements HasLocalePreference
{
    use Queueable;

    public function __construct(
        public UserStatus $oldStatus,
        public UserStatus $newStatus,
        public ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->newStatus;
        $pageUrl = match ($status) {
            UserStatus::Inactive => route('account.inactive'),
            UserStatus::Pending  => route('account.pending'),
            UserStatus::Suspended => route('account.suspended'),
            UserStatus::Banned   => route('account.banned'),
            default              => route('dashboard'),
        };

        $mail = (new MailMessage)
            ->subject(__('emails.status_changed_subject', ['status' => __('account.' . strtolower($status->value))]))
            ->greeting(__('emails.hello'))
            ->line(__('emails.status_changed_line', [
                'status' => __('account.' . strtolower($status->value)),
            ]));

        if ($this->reason) {
            $mail->line(__('emails.status_reason', ['reason' => $this->reason]));
        }

        $mail->action(__('emails.view_details'), $pageUrl)
            ->line(__('emails.status_changed_footer'));

        return $mail;
    }

    public function preferredLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
            'reason'     => $this->reason,
        ];
    }
}
