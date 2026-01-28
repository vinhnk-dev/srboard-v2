<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\IssueRepository;
use App\Repositories\UserRepository;
use App\Repositories\CommentRepository;
use App\Services\ProjectService;
use App\Services\IssueService;
use App\Http\Requests\IssueRequest;
use App\Http\Requests\CommentRequest;


class IssueController extends Controller
{
    protected $issueRepository;
    protected $statusRepository;
    protected $projectRepository;

    protected $projectService;
    protected $issueService;

    protected $commentRepository;
    public function __construct(IssueRepository $issueRepository,
    UserRepository $userRepository, ProjectRepository $projectRepository,ProjectService $projectService, IssueService $issueService)
    {
        parent::__construct($issueRepository, $userRepository);
        $this->projectRepository = $projectRepository;
        $this->projectService = $projectService;
        $this->issueService = $issueService;
    }

    public function index()
    {
        $this->context['tableview_config'] = [
            'actions' => [],
            'tools' => [
                "add" => [],
                "excel" => []
            ]
        ];
        if(isset(request()->theme)) $this->userRepo->setConfig("issue_theme", Auth::user()->id, request()->theme);
        $this->context['categories'] = $this->projectService->getStatuses(request()->parentid);
        $this->context['form_action'] = route('issues.index', ['parentid' => request()->parentid]);
        $this->context['hasCardCategory'] = true;
        $theme = $this->userRepo->getConfig("issue_theme", Auth::user()->id);
        $this->context['page_left_tools'] = $this->issueService->pageLeftTools($theme);
        if($theme == "agile"){
            $userGroupAssign = $this->issueService->getAllUsersAssigned();
            $this->context['userGroupAssign'] = $userGroupAssign;
            $this->context['issues'] = $this->issueService->search(null, null, false);
            return parent::customView("Issue.agile");
        }
        return parent::index();
    }

    public function trash()
    {
        $this->context['tableview_config'] = [
            'actions' => [],
            'tools' => ["add" => []],
        ];
        return parent::trash();
    }

    public function create()
    {
        $issue_id = 0;
        $this->context['users'] = $this->issueService->getUserAssign($issue_id);
        $this->context['reporters'] = $this->issueService->getUserReporter($issue_id);
        $this->context['status_name'] = $this->issueService->getStatuses(request()->parentid);
        $this->context['issue_picture'] = $this->issueService->getImages($issue_id);
        $this->context['project'] = $this->projectService->getAll();
        $this->context['form_action'] = route('issues.create', ['parentid' => request()->parentid]);
        return parent::create();
    }

    public function edit($project_id)
    {
        $issue_id = request()->id;
        $this->context['users'] = $this->issueService->getUserAssign($issue_id);
        $this->context['reporters'] = $this->issueService->getUserReporter($issue_id);
        $this->context['status_name'] = $this->issueService->getStatuses($project_id);
        $this->context['issue_picture'] = $this->issueService->getImages($issue_id);
        $this->context['project'] = $this->projectService->getAll();
        $this->context['form_action'] = route('issues.update', ['id' => request()->id, 'parentid' => request()->parentid]);
        return parent::edit($issue_id);
    }

    public function view($projectId, $issue_id){
        $issue = $this->issueService->find($issue_id);
        $issue->users = $this->issueService->getIssueAssigned($issue_id, true);
        $issue->reporters = $this->issueService->getReporter($issue_id, true);
        $issue->pictures = $this->issueService->getImages($issue_id);
        $issue->comments = $this->issueService->getIssueComments($issue_id);

        $this->context['issue'] = $issue;
        $theme = $this->userRepo->getConfig("issue_theme", Auth::user()->id) ?? 'Grid';
        return parent::customView($this->repo->getClassName() . ".view");
    }

    public function store(IssueRequest $request){
        $validatedData = $request->validated();
        $issue = $this->issueService->create($validatedData);

        if($issue) return redirect("/projects/" . $issue->project_id . "/issues/" . $issue->id . "/view");
        return redirect("/projects/" . request()->parentid . "/issues/");
    }

    public function update($parentId, $id, IssueRequest $request)
    {
        $validatedData = $request->validated();
        $issue = $this->issueService->update($id, $validatedData);

        if($issue) return redirect("/projects/" . $issue->project_id . "/issues/" . $issue->id . "/view");
        return redirect("/projects/" . request()->parentid . "/issues/");
    }

    public function changeStatus()
    {
        $issue = $this->issueService->updateStatus(request()->id, ['status' => request()->newStatus]);
        if($issue){
            $this->issueService->updateSortIndex();
            return response()->json(json_encode($issue));
        } 
        return response()->json(json_encode(['error' => 'Update failed !']));
    }

    public function delete($projectid)
    {
        if($this->issueService->delete(request()->id)) return redirect("/projects/".$projectid."/issues/");
        return redirect("/projects/" .$projectid . "/issues/" . request()->id . "/view");
    }

    public function forcesDelete($projectId)
    {
        if($this->issueService->forceDelete(request()->id)) return redirect("/projects/" . request()->parentid . "/issues/");
        return redirect("/projects/" .$projectId . "/issues/" . request()->id . "/view");

    }

    public function comment(CommentRequest $request){
        $validatedData = $request->validated();
        
        $this->issueService->comment($validatedData);

        return redirect()->back();

    }
}
