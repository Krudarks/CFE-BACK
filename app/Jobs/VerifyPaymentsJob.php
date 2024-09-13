<?php

namespace App\Jobs;

use App\Mail\VerifyPaymentsEmail;
use App\Models\DocumentsInscriptionModel;
use App\Models\GroupsModel;
use App\Models\GroupsUsersModel;
use App\Models\PaymentsModel;
use App\Models\UserModel;
use App\Trait\EmailTemplateTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class VerifyPaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EmailTemplateTrait;

    protected $payment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $payment = $this->payment;
        // 7 Pagos esperados
        $expectedPayments = ['Inscripción', 'Pago mes 1', 'Pago mes 2', 'Pago mes 3', 'Pago mes 4', 'Pago mes 5', 'Pago mes 6'];

        // 5 Documentos esperados
        $expectedDocuments = ['Cedula', 'Curp', 'Acta de Nacimiento', 'INE', 'Carta Pasante'];

        $allPaymentsVerify = PaymentsModel::where('group_id', $payment['group_id'])
            ->where('status', 'approve') // Verificar que el estatus sea "approve"
            ->where('student_id', $payment['student_id'])->get();
        $documents = DocumentsInscriptionModel::where('user_id', $payment['student_id'])->get();


        // Verificación de pagos
        $missingPayments = [];
        foreach ($expectedPayments as $paymentName) {
            if (!$allPaymentsVerify->contains('type_payment', $paymentName)) {
                $missingPayments[] = $paymentName;
            }
        }

        // Verificación de documentos
        $missingDocuments = [];
        foreach ($expectedDocuments as $documentName) {
            if (!$documents->contains('type', $documentName)) {
                $missingDocuments[] = $documentName;
            }
        }

        // Resultado de la validación
        if (empty($missingPayments) && empty($missingDocuments)) {
            $this->sendMessageApproveInGroup($payment['student_id'], $payment['group_id']);
        }
    }

    private function sendMessageApproveInGroup($student_id, $group_id): void
    {
        logger('pass');
        $user = UserModel::find($student_id);
        $group = GroupsModel::with(['diploma'])->find($group_id);
        $diploma = $group['diploma'];

        GroupsUsersModel::create([
            'user_id' => $student_id,
            'group_id' => $group_id,
        ]);

        $templateStr = $this->generateMessage($user['name'], $diploma['name'], $group['name']);

        Mail::send(new VerifyPaymentsEmail($user['email'], $templateStr));
    }

    private function generateMessage($username, $diplomaName, $groupName): string
    {
        $message = "<p>Estimado(a) <strong>$username</strong>,</p>\n";
        $message .= "<p>¡Felicitaciones! Nos complace informarle que ha sido inscrito(a) en el diplomado <strong>$diplomaName</strong>, en el grupo <strong>$groupName</strong>.</p>\n";
        $message .= "<p>Estamos seguros de que este nuevo ciclo será una experiencia enriquecedora para su desarrollo profesional y personal. ¡Le deseamos mucho éxito en este nuevo desafío!</p>\n";
        $message .= "<p>Recuerde que estamos aquí para apoyarle en todo lo que necesite. No dude en ponerse en contacto con nosotros si tiene alguna duda o requiere asistencia.</p>\n";
        $message .= "<p style='color: #5a6268; font-size: .8em;'>*La información de este correo, así como la contenida en los documentos adjuntos, puede ser objeto de solicitudes de acceso a la información.</p>\n";

        return $message;
    }

}
