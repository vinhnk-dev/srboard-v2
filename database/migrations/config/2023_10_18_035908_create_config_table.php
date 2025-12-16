<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('project_code');
            $table->string('url');
            $table->string('git_url');
            $table->string('description');
            $table->string('project_type');
            $table->tinyInteger('active');
            $table->timestamps();
            $table->foreignId('user_id');
        });
        //issues
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('issue_description');
            $table->string('url');
            $table->unsignedBigInteger('status')->default(0);
            $table->string('due_date');
            $table->integer('order_by');
            $table->foreignId('project_id');
            $table->foreignId('user_id');
            $table->timestamps();
        });
        //user assignments
        Schema::create('user_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id');
            $table->foreignId('user_id');
            $table->timestamps();
        });
        //groups
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->foreignId('user_id');
            $table->timestamps();
        });
        //user groups
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('group_id');
            $table->timestamps();
        });
        //groups assignment
        Schema::create('group_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('group_id');
            $table->timestamps();
        });
        //statuses
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name');
            $table->string('color');
            $table->boolean('is_check_due');
            $table->timestamps();
        });
        //project status
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->unsignedBigInteger('status_id');
            $table->boolean('show')->default(false);
            $table->timestamps();
        });
        //issue pictures
        Schema::create('issue_pictures', function (Blueprint $table) {
            $table->id();
            $table->string('picture_url')->nullable();
            $table->foreignId('issue_id');
            $table->timestamps();
        });
        //comment
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->longText('comment');
            $table->string('image')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('issue_id');
            $table->timestamps();
        });
        //boards
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('isused')->default(0);
            $table->string('title');
            $table->longText('board_content');
            $table->foreignId('user_id');
            $table->string('url');
            $table->unsignedBigInteger('board_type_id');
            $table->unsignedBigInteger('board_category_id')->default(0);
            $table->timestamps();
        });
        //board comments
        Schema::create('board_comments', function (Blueprint $table) {
            $table->id();
            $table->longText('comment');
            $table->foreignId('user_id');
            $table->foreignId('board_id');
            $table->timestamps();
        });
        //board files
        Schema::create('board_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_url');
            $table->foreignId('board_id');
            $table->timestamps();
        });
        //board cate
        Schema::create('board_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->timestamps();
        });
        //board types
        Schema::create('board_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name');
            $table->timestamps();
        });
        //board type cate
        Schema::create('board_type_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_category_id');
            $table->unsignedBigInteger('board_type_id');
            $table->timestamps();
        });
        //assign reporter
        Schema::create('assign_reporters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('issue_id');
            $table->timestamps();
        });
        //issue histories
        Schema::create('issue_histories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('issue_description');
            $table->string('url');
            $table->string('due_date');
            $table->unsignedBigInteger('status');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('issue_id');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE user_assignments MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');

        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('val');
            $table->foreignId('user_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config');
    }
}
