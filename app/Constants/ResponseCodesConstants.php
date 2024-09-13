<?php

namespace App\Constants;

class ResponseCodesConstants
{
    const UNAUTHORISED = ['message' => 'UNATHORISED', 'code' => 401 ];
    const SUCCESS = ['message' => 'SUCCESS', 'code' => 200 ];
    const INTERNAL_ERROR = ['message' => 'INTERNAL_ERROR',  'code' => 500];
    const INVALID_DATA = ['message' => 'INVALID_DATA', 'code' => 422];
    const SIGNATURE_ERROR = 501;
    const BAD_REQUEST = 400;

    const BY_CODE = 9001;
    const BY_JSON = 9002;

    const CODE_ERROR = [
        'INTERNAL_ERROR' =>             ['message' => 'Error Interno del Servidor',  'code' => 500],
        'PARAMETROS_INCORRECTOS'=>      ['message' => 'Faltan parametros', 'code' => 422, 'local' => true],
    ];

    const LIB_ERROR = [
    ];
}
