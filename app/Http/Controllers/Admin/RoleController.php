<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(Role $roleModel, User $userModel)
    {
        $this->role = $roleModel;

        $this->user = $userModel;
    }

    public function index()
    {
        $role = $this->role::all();
        return view("Role.Role", compact("role"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("Role.CreateRole");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            "name" => ["required", "string", "max:255"],
        ]);
        $role = Role::create($validated_data);
        return redirect()->route("admin.role");
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
    public function edit($id)
    {
        $role = $this->role->findOrfail($id);
        return view("Role.EditRole", compact("role"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated_data = $request->validate([
            "name" => ["required", "string", "max:255"],
        ]);
        $role = $this->role->findOrfail($id);
        $role->update($validated_data);
        return redirect()->route("admin.role");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = $this->role->findOrfail($id);
        return view("Role.DeleteRole", compact("role"));
    }

    public function delete($id)
    {
        $role = $this->role->findOrfail($id);
        $role->delete();
        return redirect()->route("admin.role");
    }
}
