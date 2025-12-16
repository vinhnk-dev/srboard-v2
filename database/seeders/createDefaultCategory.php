<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BoardCategory;

class createDefaultCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BoardCategory::create([
            'id' => 0,
            'category' => 'itnononnone'
        ]);

    }
}
