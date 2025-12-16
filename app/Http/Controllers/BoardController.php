<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Board;
use App\Repositories\BoardRepository;
use App\Repositories\BoardCategoryRepository;
use App\Repositories\BoardCommentRepository;
use App\Repositories\BoardFileRepository;
use App\Repositories\BoardTypeRepository;

class BoardController extends Controller
{
    protected $boardCmtRepo;
    protected $boardCategoryRepo;
    protected $boardFileRepo;
    protected $boardTypeRepo;

    public function __construct(
        BoardRepository $boardRepo,
        BoardCommentRepository $boardCmtRepo,
        BoardCategoryRepository $boardCategoryRepo,
        BoardFileRepository $boardFileRepo,
        BoardTypeRepository $boardTypeRepo
    ) {
        parent::__construct($boardRepo);
        $this->boardCmtRepo = $boardCmtRepo;
        $this->boardCategoryRepo = $boardCategoryRepo;
        $this->boardFileRepo = $boardFileRepo;
        $this->boardTypeRepo = $boardTypeRepo;
    }

    public function index()
    {
        $this->context['list'] = $this->repo->searchBoard($request->search_text);
        $this->context['cate'] = $this->boardCategoryRepo->getAll();
        return parent::index($request);
    }

    public function create()
    {
        $board = new Board();
        $board_file = [];
        return view('Board.Notice.form', compact('board', 'board_file'));
    }

    public function edit($id)
    {
        $board = $this->repo->find($id);
        $board_file = $this->boardFileRepo->getBoardFile($id);
        return view("Board.Notice.form", compact('board', 'board_file'));
    }

    public function delete($id)
    {
        $board = $this->repo->find($id);

        $this->boardCmtRepo->deleteAllCommentofNotice($id);
        $this->repo->delete($id);

        if ($board->board_type_id == 2) {
            return redirect()->route("admin.board.index", ['faq']);
        }
        return redirect()->route("admin.board.index");
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate([
            "title" => ["required", "string", "max:255"],
            "isused" => [],
            "board_type_id" => [],
            "board_category_id" => [],
            "board_content" => ["required"],
            "user_id" => ["required"],
        ]);

        $board = $this->repo->find($request->input('id'));

        if ($board) {
            //update notice
            $board = $this->repo->update($request->input('id'), $validated_data);
            $this->boardFileRepo->deleteBoardFile($board->id);
            $upload_files = $request->file_url;
            if ($upload_files) {
                $path = "images/board/";

                if (is_string($upload_files)) $upload_files = [$upload_files];

                foreach ($upload_files as $upload_file) {
                    $new_name = $this->boardFileRepo->saveFile($upload_file, $path);
                    $idata = [
                        "board_id" => $board->id,
                        "file_name" => $new_name,
                        "file_url" => $path . $new_name,
                    ];
                    $this->repo->update($request->input('id'), $idata);
                }
            }
        } else {
            //create notice
            $board = $this->repo->create($validated_data);
            $upload_files = $request->file_url;
            if ($upload_files) {
                $path = "images/board/";

                if (is_string($upload_files)) $upload_files = [$upload_files];

                foreach ($upload_files as $upload_file) {
                    $new_name = $this->boardFileRepo->saveFile($upload_file, $path);
                    $idata = [
                        "board_id" => $board->id,
                        "file_name" => $new_name,
                        "file_url" => $path . $new_name,
                    ];
                    $this->repo->create($idata);
                }
            }
        }

        if ($request->board_type_id == 2) {
            return redirect()->route("admin.board.index", ['faq']);
        }
        return redirect()->route("admin.board.index");
    }

    public function view($id)
    {
        $board = $this->repo->find($id);

        $board_file = $this->boardFileRepo->getBoardFile($id);

        $writer = DB::table('users')
            ->join('boards', "boards.user_id", "users.id")
            ->where("boards.id", "=", $id)
            ->first();

        $comment = $this->boardCmtRepo->getComment($id);

        $comment_cnt = $comment->count();
        return view("Board.Notice.view", compact('board', 'comment', 'board_file', "writer", "comment_cnt"));
    }

    public function comment(Request $request)
    {
        $validated_data = $request->validate([
            "board_id" => ["required"],
            "comment" => ["required", "string"],
            "user_id" => ["required"],
        ]);
        $this->boardCmtRepo->create($validated_data);
        return Redirect::back();
    }

    public function comment_edit(Request $request)
    {   
        $this->boardCmtRepo->updateComment($request->comment_id, $request->input("comment"));
        return Redirect::back();
    }

    public function comment_delete($boardId, $cmt_id)
    {
        $this->boardCmtRepo->deleteComment($cmt_id);
        return Redirect::back();
    }
}
