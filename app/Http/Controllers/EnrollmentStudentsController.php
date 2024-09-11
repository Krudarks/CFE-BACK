<?php

namespace App\Http\Controllers;

use App\Constants\Queue\QueueConstants;
use App\Jobs\AcceptEnrollmentJob;
use App\Models\EnrollmentsStudentsModel;
use App\Models\GroupsModel;
use App\Models\PaymentsModel;
use App\Models\StudentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentStudentsController extends Controller
{
    public function index()
    {
        $document = EnrollmentsStudentsModel::with(['student', 'group'])->get();
        return response()->json(['status' => true, 'data' => $document]);
    }

    public function store(Request $request)
    {
        $authenticatedUser = Auth::user();

        $params = $request->all();
        $params['student_id'] = $authenticatedUser->id;
        $params['status'] = 'pending';

        // Verifica si ya existe una solicitud similar
        $existingRequest = EnrollmentsStudentsModel::where('student_id', $params['student_id'])
            ->where('group_id', $params['group_id']) // Asegúrate de ajustar esto según tu modelo
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json(['status' => false, 'message' => 'Ya existe una solicitud pendiente para este curso']);
        }

        $course = EnrollmentsStudentsModel::create($params);
        if ($course) {
            $course->load('group');
            $group = $course->group;
            $teachers = StudentModel::where('user_id', $params['student_id'])->with(['user'])->first();
            $teachers['group'] = $group;
            return response()->json(['status' => true, 'message' => 'Solicitud realizado con éxito', 'data' => $teachers], 201);
        }

        return response()->json(['status' => false, 'message' => 'No se pudo procesar la solicitud']);
    }

    public function acceptEnrollment($id, Request $request)
    {
        $status = $request->status;

        $enrollment = EnrollmentsStudentsModel::where('id', $id)->with(['student', 'group'])->first();

        $enrollment->status = $status;
        $enrollment->save();

        $user = $enrollment->student;
        $group = $enrollment->group;

        AcceptEnrollmentJob::dispatch($user, $status, $group)->onQueue(QueueConstants::EMAIL_NOTIFICATION);

        // Crear 7 registros de pagos: 1 para inscripción y 6 para los meses
        $payments = [
            ['type_payment' => 'Inscripción'],
            ['type_payment' => 'Pago mes 1'],
            ['type_payment' => 'Pago mes 2'],
            ['type_payment' => 'Pago mes 3'],
            ['type_payment' => 'Pago mes 4'],
            ['type_payment' => 'Pago mes 5'],
            ['type_payment' => 'Pago mes 6'],
        ];

        foreach ($payments as $payment) {
            PaymentsModel::create([
                'student_id' => $user->id,
                'status' => false,
                'path' => null,
                'type_payment' => $payment['type_payment'],
                'group_id' => $group->id,
            ]);
        }

        return response()->json(['status' => true, 'message' => 'No se pudo procesar la solicitud', 'data' => $enrollment]);
    }

    public function getByUser()
    {

        $authenticatedUser = Auth::user();

        $document = EnrollmentsStudentsModel::where('student_id', $authenticatedUser->id)->with(['student', 'group'])->get();

        return response()->json(['status' => true, 'data' => $document]);
    }

    public function verifyAvailable($id)
    {
        $group = GroupsModel::where('id', $id)->first();

        if (!$group) {
            return response()->json(['status' => false, 'message' => 'Grupo no encontrado'], 404);
        }

        // Verifica si hay espacios disponibles
        if ((int)$group->available < (int)$group->limit) {
            $remaining = $group->limit - $group->available;
            return response()->json(['status' => true, 'message' => 'Hay espacios disponibles', 'available_spaces' => $remaining]);
        } else {
            return response()->json(['status' => false, 'message' => 'No hay espacios disponibles'], 200);
        }
    }
}
