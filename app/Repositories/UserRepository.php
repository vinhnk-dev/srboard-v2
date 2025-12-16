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



    public function createUser(array $userData, array $groupIds, $roleNames)
    {
        $user = $this->model->create([
            'username' => $userData['username'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'active' => isset($userData['active']) ? 1 : 0,
        ]);

        if ($user->id && !empty($groupIds)) {
            foreach ($groupIds as $groupId) {
                $data = [
                    'user_id' => $user->id,
                    'group_id' => $groupId,
                ];
                UserGroup::create($data);
            }
        }


        $this->sendWelcomeEmail($userData);

        $this->assignRole($user, $roleNames);

        return $user;
    }

    public function sendWelcomeEmail(array $userData)
    {
        $email = $userData['email'];

        Mail::send([], [], function (Message $message) use ($email, $userData) {
            $message->to($email);
            $message->subject("Welcome to MA-board");
            $message->text(
                "Welcome to MA-board, " .
                    $userData['name'] .
                    "\n\n" .
                    "Here is your account information to login:" .
                    "\n" .
                    "URL: " . env("APP_URL") .
                    "\n" .
                    "Username: " .
                    $userData['username'] .
                    "\n" .
                    "Password: " .
                    $userData['password'] .
                    "\n\n" .
                    "Remember to change your password." .
                    "\n" .
                    "~ Best regards ~"
            );
        });
    }

    public function assignRole(User $user, $roleName)
    {
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

    public function manageUserRoles($user, $roleName)
    {
        $permissions = Permission::all();
        $user->syncRoles([]);
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $role = Role::create(['name' => $roleName]);
        }
        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }

    public function sendPasswordResetEmail($user, $newPassword)
    {
        $email = $user->email;

        Mail::send([], [], function (Message $message) use ($email, $newPassword) {
            $message->to($email);
            $message->subject("Your password has been reset!");
            $message->text(
                "Your password has been reset.\n" .
                    "Here is your new password: " . $newPassword .
                    "\n\n" .
                    "URL: " . env("APP_URL") .
                    "\n\n" .
                    "If you have any problem, please contact the administrator for support."
            );
        });
    }

    public function updateAvatar($user, $avatarFile)
    {
        if ($avatarFile) {
            $path = "images/avatar/" . $user->avatar;
            if (file_exists($path)) {
                @unlink($path);
            }

            $get_name_image = $avatarFile->getClientOriginalName();
            $path = "images/avatar/";
            $name_image = current(explode(".", $get_name_image));
            $new_image =
                $name_image .
                rand(0, 99) .
                "." .
                $avatarFile->getClientOriginalExtension();
            $avatarFile->move($path, $new_image);
            $user->avatar = $path . $new_image;
        }
        $user->save();

        return $user;
    }
}
