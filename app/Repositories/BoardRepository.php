<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

class BoardRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\Board::class;
    }

    public function getBaseUrl()
    {
        return "admin.board";
    }

    public function getSearchFields()
    {
        return ['title'];
    }

    public function rules()
    {
        return [
            "board_type_id" => [],
            "board_category_id" => [],
            "isused" => [],
            "title" => ["required", "string", "max:255"],
            "board_content" => ["required"],
            "user_id" => ["required"],
        ];
    }

    public function searchBoard($search_text = '')
    {
        if ($search_text != '') {
            $boards = $this->model->select('boards.*', 'board_categories.category', "users.name")
                ->join('board_categories', 'board_categories.id', '=', 'boards.board_category_id')
                ->join("users", "boards.user_id", "=", "users.id")
                ->where("boards.title", "like", "%" . trim($search_text) . "%")
                ->get();
        } else {
            $boards = $this->model->select('boards.*', 'board_categories.category', "users.name")
                ->join('board_categories', 'board_categories.id', '=', 'boards.board_category_id')
                ->join("users", "boards.user_id", "=", "users.id")
                ->get();
        }
        return $boards;
    }
}
