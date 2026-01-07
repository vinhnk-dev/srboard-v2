<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Auth;
use App\Http\Controllers\Controller;
use App\Repositories\ProjectRepository;
use App\Repositories\GroupRepository;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;

use App\Services\ProjectService;
use App\Services\StatusService;
use App\Services\GroupService;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    protected $projectRepository;
    protected $groupRepository;
    protected $statusRepository;
    protected $projectStatusRepository;

    protected $projectService;
    protected $statusService;
    protected $groupService;

    public function __construct(
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        GroupRepository $groupRepository,
        StatusRepository $statusRepository,
        ProjectService $projectService,
        StatusService $statusService,
        GroupService $groupService
    ) {
        parent::__construct($projectRepository, $userRepository);
        $this->groupRepository = $groupRepository;
        $this->statusRepository = $statusRepository;

        $this->groupService = $groupService;
        $this->statusService = $statusService;
        $this->projectService = $projectService;
    }

    public function create()
    {
        $this->context['group'] = $this->groupService->getAll();
        $this->context['status'] =  $this->statusService->getAll();
        return parent::create();
    }

    public function edit($id)
    {
        $this->context['group'] =  $this->projectService->getGroupAssign($id);
        $this->context['status'] = $this->projectService->getProjectStatus($id);
        return parent::edit($id);
    }

    public function checkProjectCode(Request $request): JsonResponse
    {
        $code = $request->input('project_code');
        $exceptId = $request->input('except_id');
        
        if (empty($code)) {
            return response()->json(['exists' => false]);
        }
        
        $exists = $this->projectService->isProjectCodeExists($code, $exceptId);
        return response()->json(['exists' => $exists]);
    }

    public function store(ProjectRequest $request)
    {
        $validatedData = $request->validated();
        $this->projectService->create($validatedData);
        return $this->redirectAfterSave($request, "Project created successfully!");
    }

    public function update($id, ProjectRequest $request)
    {
        $validatedData = $request->validated();
        $this->projectService->update($id, $validatedData);

       return $this->redirectAfterSave($request, "Project updated successfully!");
    }

    public function delete($id)
    {
        return parent::delete($id);
    }

    public function forcesDelete($id)
    {
        $this->context['project'] = $this->projectService->forcesDeleteRelationship($id);
        return parent::forcesDelete($id);
    }

    private function redirectAfterSave(Request $request, string $message)
    {
        if ($groupId = $request->input('groupId')) {
            return redirect()
                ->route("admin.group.show", ["id" => $groupId])
                ->with("status", $message);
        }

        return redirect()
            ->route("admin.projects.index")
            ->with("status", $message);
    }
}
