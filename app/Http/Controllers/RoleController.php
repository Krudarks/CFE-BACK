<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return RoleModel::with('permissions')->get();
    }

    public function store(Request $request)
    {
        $role = RoleModel::create($request->all());
        if ($request->permissions) {
            $role->permissions()->attach($request->permissions);
        }
        return response()->json($role, 201);
    }

    public function show($id)
    {
        return RoleModel::with('permissions')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $role = RoleModel::findOrFail($id);
        $role->update($request->all());
        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }
        return response()->json($role, 200);
    }

    public function destroy($id)
    {
        RoleModel::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
