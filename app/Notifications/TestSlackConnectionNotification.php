<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class TestSlackConnectionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage);
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->content('This is a test notification of a Slack connection.');

        /**
         * Get the array representation of the notification.
         *
         * @return array<string, mixed>
         */
        public
        function toArray(object $notifiable): array
        {
            return [
                //
            ];
        }
    }
