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

    public function issueImages($issue_id)
    {
        return IssuePicture::where("issue_id", "=", $issue_id)->get();
    }

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
        $assigned = UserAssignment::select("users.*")
            ->join("users", "users.id", "=", "user_assignments.user_id")
            ->where("user_assignments.issue_id", "=", $issue_id)
            ->get();
        if ($convertToStringList) return $this->toStringList($assigned, 'name', "Not assigned yet");
        return $assigned;
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

    public function getUserAssign($issue_id)
    {
        $assigned = UserAssignment::where('issue_id', '=', $issue_id)->get();
        $users = User::all();
        foreach ($users as $user) {
            $user->active = "";
            foreach ($assigned as $assign) {
                if ($user->id == $assign->user_id) {
                    $user->active = "selected";
                    break;
                }
            }
        }
        return $users;
    }

    public function getUserReporter($issue_id)
    {
        $assigned = AssignReporter::where('issue_id', '=', $issue_id)->get();
        $users = User::all();
        foreach ($users as $user) {
            $user->active = "";
            foreach ($assigned as $assign) {
                if ($user->id == $assign->user_id) {
                    $user->active = "selected";
                    break;
                }
            }
        }
        return $users;
    }

    public function getPitures($issue_id)
    {
        return IssuePicture::where("issue_id", $issue_id)
            ->get();
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
        $attributes['user_id'] = Auth::user()->id;
        $attributes["project_id"] = request()->parentid;
        $issue = parent::create($attributes);
        if ($issue) {
            $this->updateAssgined($issue->id);
            $this->updateReporter($issue->id);
            $this->updatePictures($issue->id);

            $this->load_full_display_detail($issue);
            //send email to reporter, assigned, author
            $issue->sendCreatedMail();
        }
        return $issue;
    }

    public function update($issue_id, $attributes = [])
    {
        $issue_old = Issue::find($issue_id);

        $this->write_update_log($issue_old);
        $this->load_full_display_detail($issue_old);

        unset($attributes['user_id']);
        if (!Auth::user()->hasRole('Admin')) unset($attributes['project_id']);
        if (parent::update($issue_id, $attributes)) {
            $issue = Issue::find($issue_id);

            $this->updateAssgined($issue_id);
            $this->updateReporter($issue_id);
            $this->updatePictures($issue_id);

            $this->load_full_display_detail($issue);

            $html_changed = $issue->compair($issue_old);
            //send email to reporter, assigned, author
            $issue->sendUpdatedMail($html_changed);
            //leave a comment about changed
            Comment::create(['issue_id' => $issue_id, 'comment' => $html_changed, 'user_id' => Auth::user()->id]);
        }
        return $issue;
    }

    public function updateStatus($issue_id, $attributes = [])
    {
        $issue_old =  Issue::select("issues.*", "statuses.is_check_due")
        ->join("statuses", "statuses.id", "issues.status")
        ->where("issues.id", $issue_id)
        ->first();

        $this->write_update_log($issue_old);
        $this->load_full_display_detail($issue_old);
        unset($attributes['user_id']);
        if (!Auth::user()->hasRole('Admin')) unset($attributes['project_id']);

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

    public function updateSortIndex()
    {
        $position = request()->position;
        foreach ($position as $key => $value) {
            Issue::where("id", $value)
                ->update([
                    "order_by" => $key
                ]);
        }
    }

    public function updateAssgined($issue_id)
    {
        if (request()->has("user_assign")) {
            $assigned = request()->input("user_assign");
            $existingAssigned = UserAssignment::where("issue_id", $issue_id)->pluck("user_id")->toArray();

            UserAssignment::where("issue_id", $issue_id)
                ->whereIn("user_id", array_diff($existingAssigned, $assigned))->delete();

            foreach (array_diff($assigned, $existingAssigned) as $assignee) {
                UserAssignment::create(["issue_id" => $issue_id, "user_id" => $assignee]);
            }
        }
    }

    public function updateReporter($issue_id)
    {
        if (request()->has("report_assign")) {
            $reporterAssigned = request()->input("report_assign");
            $existingAssigned = AssignReporter::where("issue_id", $issue_id)->pluck("user_id")->toArray();

            AssignReporter::where("issue_id", $issue_id)
                ->whereIn("user_id", array_diff($existingAssigned, $reporterAssigned))->delete();

            foreach (array_diff($reporterAssigned, $existingAssigned) as $assignee) {
                AssignReporter::create(["issue_id" => $issue_id, "user_id" => $assignee]);
            }
        }
    }

    public function updatePictures($issue_id)
    {
        if (request()->has("pic_url")) {
            $keep = request()->input("pic_url");
            $olds = IssuePicture::where("issue_id", $issue_id)->whereNotIn("picture_url", $keep)->get();
            $publicPath = public_path() . '/';
            foreach ($olds as $old) if (unlink($publicPath . $old->picture_url)) $old->delete();
        }
        if (request()->has("picture_url")) {
            $uploaded = request()->file("picture_url");
            $path = "images/issue/";
            $idata = ["issue_id" => $issue_id];
            foreach ($uploaded as $image) {
                $idata["picture_url"] = save_upload_file($image, $path);
                IssuePicture::create($idata);
            }
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
        $attributes['user_id'] = Auth::user()->id;
        $attributes["issue_id"] = request()->id;

        $comment = parent::create($attributes);
        if ($comment) {
            $issue = Issue::find(request()->id);
            $this->load_full_display_detail($issue);
            $issue->sendCommentMail($comment);
        }
        return $comment;
    }

    public function pageLeftTools($theme)
    {
        $tools = '';
        if ($theme == 'agile') {
            $tools .= '<a href="/projects/' . request()->parentid . '/issues?theme=issues" class="btn btn-sm btn-danger text-white">Grid Board</a>
            <button type="button" class="btn btn-sm btn-gray text-white ml-1" disabled >Agile board</button>';
        } else {
            $tools .= '<button type="button" class="btn btn-sm btn-gray text-white" disabled>Grid Board</button>
            <a href="/projects/' . request()->parentid . '/issues?theme=agile" class="btn btn-sm btn-danger text-white ml-1">Agile board</a>';
        }
        return $tools;
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
