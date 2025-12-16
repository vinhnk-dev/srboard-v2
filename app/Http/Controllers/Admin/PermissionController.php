<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(Permission $permissionModel, User $userModel)
    {
        $this->permission = $permissionModel;

        $this->user = $userModel;
    }

    public function index()
    {
        $permission = $this->permission::all();
        return view("Permission.Permission", compact("permission"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("Permission.CreatePermission");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            "name" => ["required", "string", "max:255"],
        ]);
        $permission = Permission::create($validated_data);
        return redirect()
            ->route("admin.permission")
            ->with("status", "Complete");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permission = $this->permission->findOrfail($id);
        return view("Permission.EditPermission", compact("permission"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated_data = $request->validate([
            "name" => ["required", "string", "max:255"],
        ]);
        $permission = $this->permission->findOrfail($id);
        $permission->update($validated_data);
        return redirect()
            ->route("admin.permission")
            ->with("status", "Successfull");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = $this->permission->findOrfail($id);
        return view("Permission.DeletePermission", compact("permission"));
    }
    public function delete($id)
    {
        $permission = $this->permission->findOrfail($id);
        $permission->delete();
        return redirect()->route("admin.permission");
    }
}
