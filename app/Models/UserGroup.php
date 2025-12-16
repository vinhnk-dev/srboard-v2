<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;


class UserGroup extends BaseModel
{
    protected $fillable = [
        'group_id',
        'user_id',    
    ];
  
}
