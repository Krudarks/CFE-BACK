<?php

namespace App\Mail;

use App\Constants\EmailTemplateConstants;
use App\Models\CatEmailTemplateModel;
use App\Trait\EmailTemplateTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserEmail extends Mailable
{
    use Queueable, SerializesModels, EmailTemplateTrait;

    public $welcome;

    public string $token;

    public string $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $email)
    {
        $this->welcome = $this->getEmailTemplate(EmailTemplateConstants::WELCOME_TEMPLATE_CODE);
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->to($this->email)->markdown('emails.NewUser')
            ->subject('Nuevo usuario')
            ->with('message', $this->welcome)
            ->with('token', $this->token)
            ->with('email', $this->email);
    }
}
