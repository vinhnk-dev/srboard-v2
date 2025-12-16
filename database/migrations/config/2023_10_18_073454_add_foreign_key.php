<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreign('status')->references('id')->on('statuses');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('user_assignments', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('user_groups', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('group_assignments', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('project_statuses', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('issue_pictures', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('issue_id')->references('id')->on('issues');
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->foreign('board_type_id')->references('id')->on('board_types');
            $table->foreign('board_category_id')->references('id')->on('board_categories');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('board_files', function (Blueprint $table) {
            $table->foreign('board_id')->references('id')->on('boards');
        });

        Schema::table('board_comments', function (Blueprint $table) {
            $table->foreign('board_id')->references('id')->on('boards');
        });

        Schema::table('board_type_categories', function (Blueprint $table) {
            $table->foreign('board_category_id')->references('id')->on('board_categories');
            $table->foreign('board_type_id')->references('id')->on('board_types');
        });

        Schema::table('assign_reporters', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('issue_id')->references('id')->on('issues');
        });

        Schema::table('issue_histories', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('status')->references('id')->on('project_statuses');
        });

        // Thêm ràng buộc cho các bảng khác nếu cần
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
