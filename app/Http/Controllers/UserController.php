<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\GroupRepository;


class UserController extends Controller
{
    protected $repo;
    protected $groupRepository;
    protected $roleModel;

    public function __construct(
        UserRepository $repo,
        GroupRepository $groupRepository,
        Role $roleModel
    ) {
        parent::__construct($repo,$repo);
        $this->groupRepository = $groupRepository;
        $this->roleModel = $roleModel;
    }

    public function create()
    {
        $this->context['group'] = $this->groupRepository->all();
        return parent::create();
    }

    public function edit($id)
    {
        $this->context['user']  = $this->repo->find($id);
        $this->context['group']  = $this->groupRepository->all($id);
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
        return parent::customView("User.view");
    }
    public function delete($id)
    {
        return parent::delete($id);
    }
    public function forcesDelete($id)
    {
        $this->context["user"] = $this->repo->forceDeleteRelationship($id);
        return parent::forcesDelete($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string"],
        ]);
        $userData = [
            "name" => $request->name,
            "email" => $request->email,
        ];

        $user = $this->repo->find($request->input('id'));

        if ($user) {
            if ($request->input('isUser')) {
                if (!empty($request->input("password"))) {
                    $password = bcrypt($request->input("password"));
                    $userData = [
                        "name" => $request->name,
                        "email" => $request->email,
                        "password" => $password,
                    ];
                }
                if ($request->hasFile('avatar')) {
                    $avatarFile = $request->file('avatar');
                    $user = $this->repo->updateAvatar($user, $avatarFile);
                }
                $this->repo->updateUser($user->id, $userData);
                return redirect()->route('index');
            } else {
                $groupIds = $request->input("user_group_id") ?? [];
                $roleNames = $request->input("role") ?? [];

                if (!empty($request->input("password"))) {
                    $password = bcrypt($request->input("password"));
                    $userData = [
                        "name" => $request->name,
                        "email" => $request->email,
                        "password" => $password,
                        "active" => $request->active ? 1 : 0,
                    ];
                } else {
                    $userData = [
                        "name" => $request->name,
                        "email" => $request->email,
                        "active" => $request->active ? 1 : 0,
                    ];
                }
                $this->repo->updateUser($user->id, $userData);
                $this->repo->manageUserGroups($user->id, $groupIds);
                $this->repo->manageUserRoles($user, $roleNames);
            }
        } else {
            $request->validate([
                "username" => ["required", "string", "max:255", "unique:" . $this->repo->getModel()],
                "password" => ["required"],
                "name" => ["required"],
            ]);
            $userData = [
                "username" => $request->username,
                "password" => $request->password,
                "name" => $request->name,
                "email" => $request->email,
            ];


            $groupIds = $request->input("user_group_id") ?? [];
            $roleNames = $request->input("role") ?? [];
            $user = $this->repo->createUser($userData, $groupIds, $roleNames);
        }

        return redirect()->route('admin.users.index');
    }

    public function login()
    {
        return view("User.login");
    }

    public function login_submit(Request $request)
    {
        $login = [
            "username" => $request->username,
            "password" => $request->password,
        ];

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
