<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'group_name',
        'user_id'
    ];

    public function __construct()
    {
        $this->tableHeader = config_table((new \ReflectionClass($this))->getShortName());
        $this->formatCell['group_name'] = function ($modal) {
            return render_title($modal->group_name, '/admin/group/' . $modal->id . '/member');
        };
        $this->formatCell['users_cnt'] = function ($modal) {
            return render_countList(
                $modal->repo->countMember($modal->id),
                $modal->repo->listMember($modal->id, true)
            );
        };
        $this->formatCell['project_cnt'] = function ($modal) {
            return render_countList(
                $modal->repo->countProject($modal->id),
                $modal->repo->listProject($modal->id, true)
            );
        };
        $this->formatCell['created_at'] = function ($modal) {
            return render_datetime($modal->created_at);
        };
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
