<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $projectIds = DB::table('project_statuses')
            ->select('project_id')
            ->groupBy('project_id')
            ->get();

        foreach ($projectIds as $projectId) {
            $idsToUpdate = DB::table('project_statuses')
                ->where('project_id', $projectId->project_id)
                ->limit(2)
                ->pluck('id');

            DB::table('project_statuses')
                ->whereIn('id', $idsToUpdate)
                ->update(['show' => true]);
        }
    }
}
