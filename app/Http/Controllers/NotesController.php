<?php

namespace App\Http\Controllers;

use App\Models\NotesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function index()
    {
        $authenticatedUser = Auth::user();

        return NotesModel::where('user_id', $authenticatedUser->id)->get();
    }

    public function show($id)
    {
        return NotesModel::findOrFail($id);
    }

    public function store(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = Auth::user()->id;
        $note = NotesModel::create($params);
        return response()->json(['status' => true, 'message' => 'Note created successfully', 'data' => $note], 201);
    }

    public function update(Request $request, $id)
    {
        $note = NotesModel::findOrFail($id);
        $note->update($request->all());
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $note],);
    }

    public function delete($id)
    {
        NotesModel::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Eliminado']);
    }
}
