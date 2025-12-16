<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Jobs\SendMailJob;
use App\Jobs\SendHtmlMail;

class Issue extends BaseModel
{
    protected $fillable = [
        "title",
        "issue_description",
        "url",
        "pic_url",
        "status",
        "due_date",
        "project_id",
        "user_id",
        "order_by"
    ];

    public function __construct()
    {
        $this->tableHeader = config_table((new \ReflectionClass($this))->getShortName());
        $this->formatCell['project_code'] = function ($modal) {
            return render_tooltip($modal->project_code . '-' . $modal->id, $modal->project_name);
        };
        $this->formatCell['title'] = function ($modal) {
            return render_title($modal->title, '/projects/' . $modal->project_id . '/issues/' . $modal->id . '/view', 'w-400px');
        };
        $this->formatCell['status_name'] = function ($modal) {
            return render_color($modal->status_name, $modal->color, 'badge badge-sm badge-dot has-bg');
        };
        $this->formatCell['assigned'] = function ($modal) {
            return render_stringList($modal->repo->issueAssigned($modal->id, true), 'w-200px');
        };
        $this->formatCell['created_at'] = function ($modal) {
            return render_datetime($modal->created_at);
        };
        $this->formatCell['picture_url'] = function ($modal) {
            return render_pictures($modal->repo->issueImages($modal->id), $modal->id);
        };
        $this->formatCell['due_date'] = function ($modal) {
            $textClass = '';
            $toDay = date("m/d/Y");
            if ($modal->is_check_due == 1) {
                if ($toDay > $modal->due_date) {
                    $textClass = 'expired';
                } elseif ($toDay === $modal->due_date) {
                    $textClass = 'due-date';
                }
            }
            return render_date($modal->due_date, $textClass);
        };
        $this->formatCell['due_date_agile'] = function ($modal) {
            $textClass = 'alert-primary';
            $toDay = date("m/d/Y");
            if ($modal->is_check_due == 1) {
                if ($toDay > $modal->due_date) {
                    $textClass = 'alert-danger';
                } elseif ($toDay === $modal->due_date) {
                    $textClass = 'alert-warning';
                }
            }
            return render_date_custom($modal->due_date, 'd M',  $textClass);
        };

        $this->formatCell['due_date_agile_border'] = function ($modal) {
            $textClass = 'border-primary';
            $toDay = date("m/d/Y");
            if ($modal->is_check_due == 1) {
                if ($toDay > $modal->due_date) {
                    $textClass = 'border-danger';
                } elseif ($toDay === $modal->due_date) {
                    $textClass = 'border-warning';
                }
            }
            return $textClass;
        };
    }

    public function deadline()
    {
        $textClass = 'primary';
        $toDay = date("m/d/Y");
        if ($this->is_check_due == 1) {
            if ($toDay > $this->due_date) {
                $textClass = 'danger';
            } elseif ($toDay === $this->due_date) {
                $textClass = 'warning';
            }
        }
        return $textClass;
    }

    public function sendCreatedMail()
    {
        $users = array_merge($this->reporters, $this->assignments);
        foreach ($users as $user) {
            $title = 'You has been assigned as';
            if (in_array($user, $this->reporters) && in_array($user, $this->assignments)) {
                $title .= ' Reporter and Developer ';
            } else if (in_array($user, $this->reporters)) {
                $title .= ' Reporter ';
            } else if (in_array($user, $this->assignments)) {
                $title .= ' Developer ';
            }

            $issue_url = env("APP_URL") . '/projects/' . $this->project_id . '/issues/' . $this->id . '/view';
            $head = '<p style="font-size:12pt;color:#000; padding: 0; margin: 0; width:100%; "> ' . $title .
                '(<a href="' . $issue_url . '">Visit issue</a>)</p>';
            $head .= '<p style="font-size:12pt;color:#000; padding: 0; margin: 0; width:100%; "> Issue code: ' . $this->project->project_code . '-' . $this->id . '</p>';
            $head .= '<p style="font-size:12pt;color:#000; padding: 0; margin: 0; width:100%; "> Issue title: ' . $this->title . '</p>';
            send_mail(3, "[" . env("APP_NAME") . "] New issue created: " . $this->title, $head);
        }
    }

    public function sendUpdatedMail($content)
    {
        $users = array_merge($this->reporters, $this->assignments);
        foreach ($users as $user) {
            $title = 'You has been assigned as';
            if (in_array($user, $this->reporters) && in_array($user, $this->assignments)) {
                $title .= ' Reporter and Developer ';
            } else if (in_array($user, $this->reporters)) {
                $title .= ' Reporter ';
            } else if (in_array($user, $this->assignments)) {
                $title .= ' Developer ';
            }

            $issue_url = env("APP_URL") . '/projects/' . $this->project_id . '/issues/' . $this->id . '/view';
            $head = '<p style="font-size:12pt;color:#333; padding: 0; margin: 0; width:100%; font-weight: bold;"> ' . $title .
                '(<a href="' . $issue_url . '">Visit issue</a>)</p>';
            send_mail(3, "[" . env("APP_NAME") . "] Issue updated: " . $this->title, $head . $content);
        }
    }

    public function sendCommentMail($comment)
    {
        $users = array_merge($this->reporters, $this->assignments);
        foreach ($users as $user) {
            $title = 'You has been assigned as';
            if (in_array($user, $this->reporters) && in_array($user, $this->assignments)) {
                $title .= ' Reporter and Developer ';
            } else if (in_array($user, $this->reporters)) {
                $title .= ' Reporter ';
            } else if (in_array($user, $this->assignments)) {
                $title .= ' Developer ';
            }

            $issue_url = env("APP_URL") . '/projects/' . $this->project_id . '/issues/' . $this->id . '/view';
            $head = '<p style="font-size:12pt;color:#000; padding: 0; margin: 0; width:100%; "> ' . $title .
                '(<a href="' . $issue_url . '">Visit issue</a>)</p>';
            $head .= '<p style="font-size:12pt;color:#000; padding: 0; margin: 0; width:100%; "> Issue code: ' . $this->project->project_code . '-' . $this->id . '</p>';
            $head .= '<p style="font-size:12pt;color:#000; padding: 0; margin: 0; width:100%; "> Issue title: ' . $this->title . '</p>';
            send_mail(3, "[" . env("APP_NAME") . "] New issue created: " . $this->title, $head);
        }
    }

    public function compair($old_version)
    {
        $compairs = [
            'title' => "Title: ",
            "url" => 'URL: ',
            "status_name" => 'Status: ',
            "due_date" => 'Due date: ',
            "project_name" => 'Project: ',
            "reporters_toString" => 'Reporters: ',
            "assignments_toString" => 'Assignments: ',
            "issue_description" => 'Issue description was changed</b>',
        ];
        $style = '<p style="font-size:12pt;color:#333; padding: 0; margin: 0; width:100%"> <span style="color: #333 !important; font-weight: bold;">';
        $contents = [];
        foreach ($compairs as $key => $value) {
            if ($this->$key != $old_version->$key) {
                if ($key == "issue_description")
                    $contents[] = $style . $value . '</span>';
                else
                    $contents[] = $style . $value . '</span> <i style="color: #8c8c8c !important"> changed from </i>' . $old_version->$key . '<i style="color: #8c8c8c !important"> to </i>' . $this->$key . '</p>';
            }
        }
        return implode('', $contents);
    }

    public function Project()
    {
        return $this->belongsTo(Project::class);
    }
    public function IssuePicture()
    {
        return $this->hasMany(IssuePicture::class);
    }
    public function Comment()
    {
        return $this->hasMany(Comment::class);
    }
}
