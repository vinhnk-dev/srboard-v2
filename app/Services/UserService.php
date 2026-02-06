<?php

namespace App\Services;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Events\UserPasswordUpdated;
use App\Events\UserCreated;

class UserService extends BaseService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    public function getBaseUrl()
    {
        return 'admin.users';
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data){
           if (empty($data['password'])) {
                $plainPassword = Str::random(12);
                $data['password'] = $plainPassword;
            } 
            $user = $this->userRepository->createUser($data);
            if($user && !empty($data['user_group_id']))
            {
                $this->userRepository->assignGroup($user->id,$data['user_group_id']);
            }
            if($user && !empty($data['role']))
            {
               $this->userRepository->assignRole($user->id,$data['role']);
            }
            event(new UserCreated($data));
            return $user;
        });
    }

   public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $passChanged = false;
            $newPassword = null;

            if (!empty($data['password'])) {
                $newPassword = $data['password'];
                $data['password'] = Hash::make($data['password']);
                $passChanged = true;
            } else {
                unset($data['password']);
            }

            $user = $this->userRepository->updateUser($id, $data);

            if (isset($data['user_group_id'])) {
                $this->userRepository->manageUserGroups($id, $data['user_group_id']);
            }

            if (isset($data['role'])) {
                $this->userRepository->manageUserRoles($id, $data['role']);
            }

            if ($passChanged && $newPassword) {
                event(new UserPasswordUpdated($id, $newPassword));
            }
            return $user;
        });
    }



}
