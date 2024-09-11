<?php

namespace App\Http\Controllers;

use App\Models\PaymentTeacherGroupsModel;
use App\Models\TeacherModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentsTeacherController extends Controller
{
    public function index()
    {
        $document = PaymentTeacherGroupsModel::with(['teacher', 'group'])->get();
        return response()->json(['status' => true, 'payments' => $document]);
    }

    public function store(Request $request)
    {
        $course = PaymentTeacherGroupsModel::create($request->all());

        if ($course) {
            $teachers = TeacherModel::where('user_id', $request->teacher_id)->with(['groups', 'user'])->get();

            return response()->json(['status' => true, 'message' => 'Pago realizado con éxito', 'data' => $teachers], 201);
        }

        return response()->json(['status' => false, 'message' => 'No se pudo procesar el pago']);
    }

    public function getByUser()
    {

        $authenticatedUser = Auth::user();

        $document = PaymentTeacherGroupsModel::where('teacher_id', $authenticatedUser->id)->with(['teacher', 'group'])->get();

        return response()->json(['status' => true, 'payments' => $document]);
    }

    public function wizardPayment()
    {

        $teachers = TeacherModel::with(['groups', 'user'])->get();

        return response()->json(['status' => true, 'teachers' => $teachers]);
    }
}
