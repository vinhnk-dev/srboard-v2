<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BoardType;

class createNewType extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notice = BoardType::create([
            'type_name' => 'Notice'
        ]);
        $faq = BoardType::create([
            'type_name' => 'FAQ'
        ]);
    }
}
