<?php

namespace App\Http\Controllers;

use App\Models\WorkerModel;

class WorkerController extends Controller
{
    public function index()
    {
        return WorkerModel::with('user')->get();
    }

    public function show($id)
    {
        return WorkerModel::where('id', $id)->with('user')->first();
    }
}
