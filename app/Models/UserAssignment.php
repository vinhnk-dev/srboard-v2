<?php

namespace App\Models;


class UserAssignment extends BaseModel
{
    protected $fillable = [
        'issue_id',
        'user_id',
    ];
}
