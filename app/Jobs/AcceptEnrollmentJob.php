<?php

namespace App\Jobs;

use App\Mail\AcceptEnrollmentEmail;
use App\Trait\EmailTemplateTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AcceptEnrollmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EmailTemplateTrait;

    protected $group;

    protected string $status;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $status, $group)
    {
        $this->group = $group;
        $this->status = $status;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {

        $user = $this->user;
        $group = $this->group;
        $statusName =  $this->status  ===  'accept' ? 'Aceptada' : 'Denegada';
        $templateStr = $this->generateMessage($user['name'], $group['name'], $this->status, $statusName);

        Mail::send(new AcceptEnrollmentEmail($user['email'], $templateStr));
    }

    private function generateMessage($username, $groupName, $status, $statusName): string
    {
        $message = "<p>Estimado(a) <strong>$username</strong>,</p>\n";
        $message .= "<p>Le informamos que su solicitud para inscribirse en el grupo <strong>$groupName</strong> ha sido <strong>$statusName</strong>.</p>\n";

        if ($status === 'denied') {
            $message .= "<p>Lamentamos informarle que su solicitud ha sido rechazada. Para más detalles o posibles acciones, por favor contacte con el administrador.</p>\n";
        } else {
            $message .= "<p>Nos complace informarle que su solicitud ha sido aceptada. Puede proceder a realizar los pagos correspondientes para acceder al grupo.</p>\n";
        }

        $message .= "<p style='color: #5a6268; font-size: .8em;'>*La información de este correo, así como la contenida en los documentos adjuntos, puede ser objeto de solicitudes de acceso a la información.</p>\n";

        return $message;
    }
}
