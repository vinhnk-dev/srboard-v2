<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\IssueRepository;
use App\Repositories\UserRepository;
use App\Repositories\CommentRepository;


class IssueController extends Controller
{
    protected $issueRepository;
    protected $statusRepository;
    protected $projectRepository;

    protected $commentRepository;
    public function __construct(IssueRepository $issueRepository,
    UserRepository $userRepository, ProjectRepository $projectRepository)
    {
        parent::__construct($issueRepository, $userRepository);
        $this->projectRepository = $projectRepository;
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
        $this->context['categories'] = $this->projectRepository->getStatuses(request()->parentid);
        $this->context['form_action'] = route('issues.index', ['parentid' => request()->parentid]);
        $this->context['hasCardCategory'] = true;
        $theme = $this->userRepo->getConfig("issue_theme", Auth::user()->id);
        $this->context['page_left_tools'] = $this->repo->pageLeftTools($theme);
        if($theme == "agile"){
            $userGroupAssign = $this->repo->usersAssigned();
            $this->context['userGroupAssign'] = $userGroupAssign;
            $this->context['issues'] = $this->repo->search(null, null, false);
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
        $this->context['users'] = $this->repo->getUserAssign($issue_id);
        $this->context['reporters'] = $this->repo->getUserReporter($issue_id);
        $this->context['status_name'] = $this->repo->getStatuses(request()->parentid);
        $this->context['issue_picture'] = $this->repo->getPitures($issue_id);
        $this->context['project'] = $this->projectRepository->all();
        $this->context['form_action'] = route('issues.create', ['parentid' => request()->parentid]);
        return parent::create();
    }

    public function edit($project_id)
    {
        $issue_id = request()->id;
        $this->context['users'] = $this->repo->getUserAssign($issue_id);
        $this->context['reporters'] = $this->repo->getUserReporter($issue_id);
        $this->context['status_name'] = $this->repo->getStatuses($project_id);
        $this->context['issue_picture'] = $this->repo->getPitures($issue_id);
        $this->context['project'] = $this->projectRepository->all();
        $this->context['form_action'] = route('issues.update', ['id' => request()->id, 'parentid' => request()->parentid]);
        return parent::edit($issue_id);
    }

    public function view($projectId, $issue_id){
        $issue = $this->repo->find($issue_id);
        $issue->users = $this->repo->issueAssigned($issue_id, true);
        $issue->reporters = $this->repo->getReporter($issue_id, true);
        $issue->pictures = $this->repo->issueImages($issue_id);
        $issue->comments = $this->repo->issueComments($issue_id);

        $this->context['issue'] = $issue;
        $theme = $this->userRepo->getConfig("issue_theme", Auth::user()->id) ?? 'Grid';
        return parent::customView($this->repo->getClassName() . ".view");
    }

    public function store(Request $request){
        $validated_data = $this->validate($request, $this->repo->rules());
        if(request()->id > 0){
            $issue = $this->repo->update(request()->id, $validated_data);
        }else{
            $issue = $this->repo->create($validated_data);
        }

        if($issue) return redirect("/projects/" . $issue->project_id . "/issues/" . $issue->id . "/view");
        return redirect("/projects/" . request()->parentid . "/issues/");
    }

    public function changeStatus()
    {
        $issue = $this->repo->updateStatus(request()->id, ['status' => request()->newStatus]);
        if($issue){
            $this->repo->updateSortIndex();
            return response()->json(json_encode($issue));
        } 
        return response()->json(json_encode(['error' => 'Update failed !']));
    }

    public function delete($projectid)
    {
        if($this->repo->delete(request()->id)) return redirect("/projects/".$projectid."/issues/");
        return redirect("/projects/" .$projectid . "/issues/" . request()->id . "/view");
    }

    public function forcesDelete($projectId)
    {
        if($this->repo->forceDelete(request()->id)) return redirect("/projects/" . request()->parentid . "/issues/");
        return redirect("/projects/" .$projectId . "/issues/" . request()->id . "/view");

    }

    public function comment(Request $request){
        // dd($request->input());
        // $validated_data = $this->validate($request, $this->repo->rules());
        if(request()->id > 0){
            $comment = $this->repo->update(request()->id, $request->input());
        }else{
            $comment = $this->repo->create($request->input());
        }

        if($comment) return redirect()->back();

    }
}
