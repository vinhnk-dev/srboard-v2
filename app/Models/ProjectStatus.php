<?php

namespace App\Models;


class ProjectStatus extends BaseModel
{
    protected $fillable = [
        'project_id',
        'status_id',
        'show'
    ];
}
