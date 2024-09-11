<?php

namespace App\Http\Controllers;

use App\Models\TeacherModel;
use App\Models\UserModel;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        return UserModel::whereHas('teacher')->with(['teacher', 'role'])->get();
    }

    public function show($id)
    {
        return TeacherModel::where('id', $id)->with('user')->first();
    }

    public function update(Request $request, $id)
    {

        $user = UserModel::findOrFail($id);
        $user->name = $request->name;
        $user->save();

        $teacher = TeacherModel::where('user_id', $id)->first();
        $teacher->update([
            'curp' => $request->curp,
            'ine' => $request->ine,
            'cedula' => $request->cedula,
            'phone' => $request->phone,
        ]);
        return response()->json(['data' => [
            'user' => $user,
            'teacher' => $teacher,
        ], 'status' => true, 'message' => 'Successfully updated!']);
    }

    public function setQualification(Request $request)
    {
        return response()->json([], 200);
    }
}
