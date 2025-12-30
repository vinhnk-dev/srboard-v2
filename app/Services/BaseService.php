<?php
namespace App\Services;

use App\Services\Contracts\BaseServiceInterface;
use App\Repositories\BaseRepositoryInterface;


class BaseService implements BaseServiceInterface
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
   
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id,$data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    public function forcesDelete($id)
    {
        return $this->repository->forcesDelete($id);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function pdf()
    {
        $data = TableView::render_normal($this->repo, $this->context);
        $html = view('Sys.table_pdf',  $data);
        $pdf = new Dompdf();
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->render();
        return $pdf->stream();
    }
    
    public function excel()
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

        $header = $this->service->emptyModal()->getTableHeader();
        $char = 0;
        foreach ($header as $head) {
            $activeWorksheet->setCellValue(chr(65 + $char) . (1), $head);
            $char += 1;
        }


        $r = 2;
        $data = $this->service->search();
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
    
}
