<?php

namespace App\Http\Controllers;

use Auth;
use Dompdf\Dompdf;
use PDF;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use App\View\Components\TableView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Exceptions\HttpResponseException;
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $repo;
    protected $userRepo = null;
    protected $context = [];

    public function __construct($repo, $userRepo = null)
    {
        $this->repo = $repo;
        $this->userRepo = $userRepo;
    }

    protected function index()
    {
        $this->defautContext();
        return View::first(
            [$this->repo->getClassName() . '.index', 'Sys.Index'],
            TableView::render_normal($this->repo, $this->context, $this->context['tableview_config'] ?? [])
        );
    }

    protected function customView($customView)
    {
        $this->defautContext();
        return view($customView, $this->context);
    }

    protected function create()
    {
        $this->defautContext();
        if (!isset($this->context['modal'])) {
            $this->context['modal'] = $this->repo->emptyModal();
        }
        return view($this->repo->getClassName() . '.form', $this->context);
    }

    protected function edit($id)
    {
        $this->defautContext();
        if (!isset($this->context['modal'])) {
            $this->context['modal'] = $this->repo->find($id);
        }
        return view($this->repo->getClassName() . '.form', $this->context);
    }

    protected function trash()
    {
        $this->defautContext();
        $this->context['trash'] = true;
        return View::first(
            [$this->repo->getClassName() . '.index', 'Sys.Index'],
            TableView::render_trash($this->repo, $this->context, $this->context['tableview_config'] ?? [])
        );
    }

    protected function delete($id)
    {
        $this->repo->delete($id);
        return redirect()->route($this->repo->getBaseUrl() . ".index");
    }

    public function restore($id)
    {
        $this->repo->restore($id);
        return redirect()->back();
    }

    public function forcesDelete($id)
    {
        if($this->repo->forceDelete($id)) return redirect()->back();
        return redirect()->back()->withErrors(['error' => 'Can not forces deleted, maybe it is being used by another object ! ']);
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate($this->repo->rules());

        $modal = $this->repo->find($request->input('id'));

        if ($modal) {
            $this->repo->update($request->input('id'), $validated_data);
        } else {
            $this->repo->create($validated_data);
        }

        return redirect()->route($this->repo->getBaseUrl() . ".index");
    }

    protected function pdf()
    {
        $data = TableView::render_normal($this->repo, $this->context);
        $html = view('Sys.table_pdf',  $data);
        $pdf = new Dompdf();
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->render();
        return $pdf->stream();
    }

    protected function excel()
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $style = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => false,
            ],
        ];
        $activeWorksheet->getRowDimension(1)->setRowHeight(30);
        $activeWorksheet->getStyle('A1:Z1')->applyFromArray($style);
        $columns = range('A', 'Z');
        foreach ($columns as $column) $activeWorksheet->getColumnDimension($column)->setAutoSize(true);

        $header = $this->repo->emptyModal()->getTableHeader();
        $char = 0;
        foreach ($header as $head) {
            $activeWorksheet->setCellValue(chr(65 + $char) . (1), $head);
            $char += 1;
        }


        $r = 2;
        $data = $this->repo->search();
        foreach ($data['list'] as $row) {
            $char = 0;
            foreach ($header as $k => $v) {
                $activeWorksheet->setCellValue(chr(65 + $char) . ($r), $row->$k);
                $char += 1;
            }
            $r += 1;
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="SR_Board.xlsx"');
        $response->send();
    }

    private function defautContext(){
        if ($this->userRepo != null) {
            $this->context['myProjects'] = $this->userRepo->myProjects(Auth::user()->id);
            $this->context['myMaintenances'] = $this->userRepo->myProjects(Auth::user()->id, "Maintenance");
            $this->context['issueTheme'] = $this->userRepo->getConfig("issue_theme", Auth::user()->id);
        }
        $this->context['parentid'] = request()->parentid;
        $this->context['id'] = request()->id;
        if(!isset($this->context['page_left_tools'])) $this->context['page_left_tools'] = "";
        if(!isset($this->context['hasCardCategory'])) $this->context['hasCardCategory'] = false;
    }
}
