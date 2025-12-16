<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['id', 'username','name']);
        });
        //projects
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['id', 'project_name', 'user_id']);
        });
         //issues
        Schema::table('issues', function (Blueprint $table) {
            $table->index(['id','status','project_id','user_id']);
        });
        //user assignments
        Schema::table('user_assignments', function (Blueprint $table) {
            $table->index(['issue_id','user_id']);
        });
        //group
        Schema::table('groups', function (Blueprint $table) {
            $table->index(['user_id']);
        });
        // user groups
        Schema::table('user_groups', function (Blueprint $table) {
            $table->index(['user_id','group_id']);
        });
        // group_assignments
        Schema::table('group_assignments', function (Blueprint $table) {
            $table->index(['project_id','group_id']);
        });
        Schema::table('project_statuses', function (Blueprint $table) {
            $table->index(['status_id','project_id']);
        });
        Schema::table('issue_pictures', function (Blueprint $table) {
            $table->index(['issue_id']);
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['user_id', 'issue_id']);
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->index(['board_type_id','title','user_id','board_category_id']);
        });

        Schema::table('board_comments', function (Blueprint $table) {
            $table->index(['board_id','user_id']);
        });

        Schema::table('board_files', function (Blueprint $table) {
            $table->index(['board_id']);
        });

        Schema::table('assign_reporters', function (Blueprint $table) {
            $table->index(['user_id','issue_id']);
        });

        Schema::table('issue_histories', function (Blueprint $table) {
            $table->index(['user_id','issue_id','project_id','status']);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
