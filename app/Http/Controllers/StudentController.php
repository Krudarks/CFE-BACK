<?php

namespace App\Http\Controllers;

use App\Models\StudentModel;

class StudentController extends Controller
{
    public function index()
    {
        return StudentModel::with('user')->get();
    }

    public function show($id)
    {
        return StudentModel::where('id', $id)->with('user')->first();
    }
}
