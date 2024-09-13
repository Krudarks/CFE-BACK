<?php

namespace App\Http\Controllers;

use App\Constants\ResponseCodesConstants;
use Exception;
use Illuminate\Http\Request;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;

class VehicleController extends Controller
{
    protected LoggerInterface $log;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = $logger;
    }

    public function index()
    {
        try {
            $vehicles = VehicleModel::all();

            if ($vehicles->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron Vehículos',
                    'status' => false
                ]);
            }

            return response()->json([
                'vehicles' => $vehicles,
                'status' => true
            ], 200);

        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    public function show($id)
    {
        try {
            $vehicle = VehicleModel::findOrFail($id);

            return response()->json([
                'data' => $vehicle,
                'status' => true
            ], 200);

        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plates' => 'required|string|max:255',
        ]);

        $validatedData['vehicle_number'] = $this->generateUniqueVehicleNumber();

        try {
            $vehicle = VehicleModel::create($validatedData);

            Log::info('Vehicle Created: ', $vehicle->toArray());

            return response()->json([
                'status' => true,
                'message' => 'Vehicle created successfully',
                'data' => $vehicle
            ], 201);
        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $vehicle = VehicleModel::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'plates' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors(),
                    'status' => false
                ], 400);
            }

            $vehicle->update($request->only(['brand', 'model', 'plates']));

            return response()->json([
                'message' => 'Vehículo actualizado',
                'data' => $vehicle,
                'status' => true
            ], 200);

        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    public function delete($id)
    {
        try {
            $vehicle = VehicleModel::findOrFail($id);

            $vehicle->delete();

            return response()->json([
                'status' => true,
                'message' => 'Vehicle deleted successfully'
            ], 200);

        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    private function generateUniqueVehicleNumber()
    {
        do {
            $vehicleNumber = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (VehicleModel::where('vehicle_number', $vehicleNumber)->exists());

        return $vehicleNumber;
    }
}
