<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

class BoardTypeRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\BoardType::class;
    }

    public function getBaseUrl()
    {
        return "admin.status.index";
    }

    public function getSearchFields(){
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
}