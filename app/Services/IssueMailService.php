<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Repositories\IssueRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class IssueMailService
{
    protected $userRepository;
    protected $issueRepository;

    public function __construct(IssueRepository $issueRepository,UserRepository $userRepository)
    {
        $this->issueRepository = $issueRepository;
        $this->userRepository = $userRepository;
    }
    public function sendCreatedMail($issueId): void
    {
        $issue = $this->issueRepository->find($issueId);
        $issue->loadMissing('project:id,project_code');
        $assigners = $this->issueRepository->getUserAssign($issueId);
        $reporters = $this->issueRepository->getUserReporter($issueId);

        $users = array_merge($reporters, $assigners);
        
        foreach ($users as $user_id) {
            $title = 'You have been assigned as';

            $userMail = $this->userRepository->getUserEmail($user_id);

            if (in_array($user_id, $reporters) && in_array($user_id, $assigners)) {
                $title .= ' Reporter and Developer ';
            } elseif (in_array($user_id, $reporters)) {
                $title .= ' Reporter ';
            } elseif (in_array($user_id, $assigners)) {
                $title .= ' Developer ';
            }

            $issue_url = env('APP_URL') . '/projects/' . $issue->project_id . '/issues/' . $issue->id . '/view';

            $head  = '<p style="font-size:12pt;color:#000; padding:0;margin:0;width:100%;">';
            $head .= $title . '(<a href="' . $issue_url . '">Visit issue</a>)</p>';
            $head .= '<p style="font-size:12pt;color:#000; padding:0;margin:0;width:100%;">';
            $head .= ' Issue code: ' . $issue->project_code . '-' . $issue->id . '</p>';
            $head .= '<p style="font-size:12pt;color:#000; padding:0;margin:0;width:100%;">';
            $head .= ' Issue title: ' . $issue->title . '</p>';
            try {
                SendMailJob::dispatch(
                    $userMail,
                    '[' . env('APP_NAME') . '] New issue created: ' . $issue->title,
                    $head
                );
            } catch (\Throwable $e) {
                Log::error('Dispatch SendMailJob failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    public function sendUpdatedMail($issueId, string $content): void
    {
        $issue = $this->issueRepository->find($issueId);
        $issue->loadMissing('project:id,project_code');
        $assigners = $this->issueRepository->getUserAssign($issueId);
        $reporters = $this->issueRepository->getUserReporter($issueId);

        $users = array_merge($reporters, $assigners);

        foreach ($users as $user_id) {
            $userMail = $this->userRepository->getUserEmail($user_id);
            
            $title = 'You has been assigned as';

            if (in_array($user_id, $reporters) && in_array($user_id, $assigners)) {
                $title .= ' Reporter and Developer ';
            } elseif (in_array($user_id, $reporters)) {
                $title .= ' Reporter ';
            } elseif (in_array($user_id, $assigners)) {
                $title .= ' Developer ';
            }

            $issue_url = env('APP_URL') . '/projects/' . $issue->project_id . '/issues/' . $issue->id . '/view';

            $head  = '<p style="font-size:12pt;color:#333; padding:0;margin:0;width:100%;font-weight:bold;">';
            $head .= $content;
            $head .= $title . '(<a href="' . $issue_url . '">Visit issue</a>)</p>';

            try {
                SendMailJob::dispatch(
                    $userMail,
                    '[' . env('APP_NAME') . '] Your Issue have been update: ' . $issue->title,
                    $head
                );
            } catch (\Throwable $e) {
                Log::error('Dispatch SendMailJob failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

}
