<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;

class StatusRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\Status::class;
    }

    public function getBaseUrl()
    {
        return "admin.status";
    }

    public function getSearchFields(){
        return ["status_name"];
    }

    public function rules()
    {
        return [
            "status_name" => ["required", "string", "max:255"],
            "color" => ["required"],
            "is_check_due" => []
        ];
    }
}
