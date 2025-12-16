<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class autoFillController extends Controller
{
    public function update(Request $request)
    {

        $projects = Project::all();

        foreach($projects as $project)
        {
            $randomCode = $this->generateRandomCode();
            $project->update(['project_code' => $randomCode]);
        }

        return redirect()->route('index');
    }

    private function generateRandomCode()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomCode = '';

        do {
            $randomCode = '';
            for ($i = 0; $i < 2; $i++) {
                $randomCode .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while (Project::where('project_code', $randomCode)->exists());

        return $randomCode;
    }
}
