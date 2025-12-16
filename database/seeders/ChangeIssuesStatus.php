<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ChangeIssuesStatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $update_issue = DB::table('issues')
        ->where('status','=',106)
        ->update(['status' => 22]);
    }
}
