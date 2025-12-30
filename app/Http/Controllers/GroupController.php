<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use App\Repositories\ProjectRepository;
use App\Services\GroupService;
use App\Http\Requests\GroupRequest;

class GroupController extends Controller
{
    protected $userRepository;
    protected $projectRepository;
    protected $groupService;

    public function __construct(
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        GroupService $groupService
    ) {
        parent::__construct($groupRepository, $userRepository);

        $this->groupService = $groupService;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
    }

    public function create()
    {
        $this->context['user'] = $this->userRepository->all();
        $this->context['project'] = $this->projectRepository->all();
        return parent::create();
    }

    public function edit($id)
    {
        $this->context['user'] = $this->groupService->getUsersOfGroup($id);
        $this->context['project'] = $this->groupService->getProjectsOfGroup($id);

        return parent::edit($id);
    }

    public function delete($id)
    {
        return parent::delete($id);
    }

    public function forcesDelete($id)
    {
        $this->context['group'] = $this->groupService->forceDeleteRelationship($id);
        return parent::forcesDelete($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupRequest $request)
    {
        $inGroup =  request()->get('inGroup');
        $validatedData = $request->validated();

        $group = $this->groupService->create($validatedData);

        if ($inGroup) {
            return redirect()->route("admin.group.show", $request->input('id'));
        } else {
            return redirect()->route("admin.group.index");
        }
    }

    public function update($id, GroupRequest $request)
    {
        $inGroup =  request()->get('inGroup');
        
        $validatedData = $request->validated();

        $group = $this->groupService->update($id, $validatedData);
         if ($inGroup) {
            return redirect()->route("admin.group.show", $id);
        } else {
            return redirect()->route("admin.group.index");
        }
    }

    public function show($groupId)
    {
        $this->context['group'] = $this->groupService->find($groupId);
        $this->context['users'] = $this->groupService->listMember($groupId);
        $this->context['project'] = $this->groupService->listProjectFromGroup($groupId);
        return parent::customView("Group.view");
    }
}
