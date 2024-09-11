<?php

namespace App\Library;

use App\Constants\ResponseCodesConstants;
use App\Models\UserProfilePictureModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

class UserProfilePictureLibrary
{
    const DIR_PATH = 'UserProfilePicture/';
    const LOCAL_DISK = 'local';

    protected LoggerInterface $log;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = $logger;
    }

    /*************************************************
     *  CRUD UserProfile
     *************************************************/

    public function getProfilePicture($data, $type): JsonResponse|string|null
    {
        try {
            $file = Storage::disk(self::LOCAL_DISK)->exists(self::DIR_PATH . $data . '/' . $type . 'profile_picture.png');

            if ($file) {
                return Storage::disk(self::LOCAL_DISK)->get(self::DIR_PATH . $data . '/' . $type . 'profile_picture.png');
            }
            return response()->json(["status" => false]);
        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    public function addProfilePicture(Request $data): JsonResponse
    {
        try {
            $file = $data->file('profile');
            $file_original = $data->file('profile_original');
            $path_original = self::DIR_PATH . $data->user_id . '/original_profile_picture.png';
            $path = self::DIR_PATH . $data->user_id . '/profile_picture.png';

            $userProfile = UserProfilePictureModel::where('user_id', $data->user_id)->first();

            if (empty($userProfile)) {
                $profile = new UserProfilePictureModel();
                $profile->user_id = $data->user_id;
                $profile->crop_setting = $data->crop_setting;
                $profile->path = $path;
                $profile->path_original = $path_original;
                $profile->disk = self::LOCAL_DISK;
                $profile->save();

                $message = 'Foto de perfil agregado correctamente';
            }

            if (isset($userProfile)) {
                $userProfile->crop_setting = $data->crop_setting;
                $userProfile->path = $path;
                $userProfile->path_original = $path_original;
                $userProfile->update();

                $message = 'Foto de perfil actualizado correctamente';
            }

            if ($file_original) {
                Storage::disk('local')->put($path_original, File::get($file_original));
            }

            Storage::disk('local')->put($path, File::get($file));

            return response()->json(['status' => true, 'message' => $message], 200);
        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    public function deleteProfilePicture(Request $request): JsonResponse
    {
        try {
            $user_id = $request->user_id;

            $profilePicture = UserProfilePictureModel::where('user_id', $user_id)->first();

            if (isset($profilePicture)) {
                $profilePicture->delete();

                Storage::disk(self::LOCAL_DISK)->deleteDirectory(self::DIR_PATH . $user_id);

                return response()->json(['status' => true, 'message' => 'Perfil borrado', 'type' => 'delete_image']);
            }

            return response()->json(['status' => false, 'message' => 'No se encontro datos del perfil']);
        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

    /*************************************************
     *  Aux
     *************************************************/
    public function settingProfile(Request $request): JsonResponse
    {
        try {
            $id = $request->user_id;

            $config = UserProfilePictureModel::where('user_id', $id)->first();

            return response()->json(["status" => true, "config" => $config]);
        } catch (Exception $e) {
            $this->log->error(__CLASS__ . " " . __FUNCTION__ . " Exception " . $e->getMessage() . " " . $e->getTraceAsString());
            return response()->json(["status" => false, "message" => ResponseCodesConstants::INTERNAL_ERROR['message']], ResponseCodesConstants::INTERNAL_ERROR['code']);
        }
    }

}
