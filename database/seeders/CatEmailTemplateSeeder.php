<?php

namespace Database\Seeders;

use App\Constants\EmailTemplateConstants;
use App\Models\CatEmailTemplateModel;
use Illuminate\Database\Seeder;

class CatEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $nameApp = config("app.name");

        $welcomeTemplate = '<p>Estimado usuario</p>
                                <p>Es de nuestro agrado informarle que ha sido registrado en el  ' . $nameApp . '. </p>
                                <p>Por favor haga click en el siguiente botón para cambiar su contraseña</p>
                                <p>Si necesita ayuda, contacte por favor con el administrador.</p>
                                <p><strong>Atentamente</strong></p>
                                <p>' . $nameApp . '</p>';

        $welcome = CatEmailTemplateModel::where("code", EmailTemplateConstants::WELCOME_TEMPLATE_CODE)->first();

        if (is_null($welcome)) {
            $params = [
                "template" => $welcomeTemplate,
                "original_template" => $welcomeTemplate,
                "title" => EmailTemplateConstants::WELCOME_TEMPLATE_TITLE,
                "code" => EmailTemplateConstants::WELCOME_TEMPLATE_CODE,
                "description" => 'Email de notificación para nuevos usuarios'
            ];

            CatEmailTemplateModel::create($params);
        }

    }
}
