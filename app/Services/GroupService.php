<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\DB;

class GroupService extends BaseService
{
    protected GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        parent::__construct($groupRepository);
        $this->groupRepository = $groupRepository;
    }

    public function getBaseUrl()
    {
        return 'admin.group';
    }

    public function getUsersOfGroup($id)
    {
        return $this->groupRepository->getUsersOfGroup($id);
    }

    public function getProjectsOfGroup($id)
    {
        return $this->groupRepository->getProjectsOfGroup($id);
    }

    public function forceDeleteRelationship($id)
    {
        return $this->groupRepository->forceDeleteRelationship($id);
    }

    public function listMember($id)
    {
        return $this->groupRepository->listMember($id);
    }

    public function listProjectFromGroup($id)
    {
        return $this->groupRepository->listProject($id);
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
             $group = $this->groupRepository->create($data);
            if (!empty($data['user_group_id'])) {
                $this->groupRepository->attachUsersToGroup(
                    $group->id,
                    $data['user_group_id']
                );
            }

            if (!empty($data['group_assign_id'])) {
                $this->groupRepository->assignProjectsToGroup(
                    $group->id,
                    $data['group_assign_id']
                );
            }

            return $group;
        });
    }

public function update($id, $data)
{
    return DB::transaction(function () use ($id, $data) {

        $group = $this->groupRepository->find($id);
        if (!$group) {
            return null;
        }

        $group = $this->groupRepository->update($id, $data);

        $this->groupRepository->updateUserGroupLinks(
            $group->id,
            $data['user_group_id'] ?? []
        );

        $this->groupRepository->updateGroupAssignmentLinks(
            $group->id,
            $data['group_assign_id'] ?? []
        );

        return $group;
    });
}


}
