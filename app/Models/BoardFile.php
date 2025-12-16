<?php

namespace App\Models;


class BoardFile extends BaseModel
{
    protected $fillable = [
        'board_id',
        'file_name',
        'file_url',
    ];
}
