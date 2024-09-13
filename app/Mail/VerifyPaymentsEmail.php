<?php

namespace App\Mail;

use App\Helpers\HelperImage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyPaymentsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $message;

    public string $logo;

    public string $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $message)
    {
        $this->logo = HelperImage::getLogo();
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {

        return $this->to($this->email)->markdown('emails.TemplateBase')
            ->subject('Notificacion de Inscripcion')
            ->with('message', $this->message)
            ->with('logo', $this->logo);
    }
}
