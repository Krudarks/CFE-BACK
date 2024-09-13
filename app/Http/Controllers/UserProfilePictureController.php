<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePictureRequest;
use App\Library\UserProfilePictureLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfilePictureController extends Controller
{
    protected UserProfilePictureLibrary $userProfilePictureLibrary;

    public function __construct(UserProfilePictureLibrary $userProfilePictureLibrary)
    {
        $this->userProfilePictureLibrary = $userProfilePictureLibrary;
    }

    /*************************************************
     *  CRUD UserProfile
     *************************************************/

    public function get($id, $type = ''): JsonResponse|string|null
    {
        return $this->userProfilePictureLibrary->getProfilePicture($id, $type);
    }

    /**
     * @response App\Http\Resources\SystemSetting\AddProfileResource
     */
    public function add(ProfilePictureRequest $request): JsonResponse
    {
        return $this->userProfilePictureLibrary->addProfilePicture($request);
    }

    /**
     * @response App\Http\Resources\SystemSetting\DeleteProfileResource
     */
    public function delete(ProfilePictureRequest $request): JsonResponse
    {
        return $this->userProfilePictureLibrary->deleteProfilePicture($request);
    }

    /*************************************************
     *  Aux
     *************************************************/

    /**
     * @response App\Http\Resources\SystemSetting\SettingProfileResource
     */
    public function settingProfile(Request $request)
    {
        return $this->userProfilePictureLibrary->settingProfile($request);
    }
}
