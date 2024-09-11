<?php

namespace App\Http\Controllers;

use App\Models\GroupsModel;
use App\Models\GroupsUsersModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = GroupsModel::withCount('studentUsers')->get();
        return response()->json($groups);
    }

    public function byUser()
    {
        $authenticatedUser = Auth::user();

        $groups = GroupsUsersModel::where('user_id', $authenticatedUser->id)->with(['group' => function ($q) {
            $q->withCount('studentUsers');
        }] )->get();

        $pluckGroup = $groups->pluck('group')->toArray();

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $pluckGroup]);
    }

    public function byDiploma($id)
    {
        $groups = GroupsModel::where('diploma_id', $id)->withCount('studentUsers')->get();

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $groups]);
    }

    public function getTeacher($id)
    {
        $groups = GroupsUsersModel::where('group_id', $id)
            ->whereHas('user.teacher')
            ->whereHas('user.role', function ($query) {
                $query->where('code', 'teacher');
            })
            ->with(['user.teacher'])->get();

        $users = $groups->pluck('user');

        $idsInGroup = $users->pluck('id')->toArray();

        $userFree = UserModel::whereNotIn('id', $idsInGroup)->whereHas('role', function ($query) {
            $query->where('code', '!=', 'admin')->where('code', 'teacher');
        })->with(['teacher'])->get();

        return response()->json(['status' => true, 'message' => 'Success', 'users' => $users, 'freeUsers' => $userFree]);
    }


    public function getStudent($id)
    {
        $groups = GroupsUsersModel::where('group_id', $id)
            ->whereHas('user.student')
            ->whereHas('user.role', function ($query) {
                $query->where('code', 'student');
            })
            ->with(['user.student'])->get();


        $users = $groups->pluck('user');

        $idsInGroup = $users->pluck('id')->toArray();

        $userFree = UserModel::whereNotIn('id', $idsInGroup)->whereHas('role', function ($query) {
            $query->where('code', '!=', 'admin')->where('code', 'student');
        })->with(['student'])->get();

        return response()->json(['status' => true, 'message' => 'Success', 'users' => $users, 'freeUsers' => $userFree]);
    }

    public function linkUser(Request $request)
    {
        $link = GroupsUsersModel::create($request->all());
        $link->load('user.student');
        $link->load('user.teacher');

        $user = $link->user;

        return response()->json(['status' => true, 'message' => 'Success', 'data' => $user]);
    }

    public function unLinkUser(Request $request)
    {
        $unlink = GroupsUsersModel::where('group_id', $request->group_id)->where('user_id', $request->user_id)->first();

        if (is_null($unlink)) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }

        $unlink->delete();
        return response()->json(['status' => true, 'message' => 'Success']);
    }

    public function show($id)
    {
        return GroupsModel::findOrFail($id);
    }

    public function store(Request $request)
    {
        $groups = GroupsModel::create($request->all());
        $groups['users_count'] = 0;
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $groups], 201);
    }

    public function update(Request $request, $id)
    {
        $groups = GroupsModel::findOrFail($id);
        $groups->update($request->all());
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $groups]);
    }

    public function delete($id)
    {
        GroupsModel::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Eliminado']);
    }
}
