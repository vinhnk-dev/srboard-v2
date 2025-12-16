<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["name", "username", "email", "password", "active"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "email_verified_at" => "datetime",
        "password" => "hashed",
    ];

    protected $formatCell = [];
    protected $tableHeader = [];
    protected $repo = null;

    public function __construct()
    {
        $this->tableHeader = config_table((new \ReflectionClass($this))->getShortName());
        $this->formatCell['username'] = function ($modal) {
            $avatar = '<a class="user-card" href="' . route('admin.users.edit', ['id' => $modal->id]) . '">
                <div class="user-avatar">';

            if (!empty($modal->avatar)) {
                $avatar .= '<img src="/' . $modal->avatar . '" class="user-avatar-sm avatar-sm">';
            } else {
                $avatar .= '<img src="' . asset('./dashlite/images/Avt.jpeg') . '" class="user-avatar-sm">';
            }

            $avatar .= '</div>
                <div class="user-info"><span class="tb-lead">' . $modal->username . '</span></div>
            </a>';

            return $avatar;
        };

        $this->formatCell['group_user'] = function ($modal) {
            return render_stringList($modal->repo->myGroups($modal->id, true), 'w-200px');
        };
        $this->formatCell['active'] = function ($modal) {
            $ac = $modal->active && $modal->deleted_at == "" ? "Active" : "Disable";
            return render_color($ac, "", $ac);
        };
        $this->formatCell['roles_name'] = function ($modal) {
            return User::find($modal->id)->getRoleNames()->implode(', ');
        };
    }

    public function getCellValue($field)
    {
        if (isset($this->formatCell[$field])) {
            $this->repo = new ('\\App\\Repositories\\' . (new \ReflectionClass($this))->getShortName() . 'Repository')();
            $temp = $this->formatCell[$field];
            return $temp($this);
        }
        return $this->$field;
    }

    public function getTableHeader()
    {
        return $this->tableHeader;
    }

    public function Comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function Project()
    {
        return $this->hasMany(Project::class);
    }
    public function userGroups()
    {
        return $this->hasMany(UserGroup::class, 'user_id');
    }
    public function userAssignments()
    {
        return $this->hasMany(UserAssignment::class, 'user_id');
    }
    public function Group()
    {
        return $this->belongsToMany(Group::class);
    }
    public static function sendMail($id, $title = "", $content = "")
    {
        $user = User::findOrFail($id);
        $email = $user->email;
        if ($email) {
            Mail::send([], [], function (Message $message) use (
                $email,
                $title,
                $content
            ) {
                $message->to($email);
                $message->subject($title);
                $message->text($content . "\n\n" . "~ Best regards ~");
            });
        }
    }
    public static function getAvatar()
    {
        $userid = Auth::user()->id;
        $user = User::find($userid);
        if ($user) {
            $avatar = Avatar::where('user_id', '=', $userid)
                ->first();
            if ($avatar) {
                $user->avatar = $avatar->avatar;
            }
        }
    }
    public static function getGroupIds()
    {
        $user = Auth::user();
        if ($user) {
            $user_id = $user->id;
            $groupIdsCollection = UserGroup::where('user_groups.user_id', $user_id)
                ->select("user_groups.group_id")
                ->get();
            return $groupIdsCollection->pluck('group_id')->toArray();
        }
        return [];
    }
}
