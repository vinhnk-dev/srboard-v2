<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class setIDforbc extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bc = DB::table('board_categories')
        ->where('board_categories.category' , "=" ,"itnononnone")
        ->update(['id' => 0]);
    }
}
