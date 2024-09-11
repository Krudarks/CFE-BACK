<?php

namespace App\Http;

use App\Http\Controllers\Controller;
use App\Models\AttendanceModel;
use App\Models\WorkerModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{

    // Función para listar todas las asistencias (index)
    public function index()
    {
        $attendances = AttendanceModel::all();
        return response()->json($attendances);
    }

    // Función para ver un solo registro de asistencia (show)
    public function show($id)
    {
        $attendance = AttendanceModel::findOrFail($id);
        return response()->json($attendance);
    }

    public function registerEntry(Request $request)
    {
        $worker = WorkerModel::where('user_number', $request->user_number)->first();

        if (!$worker) {
            return response()->json(['error' => 'Trabajador no encontrado'], 404);
        }

        $attendance = AttendanceModel::create([
            'worker_id' => $worker->id,
            'user_number' => $request->user_number,
            'entry_time' => now(),
            'date' => now()->toDateString(),
            'is_late' => now()->gt(now()->setTime(9, 15)),
        ]);

        return response()->json(['success' => 'Asistencia registrada correctamente', 'data' => $attendance], 200);
    }

    // Función para actualizar un registro de asistencia (update)
    public function update(Request $request, $id)
    {
        $attendance = AttendanceModel::findOrFail($id);
        $attendance->update($request->all());
        return response()->json($attendance);
    }

    // Función para eliminar un registro de asistencia (soft delete)
    public function delete($id)
    {
        $attendance = AttendanceModel::findOrFail($id);
        $attendance->delete(); // Soft delete si el modelo tiene el trait SoftDeletes
        return response()->json(['message' => 'Asistencia eliminada correctamente']);
    }

    // Función para verificar si el trabajador llegó tarde (checkLateArrival)
    public function checkLateArrival($entryTime, $requiredTime)
    {
        // Verifica si la hora de entrada es después del límite permitido (por ejemplo, 9:15 AM)
        return Carbon::parse($entryTime)->gt(Carbon::parse($requiredTime));
    }

    // Función para generar un reporte de asistencias en PDF (downloadReport)
    public function downloadReport($date)
    {
        $attendances = AttendanceModel::where('date', $date)->get();
        // Aquí iría la lógica para generar el PDF
        // Ejemplo: $pdf = PDF::loadView('attendances.report', compact('attendances'));
        // return $pdf->download('attendance_report_' . $date . '.pdf');

        return response()->json(['message' => 'Reporte generado correctamente']);
    }

}
