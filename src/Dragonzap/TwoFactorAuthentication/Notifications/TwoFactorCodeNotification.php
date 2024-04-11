<?php

namespace Dragonzap\TwoFactorAuthentication\Notifications;

use Dragonzap\TwoFactorAuthentication\TwoFactorCode;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TwoFactorCodeNotification extends Notification
{

    protected $twoFactorCode;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TwoFactorCode $code)
    {
        $this->twoFactorCode = $code;
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
        return (new MailMessage)
            ->subject('Confirm your two factor authentication code')
            ->greeting('You must confirm your two factor authentication code')
            ->line('Your two factor authentication code is: ' . $this->twoFactorCode->getCode());
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
