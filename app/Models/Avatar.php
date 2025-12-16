<?php

namespace App\Models;


class Avatar extends BaseModel
{
     protected $fillable = [
        'user_id',
        'avatar',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
