<?php

namespace App\Models;


class BoardComment extends BaseModel
{
    protected $fillable = [
        'user_id',
        'comment',
        'board_id'
    ];
}
