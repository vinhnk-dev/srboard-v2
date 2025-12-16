<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'project_name',
        'url',
        'project_code',
        'project_type',
        'active',
        'git_url',
        'description',
        'user_id',
    ];
    public function __construct()
    {
        $this->tableHeader = config_table((new \ReflectionClass($this))->getShortName());
        $this->formatCell['project_name'] = function($modal){
            return render_title($modal->project_name, '/projects/'.$modal->id.'/issues');
        };
        $this->formatCell['created_at'] = function($modal){
            return render_datetime($modal->created_at);
        };
        $this->formatCell['active'] = function ($modal) {
            $ac = $modal->active && $modal->deleted_at == "" ? "Active" : "Disable";
            return render_color($ac, "", $ac);
        };
        $this->formatCell['group_assign'] = function ($modal) {
            return render_stringList($modal->repo->groupAssigned($modal->id, true), 'w-200px');
        };
        $this->formatCell['url'] = function($modal){
            return render_url($modal->url, $modal->url);
        };
    }

    public function groupAssignments()
    {
        return $this->hasMany(GroupAssignment::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'project_id');
    }
    public function User(){
        return $this->hasMany(User::class);
    }
    public function ProjectStatus()
    {
        return $this->hasMany(ProjectStatus::class);
    }
}
