<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\WorkerModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class AttendanceController extends Controller
{
    protected LoggerInterface $log;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = $logger;
    }

    // Registrar entrada
    public function registerAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'employeeId' => 'required|string'
        ]);

        // tabla de empleados
        $worker = WorkerModel::where('user_number', $request->employeeId)->with(['user.role'])->first();

        if (!$worker) {
            return response()->json(['message' => 'Número de control no válido']);
        }


        // Verificar si ya ha registrado entrada
        $attendance = AttendanceModel::where('worker_id', $worker->id)
            ->whereDate('date', today())
            ->first();

        if (!$attendance) { // si no existe es entrada se crea registro
            $attendance = AttendanceModel::create([
                'worker_id' => $worker->id,
                'entry_time' => now(),
                'date' => today(),
                'user_number' => $worker->user_number,
                'exit_time' => null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Entrada registrada con éxito',
                'attendance' => $attendance,
                'worker' => $worker
            ]);
        }

        // ya existe es salida se registra salida
        $attendance->exit_time = now();
        $attendance->save();

        return response()->json([
            'status' => true,
            'message' => 'Salida registrada con éxito',
            'attendance' => $attendance,
            'worker' => $worker
        ]);
    }

    // Obtener lista de asistencias
    public function index(): JsonResponse
    {
        $attendances = AttendanceModel::with(['worker.user'])->get();

        return response()->json(['status' => true, 'message' => 'Datos obtenidos', 'attendances' => $attendances]);
    }

    // Obtener detalles de asistencia
    public function show($id): JsonResponse
    {
        $attendance = AttendanceModel::find($id);

        if (!$attendance) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        return response()->json($attendance);
    }

    // Descargar reporte de asistencia en PDF
    public function downloadReport($id)
    {
//        $attendance = AttendanceModel::find($id);
//        if (!$attendance) {
//            return response()->json(['message' => 'Registro no encontrado'], 404);
//        }
//
//
//        $pdf = \PDF::loadView('reports.attendance_report', ['attendance' => $attendance]);
//        return $pdf->download('reporte_asistencia_' . $id . '.pdf');
    }

    // Eliminar registro de asistencia
    public function destroy($id): JsonResponse
    {
        $attendance = AttendanceModel::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        $attendance->delete();
        return response()->json(['message' => 'Registro eliminado con éxito']);
    }

    // Editar registro de asistencia
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'entry_time' => 'required|date',
            'exit_time' => 'nullable|date',
        ]);

        $attendance = AttendanceModel::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        $attendance->entry_time = $request->entry_time;
        $attendance->exit_time = $request->exit_time;
        $attendance->save();

        return response()->json(['message' => 'Registro actualizado con éxito']);
    }

    // Obtener estado de asistencia
    public function getAttendanceStatus($controlNumber): JsonResponse
    {
        $worker = WorkerModel::where('user_number', $controlNumber)->first();

        if (!$worker) {
            return response()->json(['message' => 'Número de control no válido'], 400);
        }

        $attendance = AttendanceModel::where('worker_id', $worker->id)
            ->whereDate('date', today())
            ->first();

        return response()->json([
            'isCheckedIn' => $attendance ? (bool)$attendance->entry_time : false
        ]);
    }

    public function getAttendanceDetails(Request $request): JsonResponse
    {
        // Obtener los registros de asistencia filtrados por la fecha
        $attendances = AttendanceModel::where('date', $request->date)->with(['worker.user.role'])->get();

        return response()->json(['status' => true, 'message' => 'Datos obtenidos', 'attendances' => $attendances]);
    }

    public function getAllAttendances(): JsonResponse
    {
        $attendanceGroupedByDate = AttendanceModel::select('date')
            ->selectRaw('count(*) as total_entries')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->values()
            ->toArray();

        return response()->json(['status' => true, 'message' => 'Registros Encontrados', 'data' => $attendanceGroupedByDate]);
    }
}
