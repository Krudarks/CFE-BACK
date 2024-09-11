<?php

namespace App\Http\Controllers;

use App\Models\ModuleModel;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        return ModuleModel::all();
    }

    public function getByGroup($id)
    {
        return ModuleModel::where('group_id', $id)->get();
    }

    public function show($id)
    {
        return ModuleModel::findOrFail($id);
    }

    public function store(Request $request)
    {
        $module = ModuleModel::create($request->all());
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $module], 201);
    }

    public function update(Request $request, $id)
    {
        $module = ModuleModel::findOrFail($id);
        $module->update($request->all());
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $module],);
    }

    public function delete($id)
    {
        ModuleModel::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Eliminado']);
    }
}
