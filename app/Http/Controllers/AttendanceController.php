<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\WorkerModel;
use Carbon\Carbon;
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
    public function registerEntry(Request $request)
    {
        $request->validate([
            'controlNumber' => 'required|string'
        ]);

        $worker = WorkerModel::where('user_number', $request->controlNumber)->first();

        if (!$worker) {
            return response()->json(['message' => 'Número de control no válido'], 400);
        }

        // Verificar si ya ha registrado entrada
        $attendance = AttendanceModel::where('worker_id', $worker->id)
            ->whereDate('date', today())
            ->first();

        if ($attendance && $attendance->entry_time) {
            return response()->json(['message' => 'Ya has registrado entrada'], 400);
        }

        if ($attendance) {
            $attendance->entry_time = now();
        } else {
            $attendance = new AttendanceModel();
            $attendance->worker_id = $worker->id;
            $attendance->entry_time = now();
            $attendance->date = today();
        }
        $attendance->save();

        return response()->json(['message' => 'Entrada registrada con éxito']);
    }

    // Registrar salida
    public function registerExit(Request $request)
    {
        $request->validate([
            'controlNumber' => 'required|string'
        ]);

        $worker = WorkerModel::where('user_number', $request->controlNumber)->first();

        if (!$worker) {
            return response()->json(['message' => 'Número de control no válido'], 400);
        }

        $attendance = AttendanceModel::where('worker_id', $worker->id)
            ->whereDate('date', today())
            ->first();

        if (!$attendance || !$attendance->entry_time) {
            return response()->json(['message' => 'No has registrado entrada'], 400);
        }

        if ($attendance->exit_time) {
            return response()->json(['message' => 'Ya has registrado salida'], 400);
        }

        $attendance->exit_time = now();
        $attendance->save();

        return response()->json(['message' => 'Salida registrada con éxito']);
    }

    // Obtener lista de asistencias
    public function index()
    {
        $attendances = AttendanceModel::with('worker')
            ->get()
            ->map(function ($attendance) {
                $attendance->worker_count = $attendance->worker()->count(); // Establecer worker_count
                return $attendance;
            });

        return response()->json($attendances);
    }

    // Obtener detalles de asistencia
    public function show($id)
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
        $attendance = AttendanceModel::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }


        $pdf = \PDF::loadView('reports.attendance_report', ['attendance' => $attendance]);
        return $pdf->download('reporte_asistencia_' . $id . '.pdf');
    }

    // Eliminar registro de asistencia
    public function destroy($id)
    {
        $attendance = AttendanceModel::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        $attendance->delete();
        return response()->json(['message' => 'Registro eliminado con éxito']);
    }

    // Editar registro de asistencia
    public function update(Request $request, $id)
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
    public function getAttendanceStatus($controlNumber)
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

    public function getAttendanceDetails($date)
    {
        // Obtener los registros de asistencia filtrados por la fecha
        $attendances = AttendanceModel::where('date', $date)->with('worker')->get();

        return response()->json($attendances);
    }

    public function getAllAttendances()
    {
        $attendances = AttendanceModel::all();
        if ($attendances->isEmpty()) {
            return response()->json(['message' => 'No se encontraron registros'], 404);
        }

        return response()->json($attendances);
    }
}
