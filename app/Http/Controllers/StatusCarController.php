<?php

namespace App\Http\Controllers;

use App\Models\StatusCarModel;

class StatusCarController extends Controller
{
    public function index()
    {
        return StatusCarModel::all();
    }

    public function show($id)
    {
        return StatusCarModel::find($id);
    }
}
