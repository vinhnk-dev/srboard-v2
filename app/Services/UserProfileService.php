<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserProfileService extends BaseService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    public function getBaseUrl()
    {
        return 'profile';
    }

    public function profileUpdate($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $avatarFile = null;
            if (!empty($data['avatar'])) {
                $avatarFile = $data['avatar'];
                unset($data['avatar']);
            }

            $user = $this->userRepository->updateUser($id, $data);

            if ($avatarFile) {
                if ($user->avatar) {
                    $oldPath = $user->avatar;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $originalName = $avatarFile->getClientOriginalName();
                $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $avatarFile->getClientOriginalExtension();
                $newImageName = $nameWithoutExtension . rand(0, 99) . '.' . $extension;
                
                $path = "images/avatar/";
                $avatarFile->move($path, $newImageName);

                $user->avatar = $path . $newImageName;
                $user->save();
            }

            return $user;
        });
    }


}
