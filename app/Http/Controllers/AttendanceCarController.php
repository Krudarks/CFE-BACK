<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\StatusCarModel;
use App\Models\VehicleModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\AttendanceVehicleModel;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;

class AttendanceCarController extends Controller
{
    protected LoggerInterface $log;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = $logger;
    }

    public function storeDailyReport(Request $request): JsonResponse
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'vehicles' => 'required|array',
            'vehicles.*.id' => 'required|exists:vehicles,id',
            'vehicles.*.status_id' => 'required|exists:status_car,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicles = $request->vehicles;
        $date = $request->date;

        // Guardar el reporte
        foreach ($vehicles as $vehicle) {
            AttendanceVehicleModel::updateOrCreate(
                ['vehicle_id' => $vehicle['id'], 'date' => $date],
                ['status_id' => $vehicle['status_id']]
            );
        }

        return response()->json(['message' => 'Reporte generado exitosamente.']);
    }

    public function getVehiclesForReport(): JsonResponse
    {
        $vehicles = VehicleModel::all(); // Trae todos los vehículos
        $statuses = StatusCarModel::all(); // Trae todos los posibles estados

        return response()->json(['status' => true, 'vehicles' => $vehicles, 'statuses' => $statuses]);
    }

    public function index(): JsonResponse
    {
        $reports = AttendanceVehicleModel::all(); // Obtiene todos los reportes
        return response()->json($reports);
    }

    public function show($id): JsonResponse
    {
        $report = AttendanceVehicleModel::where('id', $id)->first();
        if (!$report) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        return response()->json($report);
    }

    public function destroy($id): JsonResponse
    {
        $report = AttendanceVehicleModel::find($id);
        if (!$report) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        $report->delete();
        return response()->json(['message' => 'Reporte eliminado']);
    }
}
