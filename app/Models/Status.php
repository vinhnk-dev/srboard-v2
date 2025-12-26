<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends BaseModel
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'status_name',
        'color',
        'is_check_due'
    ];

    public function __construct()
    {
        $this->tableHeader = config_table((new \ReflectionClass($this))->getShortName());
        $this->formatCell['color'] = function ($modal) {
            return render_color($modal->color, $modal->color);
        };
        $this->formatCell['created_at'] = function($modal){
            return render_datetime($modal->created_at);
        };
        $this->formatCell['is_check_due'] = function($modal){
            return render_yesno($modal->is_check_due);
        };
    }
}
