<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AssignController extends Controller
{
    public function __construct(
        Permission $permissionModel,
        Role $roleModel,
        User $userModel
    ) {
        $this->permissions = $permissionModel;
        $this->roles = $roleModel;
        $this->users = $userModel;
    }
    public function index()
    {
        $user = User::with("role", "permissions")->get();
        return view("Permission.AssignRole", compact("user"));
    }
    public function rolePermission()
    {
        $role = $this->roles->with("permissions")->get();

        return view("Permission.Assign", compact("role"));
    }

    public function assignPermission($id)
    {
        $role = $this->roles->findOrfail($id);
        $permission = Permission::all();
        $get_permission = $role->permissions;
        return view(
            "Permission.AssignPermission",
            compact("permission", "role", "get_permission")
        );
    }
    public function insertPermission(Request $request, $id)
    {
        $data = $request->all();
        $role = $this->roles->find($id);

        // dd(isset($data['permission']) ? 'co' : 'lkk');

        $role->syncPermissions(
            isset($data["permission"]) ? $data["permission"] : []
        );
        return redirect()
            ->route("admin.assign")
            ->with("status", "Successfully assigning permission");
    }

    public function assignRole($id)
    {
        $user = User::findOrfail($id);
        $role = Role::all();
        $get_colum_roles = $user->roles;

        foreach ($role as $roles) {
            foreach ($get_colum_roles as $gcr) {
                if ($roles->id == $gcr->id) {
                    $roles->active = "checked";
                } else {
                    if ($roles->active == "checked") {
                        continue;
                    }
                    $roles->active = "";
                }
            }
        }

        return view(
            "Permission.AssignRole",
            compact("user", "role", "get_colum_roles")
        );
    }
    public function insertRole(Request $request, $id)
    {
        $data = $request->all();
        $user = $this->users->find($id);
        $user->syncRoles($data["role"]);
        return redirect()
            ->route("admin.users.index")
            ->with("status", "Complete");
    }
}
