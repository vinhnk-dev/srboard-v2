<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'board_categories', 'boards', 'board_types', 'board_type_category',
            'groups',
            'issues',
            'statuses'
        ];
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }
    //command : php artisan migrate:refresh --path=/database/migrations/2023_08_25_110225_add_deleted_at_to_users_table.php
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'board_categories', 'boards', 'board_types', 'board_type_category',
            'groups',
            'issues',
            'statuses'
        ];
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
