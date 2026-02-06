<?php

namespace App\Repositories;

use App\Models\AssignReporter;
use App\Models\Issue;
use App\Models\Config;
use App\Models\GroupAssignment;
use App\Models\Project;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\UserGroup;
use App\Models\User;
use App\Models\UserAssignment;
use App\Models\Comment;
use App\Models\Board;
use App\Models\IssueHistory;
use App\Models\Group;

class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return User::class;
    }
    public function getBaseUrl()
    {
        return "admin.users";
    }

    public function getSearchFields()
    {
        return ['username', 'email'];
    }

    public function rules()
    {
        return [
            "status_name" => ["required", "string", "max:255"],
            "color" => ["required"],
        ];
    }

    public function search($trash = false, $query = null, $rowlimit = true)
    {
        return  parent::search(
            $trash,
            function (&$builder) {
                $builder->leftJoin("user_groups", "user_groups.user_id", "=", "users.id")
                    ->leftJoin("groups", "user_groups.group_id", "=", "groups.id");
                $search = request()->get('search');
                if($search != "") $builder->orWhere(
                    "group_name",
                    "like",
                    "%" . $search . "%"
                );
                $builder->distinct();
            }
        );
    }

    public function myGroups($user_id, $convertToStringList = false)
    {
        $mygroups = $this->model->select('groups.*')
            ->leftjoin("user_groups", "user_groups.user_id", "=", "users.id")
            ->join("groups", "groups.id", "=", "user_groups.group_id")
            ->where('user_groups.user_id', '=', $user_id)
            ->get();

        if ($convertToStringList) return $this->toStringList($mygroups, 'group_name', "Don't have group yet");
        return $mygroups;
    }

    public function myProjects($user_id, $type = 'Project', $convertToStringList = false){

        $builder = Project::select('projects.*')
            ->join('group_assignments','group_assignments.project_id','=','projects.id')
            ->join('user_groups','user_groups.group_id','=' ,'group_assignments.group_id')
            ->where('projects.project_type','=',$type)
            ->whereNull("projects.deleted_at")
            ->orderBy('project_name', 'asc');
        //Not as super admin
        if($user_id > 1) $builder = $builder->where('user_groups.user_id', '=' , $user_id);
        $myprojects = $builder->distinct()->get();
        if ($convertToStringList) return $this->toStringList($myprojects, 'project_name', "Don't have group yet");
        return $myprojects;
    }

    public function myTasks($user_id){
        return Issue::join("statuses", "statuses.id", "issues.status")
        ->join("users", "users.id", "=", "issues.user_id")
        ->join("projects", "projects.id", "issues.project_id")
        ->join("user_assignments", "issues.id", "=", "user_assignments.issue_id")
        ->where("user_assignments.user_id","=", $user_id)
        ->where("statuses.is_check_due","=","1")
        ->whereNull("projects.deleted_at")
        ->select(
            "issues.*",
            "statuses.status_name",
            "statuses.color",
            "statuses.is_check_due",
            "users.name as authorname",
            "projects.project_code",
            "projects.id as project_id",
            "projects.project_name"
        )->distinct()->orderBy('issues.due_date')->get();
    }

    public function canAccess($projectId, $userId)
    {
        $groupAssignment = GroupAssignment::where('project_id', $projectId)
        ->where('group_id', $userId)
        ->first();

        return $groupAssignment !== null;
    }
    public function setConfig($key, $user_id, $val){
        $config = Config::where('key', $key)
        ->where('user_id', $user_id)
        ->first();
        if($config){
            $config->val =  $val;
            $config->save();
        }else{
            Config::create(['key' => $key, 'val' =>$val, 'user_id' => $user_id]);
        }
    }

    public function getConfig($key, $user_id){
        $config = Config::where('key', $key)
        ->where('user_id', $user_id)
        ->first();
        return $config->val ?? null ;
    }

    public function forceDeleteRelationship($user_id)
    {
        Comment::where('user_id', $user_id)->forceDelete();
        UserGroup::where('user_id', $user_id)->forceDelete();
        UserAssignment::where('user_id', $user_id)->forceDelete();
        AssignReporter::where('user_id', $user_id)->forceDelete();
        Board::where('user_id', $user_id)->forceDelete();
        Group::where('user_id', $user_id)->forceDelete();
        Issue::where('user_id', $user_id)->forceDelete();
        IssueHistory::where('user_id', $user_id)->forceDelete();
        Project::where('user_id', $user_id)->forceDelete();
    }



    public function createUser(array $userData)
    {
        $user = $this->model->create([
            'username' => $userData['username'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'active' => isset($userData['active']) ? 1 : 0,
        ]);

        return $user;
    }

    public function assignGroup($userId, $groupIds)
    {
        foreach ($groupIds as $groupId) {
            $data = [
                'user_id' => $userId,
                'group_id' => $groupId,
            ];
            UserGroup::create($data);
        }
    }


    public function assignRole($userId, $roleName)
    {
        $user = User::find($userId);
        $permissions = Permission::all();
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            $role = Role::create(['name' => $roleName]);
        }

        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }


    public function updateUser($id, $userData)
    {
        $user = $this->model->findOrFail($id);
        $user->update($userData);
        return $user;
    }

    public function manageUserGroups($id, $groupIds)
    {
        UserGroup::where("user_id", $id)
            ->delete();
        foreach ($groupIds as $groupId) {
            $data = [
                "user_id" => $id,
                "group_id" => $groupId,
            ];
            UserGroup::create($data);
        }
    }

    public function manageUserRoles($userId, $roleName)
    {
        $user = User::find($userId);
        $permissions = Permission::all();
        $user->syncRoles([]);
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $role = Role::create(['name' => $roleName]);
        }
        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }


    public function getUserEmail($id)
    {
       return User::where('id', $id)->value('email');
    }
}
