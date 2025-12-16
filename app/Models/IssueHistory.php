<?php

namespace App\Models;


class IssueHistory extends BaseModel
{
    protected $fillable = [
        "title",
        "issue_description",
        "url",
        "pic_url",
        "status",
        "due_date",
        "project_id",
        "user_id",
        "issue_id"
    ];
}
