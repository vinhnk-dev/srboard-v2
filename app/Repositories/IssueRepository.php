<?php

namespace App\Repositories;

use App\Models\AssignReporter;
use App\Models\Comment;
use App\Models\IssueHistory;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\UserAssignment;
use App\Repositories\BaseRepository;
use App\Models\Issue;
use App\Models\IssuePicture;
use App\Models\Status;
use App\Models\User;
use Auth;

class IssueRepository extends BaseRepository
{
    public function getModel()
    {
        return Issue::class;
    }

    public function getBaseUrl()
    {
        return "issues";
    }

    public function getSearchFields()
    {
        return ["issues.title", "users.name", "u.name"];
    }

    public function rules()
    {
        return [
            "title" => ["required", "string", "max:255"],
            "status" => ["required", "integer"],
            "url" => ["required"],
            "issue_description" => ["required", "string"],
            "due_date" => ["required"],
            "project_id" => ["required", "integer"]
        ];
    }

    public function find($id)
    {
        return $this->model
            ->join("statuses", "statuses.id", "=", "issues.status")
            ->join("users", "users.id", "=", "issues.user_id")
            ->join("projects", "projects.id", "=", "issues.project_id")
            ->where('issues.id', '=', $id)
            ->select(
                "issues.*",
                "statuses.status_name",
                "statuses.color",
                "users.name as authorname",
                "projects.project_code",
                "projects.id as project_id"
            )
            ->get()->first();
    }

    public function search($trash = false, $query = null, $rowlimit = true)
    {
        return  parent::search(
            $trash,
            function (&$builder) {
                $projectId = addslashes(request()->parentid);
                $builder->join("statuses", "statuses.id", "issues.status")
                    ->join("users", "users.id", "=", "issues.user_id")
                    ->join("projects", "projects.id", "issues.project_id")
                    ->leftjoin("user_assignments", "issues.id", "=", "user_assignments.issue_id")
                    ->leftjoin("users as u", "u.id", "=", "user_assignments.user_id")
                    ->where("issues.project_id", "=", $projectId);
                $where = [];
                if (request()->status_search != '')
                    foreach (explode(",", request()->status_search) as $stt) $where[] = "issues.status=$stt";
                if (request()->user_assignee != '')
                    $where[] = "user_assignments.user_id=" . request()->user_assignee;
                if (count($where) > 0) $builder->whereRaw(implode(" OR ", $where));
                $builder->select(
                    "issues.*",
                    "statuses.status_name",
                    "statuses.color",
                    "statuses.is_check_due",
                    "users.name as authorname",
                    "projects.project_code",
                    "projects.id as project_id",
                    "projects.project_name"
                )->distinct();
            },
            $rowlimit
        );
    }

    // public function issueImages($issue_id)
    // {
    //     return IssuePicture::where("issue_id",$issue_id)->get();
    // }

    public function issueComments($issue_id)
    {
        return Comment::select("comments.*", "users.name as username", "users.avatar as avatar")
            ->join("users", "users.id", "=", "comments.user_id")
            ->where("issue_id", $issue_id)
            ->orderBy('comments.updated_at', 'desc')
            ->get();
    }

    public function issueAssigned($issue_id, $convertToStringList = false)
    {
        $users = UserAssignment::select("users.name")
            ->join("users", "users.id", "=", "user_assignments.user_id")
            ->where("user_assignments.issue_id", $issue_id)
            ->get();
        if ($convertToStringList) return $this->toStringList($users, 'name', "Not set reporter yet");
        return $users;

    }

    public function getReporter($issue_id, $convertToStringList = false)
    {
        $users = User::select('users.*')
            ->join('assign_reporters', 'users.id', '=', 'assign_reporters.user_id')
            ->where('assign_reporters.issue_id', '=', $issue_id)
            ->get();
        if ($convertToStringList) return $this->toStringList($users, 'name', "Not set reporter yet");
        return $users;
    }

    public function getUserAssign($issueId)
    {
        return UserAssignment::where('issue_id', $issueId)
        ->pluck('user_id')
        ->toArray();
    }

    public function getUserReporter($issueId)
    {
        return AssignReporter::where('issue_id', $issueId)
        ->pluck('user_id')
        ->toArray();
    }

    public function getPitures($issue_id)
    {
        return IssuePicture::where("issue_id", $issue_id)->get();
    }

    public function getStatuses($project_id)
    {
        return ProjectStatus::select('project_statuses.*', 'statuses.status_name')
            ->join('statuses', 'statuses.id', 'project_statuses.status_id')
            ->where("project_id", $project_id)
            ->get();
    }

    public function create($attributes = [])
    {
        return parent::create($attributes);
    }

    public function update($issue_id, $attributes = [])
    {
        return parent::update($issue_id, $attributes);
    }

    public function createUpdateComment($issueId, $comment, $userId)
    {
        return Comment::create(['issue_id' => $issueId, 'comment' => $comment, 'user_id' => $userId]);
    }

    public function updateStatus($issue_id, $attributes = [])
    {
        //snapshot 

        // process data 
        unset($attributes['user_id']);
        if (!Auth::user()->hasRole('Admin')) unset($attributes['project_id']);

        //updateData

        if (parent::update($issue_id, $attributes)) {
            $issue = Issue::select("issues.*", "statuses.is_check_due")
                ->join("statuses", "statuses.id", "issues.status")
                ->where("issues.id", $issue_id)
                ->first();
            $this->load_full_display_detail($issue);
            $html_changed = $issue->compair($issue_old);
            //send email to reporter, assigned, author
            $issue->sendUpdatedMail($html_changed);
            $issue->deadline = $issue->deadline();
            $issue->oldDeadline = $issue_old->deadline();
            //leave a comment about changed
            Comment::create(['issue_id' => $issue_id, 'comment' => $html_changed, 'user_id' => Auth::user()->id]);
        }
        return $issue;
    }

    public function enrichIssueForResponse($issue, $old_issue = null)
    {
        $this->load_full_display_detail($issue);
        $issue->deadline = $issue->deadline();

        if ($old_issue) {
            $issue->oldDeadline = $old_issue->deadline();
        }
        return $issue;
    }

    public function getIssueAndStatus($id)
    {
        return  Issue::select("issues.*", "statuses.is_check_due")
        ->join("statuses", "statuses.id", "issues.status")
        ->where("issues.id", $id)
        ->first();
    }

    public function updateSortIndex($positions)
    {
        foreach ($positions as $key => $value) {
            Issue::where("id", $value)
                ->update([
                    "order_by" => $key
                ]);
        }
    }

    public function updateAssigned($issue_id, $assigned)
    {
        $existingAssigned = UserAssignment::where("issue_id", $issue_id)->pluck("user_id")->toArray();

        UserAssignment::where("issue_id", $issue_id)
            ->whereIn("user_id", array_diff($existingAssigned, $assigned))->delete();

        foreach (array_diff($assigned, $existingAssigned) as $assignee) {
            UserAssignment::create(["issue_id" => $issue_id, "user_id" => $assignee]);
        }
    }

    public function updateReporter($issue_id, $reporterAssigned)
    {
        $existingAssigned = AssignReporter::where("issue_id", $issue_id)->pluck("user_id")->toArray();

        AssignReporter::where("issue_id", $issue_id)
            ->whereIn("user_id", array_diff($existingAssigned, $reporterAssigned))->delete();

        foreach (array_diff($reporterAssigned, $existingAssigned) as $assignee) {
            AssignReporter::create(["issue_id" => $issue_id, "user_id" => $assignee]);
        }
    }

    public function updatePictures(int $issueId, array $keepUrls = [], array $newFiles = []): void
    {
        if (!empty($keepUrls)) {
            $this->deleteUnusedPictures($issueId, $keepUrls);
        }
        
        if (!empty($newFiles)) {
            $this->saveNewPictures($issueId, $newFiles);
        }
    }

    private function deleteUnusedPictures(int $issueId, array $keepUrls): void
    {
        $picturesToDelete = IssuePicture::where('issue_id', $issueId)
            ->whereNotIn('picture_url', $keepUrls)
            ->get();
        
        $publicPath = public_path() . '/';
        
        foreach ($picturesToDelete as $picture) {
            $filePath = $publicPath . $picture->picture_url;
            
            if (file_exists($filePath) && @unlink($filePath)) {
                $picture->delete();
            } elseif (!file_exists($filePath)) {
                $picture->delete();
            }
        }
    }

    private function saveNewPictures(int $issueId, array $files): void
    {
        $path = 'images/issue/';
        
        foreach ($files as $file) {
            IssuePicture::create([
                'issue_id' => $issueId,
                'picture_url' => save_upload_file($file, $path)
            ]);
        }
    }

    public function write_update_log($issue)
    {
        $issueHistoryRecord = new IssueHistory();
        $issueHistoryRecord->fill($issue->toArray());
        $issueHistoryRecord->issue_id = $issue->id;
        $issueHistoryRecord->save();
    }

    public function load_full_display_detail(&$issue)
    {
        $issue->reporters = AssignReporter::where("issue_id", $issue->id)->pluck("user_id")->toArray();
        $issue->assignments = UserAssignment::where("issue_id", $issue->id)->pluck("user_id")->toArray();
        $issue->status_name = Status::where('id', '=', $issue->status)->first()->status_name;
        $issue->project = Project::where('id', '=', $issue->project_id)->first();
        $issue->project_name =  $issue->project->project_name;
        $issue->pictures = $this->getPitures($issue->id);
        $issue->reporters_toString = $this->getReporter($issue->id, true);
        $issue->assignments_toString = $this->issueAssigned($issue->id, true);
    }

    public function delete($id)
    {
        return parent::delete($id);
    }

    public function forceDelete($id)
    {
        Comment::where('issue_id', $id)->delete();
        IssueHistory::where('issue_id', $id)->delete();
        AssignReporter::where('issue_id', $id)->delete();
        UserAssignment::where('issue_id', $id)->delete();
        $olds = IssuePicture::where("issue_id", $id)->get();
        $publicPath = public_path() . '/';

        foreach ($olds as $old) if (file_exists($publicPath . $old->picture_url)) {
            if (unlink($publicPath . $old->picture_url)) $old->delete();
        } else $old->delete();
        return parent::forceDelete($id);
    }

    public function countTotal($project_id)
    {
        $total = Issue::where('project_id', $project_id)->whereNull('deleted_at')->count();
        return $total;
    }
    public function comment($attributes = [])
    {
        return Comment::create($attributes);
    }

    public function createChangeComment(int $issueId, string $changes,$userId)
    {
        Comment::create([
            'issue_id' => $issueId,
            'comment' => $changes,
            'user_id' => $userId
        ]);
    }


    public function usersAssigned()
    {
        return User::select("users.*")
            ->join("user_groups", "user_groups.user_id", "users.id")
            ->join("group_assignments", "group_assignments.group_id", "user_groups.group_id")
            ->where("group_assignments.project_id", "=", request()->parentid)
            ->distinct()
            ->get();
    }
}
