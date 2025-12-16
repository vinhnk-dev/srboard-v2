<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Auth;
use app\Models\Config;
use App\Http\Controllers\Controller;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use App\Repositories\GroupRepository;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $projectRepository;
    protected $groupRepository;
    protected $statusRepository;
    protected $projectStatusRepository;
    public function __construct(
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        GroupRepository $groupRepository,
        StatusRepository $statusRepository
    ) {
        parent::__construct($projectRepository, $userRepository);
        $this->groupRepository = $groupRepository;
        $this->statusRepository = $statusRepository;
    }

    public function create()
    {
        $this->context['group'] = $this->groupRepository->all();
        $this->context['status'] =  $this->statusRepository->all();
        return parent::create();
    }

    public function edit($id)
    {
        $this->context['group'] =  $this->repo->getGroupAssign($id);
        $this->context['status'] = $this->repo->getProjectStatus($id);
        return parent::edit($id);
    }

    public function checkProjectCode(Request $request)
    {
        return response()->json(['exists' => Project::where('project_code', $request->input('project_code'))->exists()]);
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate($this->repo->rules());
        $data = [
            "project_name" => $validated_data["project_name"],
            "project_code" => $validated_data["project_code"],
            "project_type" => $validated_data["project_type"],
            "active" => $request->input("active") ?? 0,
            "git_url" => $validated_data["git_url"],
            "description" => $validated_data["description"],
            "url" => $validated_data["url"],
            "group_assignment_id" => $request->input("group_assignment_id"),
            "status_id" => $request->input("status_id"),
            "show" => $request->input("show"),
        ];

        $id = $request->input('id');
        if ($id) {
            $project = $this->repo->update($id, $data);
        } else {
            $data["user_id"] = Auth::user()->id;
            $project = $this->repo->store($data);
        }

        $data = [
            "project_id" => $project->id
        ];
        $show = $request->input("show");
        foreach ($request->input("status_id") as $status_id) {
            if ($show) {
                $data['show'] = array_search($status_id, $show) === false ? 0 : 1;
            } else {
                $data['show'] = 0;
            }
            $data['status_id'] = $status_id;
            $st = ProjectStatus::updateOrCreate($data);
        }

        // Check the condition, if from group => return to group else return to edit group
        if ($request->groupId) {
            return redirect()->route("admin.group.show", ["id" => $request->groupId]);
        }

        return redirect()
            ->route("admin.projects.index")
            ->with("status", "Update Complete !");
    }

    // public function selected_theme(Request $request)
    // {
    //     $theme = $request->input('theme') ?? 'issues';
    //     $data = [
    //         "key" => 'issue_theme',
    //         "val" => $theme,
    //         "user_id" => Auth::user()->id
    //     ];
    //     Config::updateOrCreate($data);

    //     return response('<script>location.href="/projects/' . $request->input('project_id') . '/' . $theme . '";</script>');
    // }

    public function delete($id)
    {
        return parent::delete($id);
    }

    public function forcesDelete($id)
    {
        $this->context['project'] = $this->repo->forcesDeleteRelationship($id);
        return parent::forcesDelete($id);
    }
}
