<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\UserGroup;
use App\Models\GroupAssignment;
use App\Models\User;

class GroupRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\Group::class;
    }

    public function getBaseUrl()
    {
        return "admin.group";
    }
    public function getSearchFields()
    {
        return ['group_name'];
    }

    public function rules()
    {
        return [
            "group_name" => ["required", "string", "max:255"],
            "color" => ["required"],
        ];
    }

    public function all($user_id = 0)
    {
        $groups = $this->model->get();
        if ($user_id < 1) return $groups;

        $mygroups = $this->model->select('groups.*')
            ->leftjoin("user_groups", "user_groups.group_id", "=", "groups.id")
            ->where('user_groups.user_id', '=', $user_id)
            ->get();
        foreach ($groups as $group) {
            foreach ($mygroups as $mygroup) {
                if ($group->id == $mygroup->id) $group->active = "selected";
            }
        }
        return $groups;
    }

    public function countMember($groupId)
    {
        return $this->model
            ->join('user_groups as ug', 'groups.id', '=', 'ug.group_id')
            ->join('users as u', 'u.id', '=', 'ug.user_id')
            ->where('ug.group_id', '=',  $groupId)
            ->whereNull('u.deleted_at')
            ->count();
    }

    public function listMember($groupId, $convertToStringList = false)
    {
        $list = $this->model->select('u.*')
            ->join('user_groups as ug', 'groups.id', '=', 'ug.group_id')
            ->join('users as u', 'u.id', '=', 'ug.user_id')
            ->where('ug.group_id', '=',  $groupId)
            ->whereNull('u.deleted_at')
            ->get();
        if ($convertToStringList) return $this->toStringList($list, 'name', 'No member');
        return $list;
    }

    public function countProject($groupId)
    {
        return $this->model
            ->join('group_assignments as ga', 'groups.id', '=', 'ga.group_id')
            ->join('projects as p', 'p.id', '=', 'ga.project_id')
            ->where('groups.id', '=',  $groupId)
            ->whereNull('p.deleted_at')
            ->count();
    }

    public function listProject($groupId, $convertToStringList = false)
    {
        $list = $this->model->select('p.*')
            ->join('group_assignments as ga', 'groups.id', '=', 'ga.group_id')
            ->join('projects as p', 'p.id', '=', 'ga.project_id')
            ->where('groups.id', '=',  $groupId)
            ->whereNull('p.deleted_at')
            ->distinct()
            ->get();
        if ($convertToStringList) return $this->toStringList($list, 'project_name', 'No project');
        return $list;
    }

    public function forceDeleteRelationship($group_id)
    {
        GroupAssignment::where('group_id', $group_id)->forceDelete();
        $users = UserGroup::where('group_id', $group_id)->get();

        foreach ($users as $user) {
            $user->forceDelete();
        }
    }

    public function show($groupId)
    {
        $group = $this->model->find($groupId);

        $users = User::join("user_groups", "users.id", "=", "user_groups.user_id")
            ->select("users.*", "user_groups.group_id", "user_groups.user_id")
            ->where("user_groups.group_id", "=", $groupId)
            ->whereNull('users.deleted_at')
            ->get();

        $project = Project::leftJoin(
                "group_assignments",
                "group_assignments.project_id",
                "=",
                "projects.id"
            )
            ->select("projects.*")
            ->where("group_assignments.group_id", "=", $groupId)
            ->whereNull('projects.deleted_at')
            ->get();

        return [
            "group" => $group,
            "users" => $users,
            "project" => $project,
        ];
    }

    public function getGroupOfUser($user_id, $convertToStringList = false)
    {
        $mygroups = $this->model->select('groups.*')
            ->leftjoin("user_groups", "user_groups.group_id", "=", "groups.id")
            ->where('user_groups.user_id', '=', $user_id)
            ->get();

        if ($convertToStringList) {
            $list = "";
            foreach ($mygroups as $group) {
                $list .= $group->group_name . ", ";
            }
            $list = substr_replace($list, "", -2);
            return $list != "" ? $list : "Don't have group yet";
        }
        return $mygroups;
    }


    public function getUsersOfGroup($groupId)
    {
        $groupAssign = UserGroup::where("group_id", '=', $groupId)
            ->pluck("user_id")
            ->all();
        $users = User::whereNull('deleted_at')->get();
        foreach ($users as $user) {
            $user->assigned = in_array($user->id, $groupAssign);
        }
        return $users;
    }

    public function getProjectsOfGroup($groupId)
    {
        $groupAssign = GroupAssignment::where("group_id", $groupId)
            ->pluck("project_id")
            ->all();
        $projects = Project::whereNull('deleted_at')->get();
        foreach ($projects as $project) {
            $project->assigned = in_array($project->id, $groupAssign);
        }
        return $projects;
    }

    public function store(array $data)
    {
        $group = $this->model->create([
            "group_name" => $data["group_name"],
            "user_id" => $data["user_id"]
        ]);

        if ($group->id && !empty($data["user_group_id"])) {
            foreach ($data["user_group_id"] as $userId) {
                $userGroupData = [
                    "group_id" => $group->id,
                    "user_id" => $userId,
                ];
                UserGroup::create($userGroupData);
            }
        }

        if ($group->id && !empty($data["group_assign_id"])) {
            foreach ($data["group_assign_id"] as $projectId) {
                $groupAssignmentData = [
                    "group_id" => $group->id,
                    "project_id" => $projectId,
                ];
                GroupAssignment::create($groupAssignmentData);
            }
        }

        return $group;
    }
    public function update($id, $data = [])
    {
        $group = $this->model->find($id);

        if ($group) {
            $group->update($data);
            $this->updateUserGroupLinks($group, $data);
            $this->updateGroupAssignmentLinks($group, $data);

            return $group;
        }

        return null;
    }

    private function updateUserGroupLinks($group, $data)
    {
        $userGroupIds = $data["user_group_id"];
        UserGroup::where('group_id', $group->id)->delete();
        if (!empty($userGroupIds)) {
            $userGroupData = [];
            foreach ($userGroupIds as $userId) {
                $userGroupData[] = [
                    'group_id' => $group->id,
                    'user_id' => $userId,
                ];
            }
            UserGroup::insert($userGroupData);
        }
    }

    private function updateGroupAssignmentLinks($group, $data)
    {
        GroupAssignment::where('group_id', $group->id)->delete();
        if (!empty($data["group_assign_id"])){
            $groupAssignmentIds = $data["group_assign_id"];
            $groupAssignmentData = [];
            foreach ($groupAssignmentIds as $projectId) {
                $groupAssignmentData[] = [
                    'group_id' => $group->id,
                    'project_id' => $projectId,
                ];
            }
            GroupAssignment::insert($groupAssignmentData);
        }
    }
    public function delete($id)
    {
        $group = $this->model->findOrFail($id);
        // Delete group assignments
        GroupAssignment::where("group_id", "=", $id)->delete();
        // Delete user groups
        UserGroup::where("group_id", $id)->delete();
        // Delete the group itself
        $group->delete();
    }
}
