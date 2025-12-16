<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

class BoardCommentRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\BoardComment::class;
    }

    public function getBaseUrl(){}
    public function getSearchFields(){}
    public function rules(){}

    public function getComment($id)
    {
        $comment = $this->model->select("board_comments.*", "users.name as username", "users.avatar as avatar")
            ->join("users", "users.id", "=", "board_comments.user_id")
            ->where('board_comments.board_id', "=", $id)
            ->orderBy('board_comments.id', 'desc')
            ->get();

        return $comment;
    }

    public function deleteAllCommentofNotice($id)
    {
        $this->model->select("board_comments")
            ->where("board_id", $id)
            ->delete();        
    }

    public function deleteComment($id)
    {   
        $this->model->select("board_comments.*")
            ->where("board_comments.id", '=', $id)
            ->delete();
    }

    public function updateComment($id, $comment)
    {
        $this->model->select("board_comments")
            ->where("id", $id)
            ->update(["comment" => $comment]);
    }
}