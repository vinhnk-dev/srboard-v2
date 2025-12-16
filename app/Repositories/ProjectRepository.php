<?php

namespace App\Repositories;

use App\Models\AssignReporter;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueHistory;
use App\Models\IssuePicture;
use App\Models\UserAssignment;
use App\Repositories\BaseRepository;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectStatus;
use App\Models\GroupAssignment;
use App\Models\Group;
use App\Models\Status;

class ProjectRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\Project::class;
    }

    public function getBaseUrl()
    {
        return "admin.projects";
    }
    public function getSearchFields()
    {
        return ['project_name'];
    }

    public function rules()
    {
        return [
            "project_name" => ["required"],
            "project_code" => ["required"],
            "project_type" => ["required"],
            "git_url" => ["required"],
            "description" => ["required"],
            "url" => ["required"],
        ];
    }

    public function search($trash = false, $query = null, $rowlimit = true)
    {
        return  parent::search(
            $trash,
            function (&$builder) {
                $builder->join("users", "projects.user_id", "=", "users.id")
                    ->select("projects.*", "users.name");
            }
        );
    }

    public function getStatuses($id)
    {
        $statuses = ProjectStatus::select('project_statuses.*', 'statuses.status_name', 'statuses.color')
            ->join('statuses', 'statuses.id', 'project_statuses.status_id')
            ->where("project_id", $id)
            ->get();
        foreach ($statuses as $st) {
            $st->issuesNum = Issue::where("project_id", $id)
                ->where("status", $st->status_id)
                ->whereNull("deleted_at")
                ->count();
        }
        return $statuses;
    }

    public function forceDelete($id)
    {
        GroupAssignment::where('project_id', '=', $id)->forceDelete();
        ProjectStatus::where('project_id', '=', $id)->forceDelete();
        $issues = Issue::where('project_id', '=', $id)->get();

        foreach ($issues as $issue) {
            IssuePicture::where('issue_id', $issue->id)->forceDelete();
            Comment::where('issue_id', $issue->id)->forceDelete();
            UserAssignment::where('issue_id', $issue->id)->forceDelete();
            AssignReporter::where('issue_id', $issue->id)->forceDelete();
            IssueHistory::where("issue_id", $issue->id)->forceDelete();
            $issue->forceDelete();
        }
        return parent::forceDelete($id);
    }

    public function groupAssigned($project_id, $convertToStringList = false)
    {
        $groups = $this->model->select("groups.*")
            ->join("group_assignments as ga", "ga.project_id", '=', 'projects.id')
            ->join("groups", "groups.id", "=", "ga.group_id")
            ->where("ga.project_id", '=', $project_id)
            ->get();
        if ($convertToStringList) return $this->toStringList($groups, 'group_name', "No assigned");
        return $groups;
    }

    public function getGroupAssign($id)
    {
        $groupAssign = GroupAssignment::where('project_id', $id)->get();
        $group = Group::all();
        foreach ($group as $g) {
            foreach ($groupAssign as $ga) {
                if ($g->id == $ga->group_id) {
                    $g->active = "selected";
                } else {
                    if ($g->active == "selected") {
                        continue;
                    }
                    $g->active = "";
                }
            }
        }
        return $group;
    }

    public function getProjectStatus($id)
    {
        $projectStatus = ProjectStatus::where('project_id', $id)->get();
        $status = Status::all();
        foreach ($status as $st) {
            $st->active = "";
            $st->check = "";
            foreach ($projectStatus as $pjst) {
                if ($st->id == $pjst->status_id) {
                    $st->active = "selected";
                } else {
                    if ($st->active == "selected") {
                        continue;
                    }
                    $st->active = "";
                }
                //check project
                if ($pjst->show == 1) {
                    $st->check = "checked";
                } else {
                    $st->check = "";
                }
            }
        }

        return $status;
    }

    public function getShortInfo($project)
    {
        $project->Status = ProjectStatus::select('statuses.status_name', 'project_statuses.status_id')
            ->join('statuses', 'statuses.id', 'project_statuses.status_id')
            ->where("project_id", $project->id)
            ->where("show", "=", true)
            ->get();

        foreach ($project->Status as $st) {
            $st->stCount = Issue::join("statuses", "statuses.id", "issues.status")
                ->where("issues.status", "=", $st->status_id)
                ->where("issues.project_id", "=", $project->id)
                ->count();
        }

        $project->overdue = Issue::join('project_statuses', 'project_statuses.id', 'issues.status')
            ->where("project_statuses.show", "=", true)
            ->where('issues.project_id', $project->id)
            ->whereRaw(' STR_TO_DATE(issues.due_date, "%m/%d/%Y") < CURDATE()')
            ->count();
    }

    public function store(array $data)
    {
        $project = $this->model->create($data);

        if ($project->id && !empty($data["group_assignment_id"])) {
            foreach ($data["group_assignment_id"] as $groupId) {
                $projectAssignmentData = [
                    "project_id" => $project->id,
                    "group_id" => $groupId,
                ];
                GroupAssignment::create($projectAssignmentData);
            }
        }

        if ($project->id && !empty($data["status_id"])) {
            foreach ($data["status_id"] as $statusId) {
                $projectStatusData = [
                    "project_id" => $project->id,
                    "status_id" => $statusId,
                ];
                ProjectStatus::create($projectStatusData);
            }
        }

        return $project;
    }

    public function update($id, $data = [])
    {
        $project = $this->model->find($id);

        if ($project) {
            $validated_data = [
                "project_name" => $data["project_name"],
                "project_code" => $data["project_code"],
                "project_type" => $data["project_type"],
                "active" => $data["active"],
                "git_url" => $data["git_url"],
                "description" => $data["description"],
                "url" => $data["url"],
            ];

            $project->update($validated_data);

            $this->updateGroupAssignments($project, $data["group_assignment_id"]);
            $this->updateProjectStatuses($project, $data["status_id"], $data["show"]);

            return $project;
        }

        return null;
    }

    private function updateGroupAssignments($project, $groupAssignmentIds)
    {
        $groupAssignmentData = [];

        foreach ($groupAssignmentIds as $groupId) {
            $groupAssignmentData[] = [
                "project_id" => $project->id,
                "group_id" => $groupId,
            ];
        }
        GroupAssignment::where('project_id', $project->id)
            ->delete();
        if (!empty($groupAssignmentData)) {
            GroupAssignment::insert($groupAssignmentData);
        }
    }

    private function updateProjectStatuses($project, $statusIds, $showFlags)
    {
        $projectStatusData = [];

        foreach ($statusIds as $key => $statusId) {
            $show = isset($showFlags[$key]) ? 1 : 0;
            $projectStatusData[] = [
                "project_id" => $project->id,
                "status_id" => $statusId,
                "show" => $show,
            ];
        }
        ProjectStatus::where('project_id', $project->id)
            ->delete();

        if (!empty($projectStatusData)) {
            ProjectStatus::insert($projectStatusData);
        }
    }

    public function forcesDeleteRelationship($project_id)
    {
        $project_assignments = GroupAssignment::where('project_id', $project_id)->get();
        $issues = Issue::where('project_id', $project_id)->get();
        $project_status = ProjectStatus::where('project_id', $project_id)->get();

        foreach ($project_assignments as $assignment) {
            $assignment->forceDelete();
        }

        foreach ($issues as $issue) {
            $issue->forceDelete();
        }
        foreach ($project_status as $status) {
            $status->forceDelete();
        }
    }
}
