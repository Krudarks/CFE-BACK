<?php

namespace App\Http\Controllers;

use App\Constants\ResponseCodesConstants;
use App\Constants\RolesConstants;
use App\Models\RoleModel;
use App\Models\TestModel;
use App\Models\UserModel;
use App\Models\WorkerModel;
use App\Trait\LoginAttemptsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class UserController extends Controller
{

    use LoginAttemptsTrait;

    protected LoggerInterface $log;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = $logger;
    }

    public function index()
    {
        return UserModel::with(['worker', 'test', 'role'])->get();
    }

    public function lasted()
    {
        return UserModel::whereHas('worker')->with(['worker', 'role'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function show($id)
    {
        return UserModel::where('id', $id)->with(['test', 'worker'])->get();
    }

    public function store(Request $request)
    {
        $userForm = $request->users;

        $role = RoleModel::where('id', $userForm['role_id'])->first();

        $user = UserModel::create($userForm);

        if ($role->code == RolesConstants::WORKER) {
            $worker = $request->workerForm;
            $worker['user_id'] = $user->id;
            $worker['user_number'] = $this->generateUniqueUserNumber();
            WorkerModel::create($worker);
            $user->load('worker');
        } else if ($role->code == RolesConstants::TEST) {
            $test = $request->testForm;
            $test['user_id'] = $user->id;
            TestModel::create($test);
            $user->load('test');
        }

        $user->load('role');
        return response()->json(['status' => true, 'message' => 'success', 'data' => $user], 201);
    }

    private function generateUniqueUserNumber()
    {
        do{
            //Generar numero de control aleatorio
            $userNumber = str_pad(random_int(0,99999),5,'0',STR_PAD_LEFT);

            //Verificar si el numero ya existe
        }while
        (WorkerModel::where('user_number', $userNumber)->exists());

        // Retorna el número generado
        return $userNumber;
    }



    public function update(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        $user->update([
            'name' => $request->name,
        ]);

        return response()->json(['status' => true, 'message' => 'success', 'data' => $user]);
    }

    public function delete($id)
    {
        $user = UserModel::where('id', $id)->with('role')->first();

        if (is_null($user)) {
            return response()->json(['status' => false, 'message' => 'Not Found User']);
        }

        if ($user->id === 1) {
            return response()->json(['status' => false, 'message' => 'Not Delete Admin']);
        }

        if ($user->role->code == RolesConstants::TEST) {
            TestModel::where('user_id', $id)->forceDelete();
        } else if ($user->role->code == RolesConstants::WORKER) {
            WorkerModel::where('user_id', $id)->forceDelete();
        }

        $user->forceDelete();

        return response()->json(['status' => true, 'message' => 'Eliminado']);
    }


    /**
     * @return JsonResponse
     * Verifica que el password sea correcta
     */
    public function checkPassword(): JsonResponse
    {
        try {
            $authenticatedUser = Auth::user();

            $email = $authenticatedUser->email;

            $this->checkLoginAttempt($email);

            if (Auth::guard('web')->attempt(['email' => $email, 'password' => request('password')])) {

                $this->clearLoginAttempts($email);

                return response()->json(["status" => true, "message" => "Contraseña correcta"]);
            } else {
                return response()->json(["status" => false, "message" => "Contraseña incorrecta"], 201);
            }
        } catch (\Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => $e->getMessage()], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Actualiza el password del usuario
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $authenticatedUser = Auth::user();

            $password = $request->input("password");

            $user = UserModel::where("email", $authenticatedUser->email)->first();

            $user->password = Hash::make($password);

            $user->setRememberToken(Str::random(60));

            $user->save();

//            Auth::user()->AauthAcessToken()->delete();

            return response()->json(["status" => true, "message" => "Contraseña actualizada con éxito"]);
        } catch (\Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => $e->getMessage()]);

        }
    }
}
