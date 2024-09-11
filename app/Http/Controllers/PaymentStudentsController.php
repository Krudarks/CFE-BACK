<?php

namespace App\Http\Controllers;

use App\Constants\Queue\QueueConstants;
use App\Jobs\AcceptEnrollmentJob;
use App\Jobs\VerifyPaymentsJob;
use App\Models\PaymentsModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentStudentsController extends Controller
{

    public function index()
    {
        $users = UserModel::whereHas('payments')->whereHas('student')->distinct()->get();

        // Inicializar el array para almacenar los datos de los pagos.
        $result = [];

        foreach ($users as $user) {
            // Buscar los pagos del estudiante y agruparlos por 'group_id'
            $search = PaymentsModel::where('student_id', $user->id)
                ->with('group') // Cargar la relación 'group'
                ->get()
                ->groupBy('group_id')
                ->map(function ($groupPayments) {
                    $group = $groupPayments->first()->group;
                    return [
                        'group_id' => $group->id,
                        'group_name' => $group->name,
                        'group_description' => $group->description,
                        'payments' => $groupPayments->toArray(),
                    ];
                })
                ->values(); // Reiniciar los índices de los arrays

            $user['group'] = $search;

            $result[] = $user;
        }

        $result = collect($result);

        return response()->json(['status' => true, 'payments' => $result]);
    }

    public function getByUser()
    {
        $authenticatedUser = Auth::user();

        $documents = PaymentsModel::where('student_id', $authenticatedUser->id)
            ->with('group') // Cargar la relación 'group'
            ->get()
            ->groupBy('group_id')
            ->map(function ($groupPayments) {
                $group = $groupPayments->first()->group;
                return [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'group_description' => $group->description,
                    'payments' => $groupPayments->toArray(),
                ];
            })
            ->values(); // Para reiniciar los índices de los arrays

        return response()->json(['status' => true, 'document' => $documents]);
    }

    public function show($id)
    {
        $document = PaymentsModel::where('id', $id)->first();

        if (is_null($document)) {
            return response()->json(['status' => false]);
        }

        $path = $document->path;

        if(is_null($path)) {
            return response()->json(['status' => false, 'message' => 'No se ah cargado el archivo']);
        }

        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['status' => false, 'message' => 'File not found']);
        }

        $pdfPath = Storage::disk('local')->path($path);

        $fileContents = file_get_contents($pdfPath);

        if ($fileContents === false) {
            return response()->json(['status' => false, 'message' => 'Failed to read file contents']);
        }

        $file64 = base64_encode($fileContents);

        return response()->json(['status' => true, 'document64' => $file64, 'document' => $document]);
    }

    public function store(Request $request)
    {
        $authenticatedUser = Auth::user();

        if (!$request->hasfile('file')) {
            return response()->json(['status' => false, 'message' => 'no existe un archivo a subir']);
        }

        $payment_id = $request->input('payment_id');
        $document = PaymentsModel::where('id', $payment_id)->first();

        if (is_null($document)) {
            return response()->json(['status' => false, 'message' => 'no existe el registro del pagp']);
        }

        if ($document->status === 'denied') {
            return response()->json(['status' => false, 'message' => 'Su pago ya fue verificado, no puede volver a subir el archivo']);
        }

        $file = $request->file('file');
        $type = $request->input('type_payment');

        $filename = $this->getNameFile($type, $file->getClientOriginalExtension());

        $path = "$authenticatedUser->id/" . 'payments';

        if (!Storage::disk('local')->exists($path)) {
            Storage::disk('local')->makeDirectory($path);
        }

        $file->move(Storage::disk('local')->path($path), $filename);
        $relativePath = $path . DIRECTORY_SEPARATOR . $filename;

        if ($document->path) { // si ya existe uno elimina el archivo viejo
            $existingPath = Storage::disk('local')->path($document->path);
            if (Storage::disk('local')->exists($existingPath)) {
                Storage::disk('local')->delete($existingPath);
            }
        }

        $document->path = $relativePath;
        $document->status = 'in_verify';
        $document->save();

        return response()->json(['status' => true, 'message' => 'success', 'data' => $document], 201);
    }

    public function updateStatusPayment(Request $request)
    {
        $payment_id = $request->payment_id;
        $document = PaymentsModel::where('id', $payment_id)->first();

        if (is_null($document)) {
            return response()->json(['status' => false, 'message' => 'no existe el registro del pago']);
        }

       // in_verify | approve | denied
        $document->status = $request->status;
        $document->save();

        // event verify all payments student_id, $document
        VerifyPaymentsJob::dispatch($document)->onQueue(QueueConstants::EMAIL_NOTIFICATION);
        return response()->json(['status' => true, 'message' => 'Estado del Pago Actualizado', 'data' => $document]);
    }

    private function sanitizeFileName($filename)
    {
        // Convert to lowercase
        $filename = strtolower($filename);

        // Replace spaces and special characters with underscores
        $filename = preg_replace('/[^a-z0-9]+/', '_', $filename);

        // Remove trailing underscores
        return trim($filename, '_');
    }

    private function getNameFile($type, $file): string
    {
        $type = $this->sanitizeFileName($type);

        // Determina el nombre del archivo basado en el tipo
        return match ($type) {
            'inscripci_n' => 'Inscripcion_' . time() . '.' . $file,
            'pago_mes_1' => 'Pago_mes_1_' . time() . '.' . $file,
            'pago_mes_2' => 'Pago_mes_2_' . time() . '.' . $file,
            'pago_mes_3' => 'Pago_mes_3_' . time() . '.' . $file,
            'pago_mes_4' => 'Pago_mes_4_' . time() . '.' . $file,
            'pago_mes_5' => 'Pago_mes_5_' . time() . '.' . $file,
            'pago_mes_6' => 'Pago_mes_6_' . time() . '.' . $file,
            default => 'Archivo_' . time() . '.' . $file,
        };
    }

}
