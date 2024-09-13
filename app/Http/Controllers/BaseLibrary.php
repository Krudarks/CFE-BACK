<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;

class BaseLibrary
{

    public function getAuthenticatedUser()
    {
        return Auth::user();
    }

}
