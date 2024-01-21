<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $details;
    public $from;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
        $this->from = env('MAIL_FROM_ADDRESS', 'tanmayapatra09@gmail.com');
        $this->from_name = env('MAIL_FROM_NAME', 'HMH Administration Team');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        echo "Hi!";
        dd($this->details);
        $mail = (new MailMessage)
            ->greeting($this->details['greeting'])
            ->subject($this->details['subject'])
            ->from($this->from, !empty($this->from_name) ?? "HMH");
            
        // buildup body lines
        foreach ($this->details['body'] as $key => $body) {
            $mail->line($body);
        }
        dd($mail);
        $mail->action($this->details['action_text'], $this->details['action_url'])
            ->line('Thank you for using our application!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
