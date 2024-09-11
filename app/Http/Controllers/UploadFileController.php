<?php

namespace App\Http\Controllers;

use App\Models\DocumentsInscriptionModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadFileController extends Controller
{

    public function index()
    {
        $document = UserModel::with('documents')->whereHas('student')->get();

        return response()->json(['status' => true, 'document' => $document]);
    }


    public function getByUser()
    {
        $authenticatedUser = Auth::user();

        $document = DocumentsInscriptionModel::where('user_id', $authenticatedUser->id)->get();

        return response()->json(['status' => true, 'document' => $document]);
    }


    public function show($id)
    {
        $document = DocumentsInscriptionModel::where('id', $id)->first();

        $path = $document->path;

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

        $data = json_decode($request->input('data'), true);

        if (!$request->hasfile('file')) {
            return response()->json(['status' => false, 'message' => 'No existe un archivo a subir'], 403);
        }

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        $path = "$authenticatedUser->id/" . $data['type'];

        if (!Storage::disk('local')->exists($path)) {
            Storage::disk('local')->makeDirectory($path);
        }

        $file->move(Storage::disk('local')->path($path), $filename);
        $relativePath = $path . DIRECTORY_SEPARATOR . $filename;

        $document = DocumentsInscriptionModel::updateOrCreate(
            ['type' => $data['type'], 'user_id' => $authenticatedUser->id],
            [
                'name' => $filename,
                'path' => $relativePath,
            ]);

        return response()->json(['status' => true, 'message' => 'success', 'data' => $document], 201);
    }
}
