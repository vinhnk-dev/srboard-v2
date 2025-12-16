<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;

class WeeklyReportController extends Controller
{
    public function index($userId){


        $now = new DateTime();
        $startDate = $now->sub(new DateInterval('P7D'));
        $endDate = new DateTime();


        // $latestHistories = DB::table('user_assignments')
        //     ->join('users', 'users.id', '=', 'user_assignments.user_id')
        //     ->join('issue_histories', 'issue_histories.issue_id', '=', 'user_assignments.issue_id')
        //     ->where('user_assignments.created_at','>',$startDate->format('Y-m-d H:i:s'))
        //     ->where('user_assignments.created_at','<=',$endDate->format('Y-m-d H:i:s'))
        //     ->where('user_assignments.user_id', $userId)
        //     ->groupBy('user_assignments.issue_id')
        //     ->select(DB::raw('MAX(issue_histories.id) as max_id, user_assignments.user_id')); // Thêm cột user_id

        // $result = DB::table('issue_histories')
        //     ->join('project_statuses', 'project_statuses.id', '=', 'issue_histories.status')
        //     ->joinSub($latestHistories, 'latest_histories', function ($join) {
        //         $join->on('issue_histories.id', '=', 'latest_histories.max_id');
        //     })
        //     ->get();


        // $filteredTasks = [];

            // $comments = DB::table('comments')
            //     ->where('issue_id', $task->issue_id)
            //     ->where('user_id', $userId)
            //     ->Where(function ($query) {
            //         $query->whereRaw("Lower(trim(REPLACE(REPLACE(comment, '&nbsp;', ''), ' ', ''))) = 'ok' ")
            //             ->orWhereRaw("Lower(trim(REPLACE(REPLACE(comment, '&nbsp;', ''), ' ', ''))) like '%>ok<%' ");})
            //     ->get();



        //     if ($comments > 0 ) {
        //         $filteredTasks[] = $task;
        //     }
        // }

        $task = DB::table('issues')
            ->join('comments','comments.issue_id','issues.id')
            ->join('statuses','statuses.id','issues.status')
            ->where('comments.user_id','=',$userId)
            ->where('comments.created_at','>',$startDate->format('Y-m-d H:i:s'))
            ->where('comments.created_at','<=',$endDate->format('Y-m-d H:i:s'))
            ->Where(function ($query) {
                $query->whereRaw("Lower(trim(REPLACE(REPLACE(comment, '&nbsp;', ''), ' ', ''))) = 'ok' ")
                    ->orWhereRaw("Lower(trim(REPLACE(REPLACE(comment, '&nbsp;', ''), ' ', ''))) = '<p>ok</p>' ");})
            ->distinct()
            ->get();

        $user = DB::table('users')
            ->where('id',$userId)
            ->select('id','name','username','avatar','email')
            ->first();



        $data = new \stdClass();

        $data->user = $user;
        $data->tasks = $task;
        // dd($data);
        return response()->json($data);
    }
}
