<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\GroupRepository;
use App\Http\Requests\UserRequest;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserService;
use App\Services\UserProfileService;


class UserController extends Controller
{
    protected $repo;
    protected $groupRepository;
    protected $roleModel;
    protected $userService;
    protected $userProfileService;

    public function __construct(
        UserRepository $repo,
        GroupRepository $groupRepository,
        Role $roleModel,
        UserService $userService,
        UserProfileService $userProfileService
    ) {
        parent::__construct($repo,$repo);
        $this->groupRepository = $groupRepository;
        $this->roleModel = $roleModel;
        $this->userService = $userService;
        $this->userProfileService = $userProfileService;
    }

    public function create()
    {
        $this->context['group'] = $this->groupRepository->all();
        $this->context['form_action'] = route('admin.users.store');
        return parent::create();
    }

    public function edit($id)
    {
        $this->context['user']  = $this->repo->find($id);
        $this->context['group']  = $this->groupRepository->all($id);
        $this->context['form_action'] = route('admin.users.update', ['id' => request()->id]);
        return parent::edit($id);
    }

    public function show()
    {
        $userid = Auth::user()->id;
        $this->context['group'] = $this->repo->myGroups($userid, true);
        return parent::customView("User.view");
    }

    public function profile($mode)
    {
        $userid = Auth::user()->id;
        $this->context['group'] = $this->repo->myGroups($userid, true);
        $this->context['mode'] = $mode;
        $this->context['form_action'] = route('user.profile.update', ['id' => $userid]);
        return parent::customView("User.view");
    }

    public function delete($id)
    {
        return $this->userService->delete($id);
    }

    public function forcesDelete($id)
    {
        $this->context["user"] = $this->userService->forceDeleteRelationship($id);
        return parent::forcesDelete($id);
    }

    public function store(UserRequest $request)
    {
       $validatedData = $request->validated();
       $this->userService->create($validatedData);
        
        return redirect()->route('admin.users.index');
    }

    public function update ($id, UserRequest $request)
    {
        $validatedData = $request->validated();
        $this->userService->update($id,$validatedData); 
        return redirect()->route('admin.users.index');  
    }
 
    public function updateProfile($id, ProfileUpdateRequest $request)
    {
        $validatedData = $request->validated();
        $this->userProfileService->profileUpdate($id,$validatedData);
        return redirect()->route('index');
    }

    public function login()
    {
        return view("User.login");
    }

    public function login_submit(LoginRequest $request)
    {
        $login = $request->validated();

        if (Auth::attempt($login)) {
            $user = Auth::user();
            if ($user && is_null($user->deleted_at)) {
                return redirect()->intended('/mytask');
            }
        }

        return redirect()
            ->back()
            ->withErrors(['login']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect("/user/login");
    }
}
