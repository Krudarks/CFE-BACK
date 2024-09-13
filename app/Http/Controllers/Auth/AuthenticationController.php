<?php

namespace App\Http\Controllers\Auth;

use App\Constants\RolesConstants;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{

    /**
     * Handle an incoming authentication request.
     */
    public function store()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = UserModel::where('id', Auth::user()->id)->with('role')->first();

            if ($user->role->code == RolesConstants::WORKER) {
                $user->load('worker');
            } else if ($user->role->code == RolesConstants::TEST) {
                $user->load('test');
            }

            $user_token = $user->createToken('appToken')->accessToken;

            return response()->json(['status' => true, 'token' => $user_token, 'user' => $user], 200);
        } else {
            // failure to authenticate
            return response()->json(['status' => false, 'message' => 'Failed to authenticate.'], 401);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function destroy(Request $request)
    {
        if (Auth::user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully',
            ], 200);
        }
    }

}
