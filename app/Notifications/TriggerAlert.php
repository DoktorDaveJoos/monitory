<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ActionsBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ContextBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;

class TriggerAlert extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        readonly string $monitorName,
        readonly int $monitorId,
        readonly array $reasons,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($notifiable->settings['notifications']['email'] ?? false) {
            return ['mail'];
        }

        return array_keys(array_filter($notifiable->settings['notifications'] ?? []));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->line('A trigger has been activated for monitor: '.$this->monitorName);

        foreach ($this->reasons as $index => $reason) {
            $message->line('Reason '.($index + 1).': '.$reason);
        }

        $message->action('View Monitor', route('monitor.show', ['monitor' => $this->monitorId]));

        return $message->line('Thank you for using our application!');
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->headerBlock('Trigger Alert')
            ->contextBlock(function (ContextBlock $block) {
                $block->text('Monitor: '.$this->monitorName);
            })
            ->sectionBlock(function (SectionBlock $block) {
                foreach ($this->reasons as $reason) {
                    $block->text($reason);
                }
            })
            ->dividerBlock()
            ->actionsBlock(function (ActionsBlock $block) {
                $block->button('View Monitor')
                    ->url(route('monitor.show', ['monitor' => $this->monitorId]));
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
