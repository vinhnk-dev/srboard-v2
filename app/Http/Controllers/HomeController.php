<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendHtmlMail;
use App\Repositories\BoardRepository;
use App\Repositories\IssueRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use App\View\Components\TableView;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $boardRepo;
    protected $projectRepository;

    public function __construct(
        UserRepository $userRepository,
        BoardRepository $boardRepo,
        ProjectRepository $projectRepository,
        IssueRepository $issueRepository
    ) {
        parent::__construct($issueRepository, $userRepository);
        $this->projectRepository = $projectRepository;
        $this->boardRepo = $boardRepo;
    }

    public function index()
    {
        $userid = Auth::user()->id;
        $projects = $this->userRepo->myProjects($userid);
        foreach ($projects as $project) $this->projectRepository->getShortInfo($project);

        $maintenances = $this->userRepo->myProjects($userid, 'Maintenance');
        foreach ($maintenances as $project) $this->projectRepository->getShortInfo($project);

        $this->context['boards'] = $this->boardRepo->all();
        $this->context['projects'] = $projects;
        $this->context['maintenances'] = $maintenances;
        return parent::customView("index");
    }

    public function mytask()
    {
        $boards = $this->boardRepo->searchBoard();

        $this->context['boards'] = $boards;
        $this->context['list'] = $this->userRepo->myTasks(Auth::user()->id);
        $this->context = TableView::render_normal($this->repo, $this->context, [
            'actions' => [],
            'filters' => [],
            'tools' => [],
            'paging' => false,
        ]);
        return parent::customView("mytask");
    }

    public function sendmail()
    {
        $user = $this->userRepo->find(request()->get('email'));
        if($user){
            $emailJob = (new SendHtmlMail($user->email, request()->get('title'), request()->get('content')))
            ->delay(Carbon::now()->addSeconds(5));
            dispatch($emailJob);
            return true;
        }
        return false;
    }
}
