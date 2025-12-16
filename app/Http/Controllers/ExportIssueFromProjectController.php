<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportIssueFromProjectController extends Controller
{

    public function export($projectId)
    {
        $project = Project::find($projectId);
        if ($project == null) {
            return 404;
        }

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $style = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
        ];

        $columns = range('A', 'G');
        foreach ($columns as $column) {
            $cellCoordinate = $column . '1';
            $activeWorksheet->getStyle($cellCoordinate)->applyFromArray($style);
        }

        $task = DB::table("issues")
            ->join("projects", "projects.id", "=", "issues.project_id")
            ->leftjoin("project_statuses", "project_statuses.id", "=", "issues.status")
            ->join("statuses","statuses.id","issues.status")
            ->join("users", "users.id", "=", "issues.user_id")
            ->leftjoin("user_assignments", "user_assignments.issue_id", "=", "issues.id")
            ->leftjoin("users as u2", "u2.id", "=", "user_assignments.user_id")
            ->where("issues.project_id", "=", $projectId)
            ->select(
                DB::raw("CONCAT(projects.project_code, '-', issues.id) as prj_id"), 
                "issues.title", 
                "status_name", 
                "users.name as author", 
                DB::raw('GROUP_CONCAT(u2.name) as assignee'), 
                "issues.created_at", 
                "issues.due_date", 
                "projects.project_name as name")
            ->groupBy('issues.id')
            ->orderBy('issues.id', 'desc')
            ->get();

        $title = ['PRJ_ID', 'Title', 'Status', 'Author', 'Assign', 'Due_date', 'Create_date'];
        $cols = ['prj_id', 'title', 'status_name', 'author', 'assignee', 'due_date', 'created_at'];

        for ($i=0; $i < count($cols); $i++) {
            $activeWorksheet->setCellValue(chr(65+$i).(1), $title[$i]);
        }

        for ($i=1; $i <= count($task); $i++) { 
            for ($j=0; $j < count($cols); $j++) { 
                $colname = $cols[$j];
                $activeWorksheet->setCellValue(chr(65+$j).($i+1), $task[$i-1]->$colname);
            }
        }

        $response = response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        
        $today = now()->format('m-d-Y');
        $projectName = $task->isEmpty() ? 'Project' : $task[0]->name;
        $filename = "{$projectName}_{$today}.xlsx";

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");
        $response->send();
    }
}