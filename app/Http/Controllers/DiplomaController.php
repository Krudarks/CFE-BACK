<?php

namespace App\Http\Controllers;

use App\Models\DiplomaModel;
use App\Models\GroupsModel;
use App\Models\ModuleModel;
use Illuminate\Http\Request;

class DiplomaController extends Controller
{
    public function index()
    {
        return DiplomaModel::all();
    }

    public function diplomaAndGroups()
    {
        $diploma = DiplomaModel::with('groups')->get();
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $diploma]);
    }

    public function show($id)
    {
        return DiplomaModel::findOrFail($id);
    }

    public function store(Request $request)
    {
        $course = DiplomaModel::create($request->all());
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $course], 201);
    }

    public function update(Request $request, $id)
    {
        $course = DiplomaModel::findOrFail($id);
        $course->update($request->all());
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $course],);
    }

    public function delete($id)
    {
        $groups = GroupsModel::where('diploma_id', $id)->get();
        $idsGroups = $groups->pluck('id')->toArray();
        $modules = ModuleModel::whereIn('group_id', $idsGroups)->get();
        $modules->each->delete();
        $groups->each->delete();

        DiplomaModel::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Eliminado']);
    }
}
