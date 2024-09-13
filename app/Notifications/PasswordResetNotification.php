<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    public string $email;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     * @return array
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     * @return MailMessage
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage)->subject("Recuperación de contraseña " . config("app.name"))
            ->line('Su contraseña de acceso al Sistema ' . config("app.name"))
            ->action('Cambiar Contraseña', config('app.url_front') . "/auth/reset-password/$this->token/$this->email")
            ->line('Sistema ' . config("app.name"))
            ->line('*La información de este correo, así como la contenida en los documentos que se adjuntan, puede ser objeto de solicitudes de acceso a la información');
    }

    /**
     * Get the array representation of the notification.
     * @return array
     */
    public function toArray(): array
    {
        return [
            //
        ];
    }
}
