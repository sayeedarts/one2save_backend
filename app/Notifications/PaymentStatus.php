<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatus extends Notification
{
    use Queueable;

    public $details;
    public $from_address;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
        $this->from_address = [
            'email' => env('MAIL_FROM_ADDRESS', 'tanmayapatra09@gmail.com'),
            'name' => env('MAIL_FROM_NAME', 'Portal Administrator')
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        echo $this->details['to']; exit;
        $mail = (new MailMessage)
            ->greeting($this->details['greeting'])
            ->subject($this->details['subject'])
            ->to($this->details['to'])
            ->from($this->from_address['email'], $this->from_address['name']);

        // buildup body lines
        foreach ($this->details['body'] as $key => $body) {
            $mail->line($body);
        }
        if (!empty($this->details['action_text']) && !empty($this->details['action_url'])) {
            $mail->action($this->details['action_text'], $this->details['action_url'])
                ->line('Thank you for using our application!');
        }

        return $mail;
        // return (new MailMessage)
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');
    }

    public function toDatabase()
    {
        return $this->details['more'];
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
