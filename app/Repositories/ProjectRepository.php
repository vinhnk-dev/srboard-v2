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
    // TODO: Optimize N+1 query all of theme
    public function rules()
    {
        return [
            // 
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
    public function getAssignedGroupIds($projectId)
    {
        return GroupAssignment::where('project_id', $projectId)
            ->pluck('group_id')
            ->toArray();
    }

    public function getProjectStatusesByProjectId($projectId)
    {
        return ProjectStatus::where('project_id', $projectId)->get();
    }

    public function getAllStatuses()
    {
        return Status::all();
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
        return $project;
    }

    public function assignGroups($id, array $groupIds): void
    {
        foreach ($groupIds as $groupId) {
            GroupAssignment::create([
                'project_id' => $id,
                'group_id'   => $groupId,
            ]);
        }
    }

    public function addStatuses($id, array $statusIds): void
    {
        foreach ($statusIds as $statusId) {
            ProjectStatus::create([
                'project_id' => $id,
                'status_id'  => $statusId,
            ]);
        }
    }

    public function update($id, $data = [])
    {
        $project = $this->model->find($id);

        if ($project) {
            $project->update($data);
            return $project;
        }

        return null;
    }

    public function updateGroupAssignments($projectId, $groupAssignmentIds)
    {
        $groupAssignmentData = [];

        foreach ($groupAssignmentIds as $groupId) {
            $groupAssignmentData[] = [
                "project_id" => $projectId,
                "group_id" => $groupId,
            ];
        }
        GroupAssignment::where('project_id', $projectId)
            ->delete();
        if (!empty($groupAssignmentData)) {
            GroupAssignment::insert($groupAssignmentData);
        }
    }

    public function updateProjectStatuses($projectId, $statusIds, $showFlags)
    {
        $projectStatusData = [];

        foreach ($statusIds as $key => $statusId) {
            $show = isset($showFlags[$key]) ? 1 : 0;
            $projectStatusData[] = [
                "project_id" => $projectId,
                "status_id" => $statusId,
                "show" => $show,
            ];
        }
        ProjectStatus::where('project_id', $projectId)
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

    public function existsByCode(string $code, ?int $exceptId = null): bool
    {
        $query = Project::where('project_code', $code);
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return $query->exists();
    }
}
