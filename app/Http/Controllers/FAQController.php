<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Board;
use App\Models\BoardCategory;
use App\Repositories\BoardCategoryRepository;
use App\Repositories\BoardRepository;

class FAQController extends Controller
{
    protected $boardRepo;
    protected $boardCategoryRepo;

    public function __construct(
        BoardRepository $boardRepo,
        BoardCategoryRepository $boardCategoryRepo
    ){
        parent::__construct($boardRepo);
        $this->boardCategoryRepo = $boardCategoryRepo;
    }

    public function create()
    {
        $board = new Board();
        $board_categories = $this->boardCategoryRepo->getCategory();
        $board_type_id = 2;
        return view('Board.FAQ.form', compact('board','board_categories', 'board_type_id'));
    }

    public function edit($id)
    {
        $board = $this->repo->find($id);
        $board_categories = $this->boardCategoryRepo->getCategory();
        $board_type_id = 2;
        return view("Board.FAQ.form", compact('board', 'board_categories', 'board_type_id'));
    }

    public function category_edit(Request $request, string $id)
    {
        $validated_data = $request->validate(["category" => ["required", "string"]]);
        $this->boardCategoryRepo->updateCate($id, $validated_data);
        return Redirect::back();
    }

    public function category_delete($id)
    {
        $this->boardCategoryRepo->deleteCate($id);
        return Redirect::back();
    }

    public function category_store(Request $request)
    {
        $validated_data = $request->validate(["category" => ["required", "string"]]);
        $category = BoardCategory::create($validated_data);
        $data['request'] = $request->input();
        $data['result'] = $category;
        return response()->json($data);
    }
}