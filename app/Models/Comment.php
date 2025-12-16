<?php

namespace App\Models;


class Comment extends BaseModel
{
    protected $fillable = [
        'issue_id',
        'comment',
        'image',
        'user_id'
    ];
}
