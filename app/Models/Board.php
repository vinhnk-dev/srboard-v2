<?php

namespace App\Models;


class Board extends BaseModel
{
    protected $fillable = [
        'board_id',
        'board_type_id',
        'board_category_id',
        'isused',
        'title',
        'board_content',
        'user_id'
    ];
    public function BoardFile()
    {
        return $this->hasMany(BoardFile::class);
    }
    public function boardType() {
        return $this->belongsTo(BoardType::class, 'board_type_id');
    }
}
