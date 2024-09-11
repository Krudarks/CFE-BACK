<?php
namespace App\Trait;

use App\Constants\EmailTemplateConstants;
use App\Models\CatEmailTemplateModel;

trait EmailTemplateTrait
{

    public function getEmailTemplate($templateCode) {
        return CatEmailTemplateModel::where("code", $templateCode)->first();
    }

    /**
     * @param $template | entry of EmailTemplateModel
     * @param $subjectData
     * [
     *      "username" => "Ulises Daniel Luna González",
     *      "subjectTitle" => "Generación de certificados",
     *      "message" =>"Contenido del menasje",
     *      "folio" => "DES.4334",
     *      "startDate" => "2018-07-02",
     *      "endDate" => "2018-08-08"
     * ]
     * @return string
     */
    public function replaceVariablesIntoTemplate($template, $subjectData): string
    {
        $templateStr = str_replace(EmailTemplateConstants::VARIABLE_USERNAME, $subjectData["username"], $template);
        $templateStr = str_replace(EmailTemplateConstants::VARIABLE_STATUS, $subjectData["status"], $templateStr);
        return trim($templateStr);
    }
}
