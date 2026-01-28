<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\IssueRepository;
use App\Repositories\UserRepository;
use App\Repositories\CommentRepository;
use App\Repositories\ProjectRepository;
use App\Services\IssueMailService;

class IssueService extends BaseService
{
    protected $issueRepository;
    protected $statusRepository;
    protected $projectRepository;
    protected $userRepository;
    protected $issueMailService;

    protected $commentRepository;
    public function __construct(IssueRepository $issueRepository,
    UserRepository $userRepository, ProjectRepository $projectRepository, 
    IssueMailService $issueMailService)
    {
        parent::__construct($issueRepository, $userRepository);
        $this->projectRepository = $projectRepository;
        $this->issueRepository = $issueRepository;
        $this->userRepository = $userRepository;
        $this->issueMailService = $issueMailService;
    }

    public function getBaseUrl()
    {
        return 'admin.issue';
    }
    //Render path
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
    public function getAllUsersAssigned()
    {
        return $this->issueRepository->usersAssigned();
    }

    public function getUserAssign($issueId)
    {
        $selectedUserIds = $this->issueRepository->getUserAssign($issueId);

        $users = $this->userRepository->all();

        return $this->markSelectedUsers($users, $selectedUserIds);
    }

    public function getUserReporter($issueId)
    {
        $selectedUserIds = $this->issueRepository->getUserReporter($issueId);

        $users = $this->userRepository->all();

        return $this->markSelectedUsers($users, $selectedUserIds);
    }

    private function markSelectedUsers($users, array $selectedUserIds)
    {
        foreach ($users as $user) {
            $user->active = in_array($user->id, $selectedUserIds)
                ? 'selected'
                : '';
        }
        return $users;
    }

    public function getStatuses($projectId)
    {
        return $this->issueRepository->getStatuses($projectId);
    }

    public function getImages($id)
    {
        return $this->issueRepository->getPitures($id);
    }

    public function getIssueAssigned($id)
    {
        $assignedUsers = $this->issueRepository->issueAssigned($id, true);
        return $assignedUsers;
    }

    public function getReporter($id)
    {
        $userReporters = $this->issueRepository->getReporter($id, true);
        return $userReporters;

    }

    public function getIssueComments($id)
    {
        return $this->issueRepository->issueComments($id);
    }

    //Request Path

    public function create($data)
    {
        $issue = DB::transaction(function () use ($data) {
            $keepUrls = $data['pic_url'] ?? [];
            $newFiles = $data['picture_url'] ?? [];

            $issueData = $this->prepareIssueData($data);

            $issue = $this->issueRepository->create($issueData);

            $this->handleAssignments($issue->id, $data);
            $this->handleReporters($issue->id, $data);

        
            if (!empty($keepUrls) || !empty($newFiles)) {
                $this->issueRepository->updatePictures($issue->id, $keepUrls, $newFiles);
            }

            $this->issueRepository->load_full_display_detail($issue);
            return $issue;
        });

        $this->issueMailService->sendCreatedMail($issue->id);

        return $issue;
    }

    public function update(int $issueId, array $data)
    {
        $contents = null;
        $updateIssue = null;

        $issue = DB::transaction(function () use ($issueId, $data, &$contents, &$updateIssue) {
            $keepUrls = $data['pic_url'] ?? [];
            $newFiles = $data['picture_url'] ?? [];
            $data = $this->sanitize($data);
            $oldIssue = $this->issueRepository->find($issueId);

           $newIssue = $this->issueRepository->update($issueId, $data);
           if($newIssue)
            {
                $updateIssue = $this->issueRepository->find($issueId);

                $this->handleAssignments($updateIssue->id, $data);
                $this->handleReporters($updateIssue->id, $data);
                if (!empty($keepUrls) || !empty($newFiles)) {
                    $this->issueRepository->updatePictures($updateIssue->id, $keepUrls, $newFiles);
                }
                $this->issueRepository->load_full_display_detail($updateIssue);
                $contents = $this->compare($oldIssue, $updateIssue);
                $this->issueRepository->createUpdateComment($issueId, $contents, Auth::id());
            }

            return $newIssue;
        });
        if ($contents) {
            $this->issueMailService->sendUpdatedMail($issueId, $contents);
        }

        return $updateIssue;
    }

    public function updateStatus($issueId, $newStatus)
    {
        $contents = null;
        $updateIssue = null;
        $oldIssue = null;

        DB::transaction(function () use ($issueId, $newStatus, &$contents, &$updateIssue,&$oldIssue) {
            $oldIssue = $this->issueRepository
                ->getIssueAndStatus($issueId);

            $this->issueRepository->load_full_display_detail($oldIssue);

            // 2. Update status
            $updated = $this->issueRepository->update($issueId,$newStatus);

            if ($updated) {
                $updateIssue = $this->issueRepository->getIssueAndStatus($issueId);
                $this->issueRepository->load_full_display_detail($updateIssue);

                $contents = $this->compare($oldIssue, $updateIssue);
                $this->issueRepository->createUpdateComment(
                    $issueId,
                    $contents,
                    Auth::id()
                );
            }
        });

        if ($contents) {
            $this->issueMailService->sendUpdatedMail($issueId, $contents);
        }
        if ($updateIssue) {
            $this->issueRepository->enrichIssueForResponse(
                $updateIssue,
                $oldIssue ?? null
            );
        }

        return $updateIssue;
    }

    public function comment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $commentData = $this->prepareIssueData($data);
            return $this->issueRepository->comment($commentData);
        });
    }


    public function updateSortIndex()
    {
        $positions = request()->position;
        return $this->issueRepository->updateSortIndex($positions);
    }

    public function forceDelete($id)
    {
        return $this->issueRepository->forceDelete($id);
    }

    private function reHydrateState($id)
    {
        $issue = $this->issueRepository->getIssueAndStatus($id);

        if ($issue) {
            $this->issueRepository->load_full_display_detail($issue);
        }

        return $issue;
    }


    private function prepareIssueData(array $data): array
    {
        unset($data['user_id']);

        $data['user_id'] = Auth::id();

        return $data;

    }

    private function handleAssignments(int $issueId, array $data): void
    {
        if (!empty($data['user_assign'])) {
            $this->issueRepository->updateAssigned($issueId, $data['user_assign']);
        }
    }

    private function handleReporters(int $issueId, array $data): void
    {
        if (!empty($data['report_assign'])) {
            $this->issueRepository->updateReporter($issueId, $data['report_assign']);
        }
    }

    private function handlePictures(int $issueId, array $data): void
    {
        $hasOldPictures = !empty($data['pic_url']);
        $hasNewPictures = !empty($data['picture_url']);
        
        if ($hasOldPictures || $hasNewPictures) {
            $this->issueRepository->updatePictures(
                $issueId,
                $data['pic_url'] ?? [],
                $data['picture_url'] ?? []
            );
        }
    }
    //Data Processing
    private function sanitize(array $data): array
    {
        unset($data['pic_url'],
        $data['picture_url'],
        $data['user_id']);

        $user = Auth::user();

        if (!$user || !$user->hasRole('Admin')) {
            unset($data['project_id']);
        }


        return $data;
    }

    //Render For Mail
    private function compare($before, $after)
    {
        $compare = [
            'title' => "Title: ",
            "url" => 'URL: ',
            "status_name" => 'Status: ',
            "due_date" => 'Due date: ',
            "project_name" => 'Project: ',
            "reporters_toString" => 'Reporters: ',
            "assignments_toString" => 'Assignments: ',
            "issue_description" => 'Issue description was changed</b>',
        ];
        $style = '<p style="font-size:12pt;color:#333; padding: 0; margin: 0; width:100%"> <span style="color: #333 !important; font-weight: bold;">';
        $contents = [];
        foreach ($compare as $key => $value) {
            if ($before->$key != $after->$key) {
                if ($key == "issue_description")
                    $contents[] = $style . $value . '</span>';
                else
                    $contents[] = $style . $value . '</span> <i style="color: #8c8c8c !important"> changed from </i>' . $before->$key . '<i style="color: #8c8c8c !important"> to </i>' . $after->$key . '</p>';
            }
        }
        return implode('', $contents);
    }





}
