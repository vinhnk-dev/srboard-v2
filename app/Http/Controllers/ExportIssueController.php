<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportIssueController extends Controller
{

    public function export(Request $request)
    {        
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
            ->leftjoin("project_statuses", "project_statuses.id", "=", "issues.status")
            ->join("statuses","statuses.id","issues.status")
            ->join("users", "users.id", "=", "issues.user_id")
            ->leftjoin("user_assignments", "user_assignments.issue_id", "=", "issues.id")
            ->leftjoin("users as u2", "u2.id", "=", "user_assignments.user_id")
            ->select(
                "users.name as author", 
                DB::raw('GROUP_CONCAT(u2.name) as assignee'), 
                "issues.title", 
                "status_name", 
                "issues.due_date", 
                "issues.created_at", 
                "issues.project_id", 
                "issues.id as issue_id")
            ->groupBy('issues.id')
            ->orderBy('author', 'asc')
            ->get();

        $title = ['Name', 'Assign', 'Title', 'Status', 'Due_date', 'Create_date', 'Project_id'];
        $cols = ['author', 'assignee', 'title', 'status_name', 'due_date', 'created_at', 'project_id'];

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

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="SR_Board.xlsx"');
        $response->send();
    }
}
