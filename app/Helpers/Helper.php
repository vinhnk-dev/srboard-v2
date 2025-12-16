<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

function render_title($title, $href = "", $class = "")
{
    return '<a href="' . $href . '" ><h6 class="text-title ' . $class . ' ">' . $title . '</h6></a>';
}

function render_url($title, $href = "", $class = "")
{
    return '<a href="' . $href . '" target="_blank"><p class="' . $class . ' ">' . $title . '</p></a>';
}

function render_date($datetime, $class = "")
{
    return '<div class="' . $class . '"> ' . \Carbon\Carbon::parse($datetime)->format('m/d/Y') . ' </div>';
}

function render_date_custom($datetime, $format, $class = "")
{
    return '<p class="' . $class . ' alert-due ml-auto"> <em class="icon ni ni-clock" style="font-size: 20px"></em> ' . \Carbon\Carbon::parse($datetime)->format($format) . ' </p>';
}

function render_datetime($datetime, $class = "")
{
    return '<div class="' . $class . '"> ' . \Carbon\Carbon::parse($datetime)->format('m/d/Y h:i A') . ' </div>';
}

function render_yesno($bool, $class = "")
{
    return '<div class="' . $class . '"> ' . ($bool ? 'Yes' : 'No') . ' </div>';
}

function render_color($text, $color, $class = "")
{
    return '<div class="rounded-pill text-center bconvert color-cell ' . $class . '" 
                    style="color: ' . $color . '">' . $text . '</div>';
}

function render_stringList($string, $class = "")
{
    return '<a class="d-block text-truncate ' . $class . '" role="button" data-toggle="tooltip" title="' . $string . '">' .
        $string . '</a>';
}

function render_tooltip($text, $tooltip, $class = "")
{
    return '<a class="d-block text-truncate ' . $class . '" role="button" data-toggle="tooltip" title="' . $tooltip . '">' .
        $text . '</a>';
}

function render_pictures($pictures, $id = 0, $class = "")
{
    $images = '';
    foreach ($pictures as $image) {
        if (is_string($image->picture_url) && Str::endsWith($image->picture_url, ['.jpg', '.jpeg', '.png', '.gif'])) {
            $images .= "<a href='/$image->picture_url' class='" . ($images != "" ? "d-none" : "") . "'>";
            $images .= "<img src='/$image->picture_url' class='img-thumbnail w-max-100px h-max-100px'>";
            $images .= "</a>";
        }
    }
    return '<div class="show-images ' . $class . '" id="show-images-' . $id . '">' . $images . '</div>';
}

function render_countList($total, $list = "", $class = "")
{
    return '<a role="button" data-toggle="tooltip" title="' . $list . '" class="' . $class . '">' .
        $total . '</a>';
}

function config_table($table)
{
    $tables = [
        'Issue' => [
            'project_code' => 'PRJ ID',
            'picture_url' => 'Image',
            'title' => 'Title',
            'status_name' => 'Status',
            'authorname' => 'Author',
            'assigned' => 'Assign',
            'created_at' => 'Created at',
            'due_date' => 'Due Date',
        ],
        'Group' => [
            'id' => 'No.',
            'group_name' => 'Group name',
            'users_cnt' => 'Member',
            'project_cnt' => 'Project',
            'created_at' => 'Created at'
        ],
        'User' => [
            'username' => 'Username',
            'name' => 'Fullname',
            'email' => 'Email',
            'group_user' => 'Group',
            'active' => 'Status',
            'roles_name' => 'Role'
        ],
        'Project' => [
            'id' => 'No.',
            'project_name' => 'Project name',
            'group_assign' => 'Assign',
            'active' => 'Status',
            'url' => 'URL',
            'name' => 'Author',
            'created_at' => 'Created at'
        ],
        'Status' => [
            'id' => 'No.',
            'status_name' => 'Status Name',
            'color' => 'Font Color',
            'is_check_due' => 'Check Due',
            'created_at' => 'Created at'
        ]
    ];
    return isset($tables[$table]) ? $tables[$table] : [];
}

function send_mail($user_id, $title, $content)
{
    return true;
    $url = env("APP_URL").'/api/sendmail';
    // $cmd = "curl ".$url." -L -X POST -H 'Content-Type: application/json' -u 'intube:Intube!234' ";
    // $cmd.= " -d '" . json_encode(['email'=>$email,'title'=>$title,'content'=>$content]) . "' ";
    // if (strpos($url, "https://") != false) $cmd.= "'  --insecure ";
    // $cmd .= " > /dev/null 2>&1 & ";
    // exec($cmd);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, 'intube:Intube!234');
    curl_setopt($curl, CURLOPT_TIMEOUT_MS, 1);
    curl_setopt($curl, CURLOPT_HEADER, 'Content-Type:application/json');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 1); 
    curl_setopt($curl, CURLOPT_USERAGENT, 'api');

    curl_setopt ($curl, CURLOPT_POSTFIELDS, ['email'=>$user_id,'title'=>$title,'content'=>$content]); 
    curl_exec($curl);
    curl_close($curl);

    return true;
}

function save_upload_file($file, $path = ""){
    $get_name_image = $file->getClientOriginalName();
    $name_image = pathinfo($get_name_image, PATHINFO_FILENAME);
    $extension = $file->getClientOriginalExtension();
    $new_image = $name_image . "." . $extension;
    if (file_exists($path . $new_image)) {
        $count = 0;
        while (file_exists($path . $new_image)) {
            $count++;
            $new_image = $name_image . "(" . $count . ")." . $extension;
        }
    }
    $file->move($path, $new_image);
    return $path . $new_image;
}