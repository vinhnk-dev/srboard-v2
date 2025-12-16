<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use App\Repositories\ProjectRepository;

class GroupController extends Controller
{
    protected $userRepository;
    protected $projectRepository;

    public function __construct(
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        ProjectRepository $projectRepository
    ) {
        parent::__construct($groupRepository, $userRepository);

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
        $this->context['user'] = $this->repo->getUsersOfGroup($id);
        $this->context['project'] = $this->repo->getProjectsOfGroup($id);

        return parent::edit($id);
    }

    public function delete($id)
    {
        return parent::delete($id);
    }

    public function forcesDelete($id)
    {
        $this->context['group'] = $this->repo->forceDeleteRelationship($id);
        return parent::forcesDelete($id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inGroup =  request()->get('inGroup');

        $data = $request->validate([
            "group_name" => ["required"],
            "user_id" => [],
            "user_group_id" => [],
            "group_assign_id" => [],
        ]);

        // if have id -> update
        if ($request->input('id')) {
            $group = $this->repo->update($request->input('id'), $data);
        } else {
            // create new
            $group = $this->repo->store($data);
        }
        if ($inGroup) {
            return redirect()->route("admin.group.show", $request->input('id'));
        } else {
            return redirect()->route("admin.group.index");
        }
    }

    public function show($groupId)
    {
        $this->context['group'] = $this->repo->find($groupId);
        $this->context['users'] = $this->repo->listMember($groupId);
        $this->context['project'] = $this->repo->listProject($groupId);
        return parent::customView("Group.view");
    }
}
