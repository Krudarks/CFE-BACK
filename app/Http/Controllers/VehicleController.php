<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleModel;

class VehicleController extends Controller
{

    public function index()
    {
        // Obtener todos los vehículos
        $vehicles = VehicleModel::with('status')->get();

        // Retornar respuesta
        return response()->json(['status' => true, 'data' => $vehicles]);
    }

    public function show($id)
    {
        // Obtener el vehículo por ID
        $vehicle = VehicleModel::with('status')->find($id);

        // Verificar si el vehículo existe
        if (!$vehicle) {
            return response()->json(['status' => false, 'message' => 'Vehicle not found'], 404);
        }

        // Retornar respuesta
        return response()->json(['status' => true, 'data' => $vehicle]);
    }

    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plates' => 'required|string|max:255',
            'status_id' => 'required|exists:status_cars,id', // Asegúrate de que `status_cars` es la tabla de estados
            // Otros campos necesarios
        ]);

        // Generar un número de vehículo único
        $validatedData['vehicle_number'] = $this->generateUniqueVehicleNumber();

        // Crear el nuevo vehículo
        $vehicle = VehicleModel::create($validatedData);

        // Retornar respuesta
        return response()->json(['status' => true, 'message' => 'Vehicle created successfully', 'data' => $vehicle], 201);
    }

    public function update(Request $request, $id)
    {
        // Obtener el vehículo por ID
        $vehicle = VehicleModel::find($id);

        // Verificar si el vehículo existe
        if (!$vehicle) {
            return response()->json(['status' => false, 'message' => 'Vehicle not found'], 404);
        }

        // Validar los datos recibidos
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'plates' => 'sometimes|string|max:255',
            'status_id' => 'sometimes|exists:status_cars,id', // Asegúrate de que `status_cars` es la tabla de estados
            // Otros campos necesarios
        ]);

        // Actualizar los datos del vehículo
        $vehicle->update($validatedData);

        // Retornar respuesta
        return response()->json(['status' => true, 'message' => 'Vehicle updated successfully', 'data' => $vehicle]);
    }

    public function delete($id)
    {
        // Obtener el vehículo por ID
        $vehicle = VehicleModel::find($id);

        // Verificar si el vehículo existe
        if (!$vehicle) {
            return response()->json(['status' => false, 'message' => 'Vehicle not found'], 404);
        }

        // Eliminar el vehículo
        $vehicle->delete();

        // Retornar respuesta
        return response()->json(['status' => true, 'message' => 'Vehicle deleted successfully']);
    }

    private function generateUniqueVehicleNumber()
    {
        do {
            // Generar número de control aleatorio
            $vehicleNumber = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

            // Verificar si el número ya existe en la tabla de vehículos
        } while (VehicleModel::where('vehicle_number', $vehicleNumber)->exists());

        return $vehicleNumber;
    }
}
