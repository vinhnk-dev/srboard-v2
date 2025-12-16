<?php

namespace App\Models;


class Config extends BaseModel
{
    protected $fillable = [
        'key',
        'val',
        'user_id'
    ];
}
