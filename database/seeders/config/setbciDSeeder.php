<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class setbciDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bc = DB::table('board_categories')
        ->where('board_categories.category' , "=" ,"itnononnone")
        ->update(['id' => 0]);
    }
}
