<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;
use Illuminate\Support\Facades\DB;


class UpdateStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $statuses = DB::table('project_statuses')->get();

        // foreach($statuses as $st)
        // {
        //     $check = DB::table('statuses')
        //     ->where('status_name','=',$st->status)
        //     ->first();
        //     if(!$check){
        //         $check = Status::create(['status_name' => $st->status]);
        //     }

        // }
        $project_status = DB::table('project_statuses')->get();
        foreach ($project_status as $prj) {
            $update = DB::table('issues')->where('status','=', $prj->id)
                ->where('updated', '=', 0)
                ->update(['status' => $prj->status_id, 'updated' => 1]);

            $issue_history = DB::table('issue_histories')->where('status','=', $prj->id)
            ->where('updated', '=', 0)
            ->update(['status' => $prj->status_id, 'updated' => 1]);;
        }

    }
}
