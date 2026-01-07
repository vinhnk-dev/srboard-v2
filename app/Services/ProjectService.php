<?php

namespace App\Services;

use App\Repositories\ProjectRepository;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProjectService extends BaseService
{
    protected ProjectRepository $projectRepository;
    protected GroupRepository $groupRepository;

    public function __construct(ProjectRepository $projectRepository, GroupRepository $groupRepository)
    {
        parent::__construct($projectRepository);
        $this->projectRepository = $projectRepository;
        $this->groupRepository = $groupRepository;
    }

    public function getBaseUrl()
    {
        return 'admin.project';
    }

    public function getGroupAssign($id)
    {
        $assignedGroupIds = $this->projectRepository->getAssignedGroupIds($id);
        $allGroups = $this->groupRepository->all();
        
        return $this->markAssignedGroups($allGroups, $assignedGroupIds);
    }

    private function markAssignedGroups($groups, $assignedGroupIds)
    {
        return $groups->map(function ($group) use ($assignedGroupIds) {
            $group->active = in_array($group->id, $assignedGroupIds) ? 'selected' : '';
            return $group;
        });
    }

    public function getProjectStatus($id)
    {
         $projectStatuses = $this->projectRepository->getProjectStatusesByProjectId($id);

        $statuses = $this->projectRepository->getAllStatuses();

        $statusMap = $projectStatuses->keyBy('status_id');

        foreach ($statuses as $status) {
            $pjStatus = $statusMap->get($status->id);

            $status->active = $pjStatus ? 'selected' : '';
            $status->check  = ($pjStatus && $pjStatus->show) ? 'checked' : '';
        }

        return $statuses;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data){
            $data['user_id'] =  Auth::id();
            $project = $this->createNewProject($data);
                if (!empty($data['group_assignment_id'])) {
                $this->projectRepository->assignGroups(
                    $project->id,
                    $data['group_assignment_id']
                );
            }

            if (!empty($data['status_id'])) {
                $this->projectRepository->addStatuses(
                    $project->id,
                    $data['status_id']
                );
            }

            return $project;
        });
        
    }

    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data){
            $project = $this->updateProject($id, $data);

            if (!$project) {
                return null;
            }
            if(!empty($data['group_assignment_id']))
            {
                $this->projectRepository->updateGroupAssignments($project->id, $data["group_assignment_id"]);
            }
            if (!empty($data['status_id']))
            {
                $this->projectRepository->updateProjectStatuses($project->id, $data["status_id"], $data['show'] ?? []);
            }

            return $project;
        });
    }

    private function createNewProject(array $data)
    {
        return $this->projectRepository->create($data);
    }
    private function updateProject($id, $data)
    {
        return $this->projectRepository->update($id,$data);
    }

    public function forcesDeleteRelationship($id)
    {
        return $this->projectRepository->forcesDeleteRelationship($id);
    }

    public function isProjectCodeExists(string $code, ?int $exceptId = null): bool
    {
        return $this->projectRepo->existsByCode($code, $exceptId);
    }

}
