<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CleannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        DB::transaction(function () {
            $this->deleteUserAssignments();
            $this->deleteUserGroups();
            $this->deleteIssuePictures();
            $this->deleteComments();
        });
        return redirect()->route('index');
    }

    private function deleteUserAssignments()
    {
        DB::table('user_assignments')
            ->leftJoin('issues', 'user_assignments.issue_id', '=', 'issues.id')
            ->whereNull('issues.id')
            ->delete();
    }

    private function deleteUserGroups()
    {
        DB::table('user_groups')
            ->leftJoin('users', 'user_groups.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->delete();
    }

    private function deleteIssuePictures()
    {
        DB::table('issue_pictures')
            ->leftJoin('issues', 'issue_pictures.issue_id', '=', 'issues.id')
            ->whereNull('issues.id')
            ->delete();
    }

    private function deleteComments()
    {
        DB::table('comments')
            ->leftJoin('issues', 'comments.issue_id', '=', 'issues.id')
            ->whereNull('issues.id')
            ->delete();
    }

}
