<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\StockAdjustment;
use App\Models\ProductReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:sales,purchases,adjustments,returns',
            'format' => 'required|in:html,excel',
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $type = $request->type;

        if ($type === 'sales') {
            $data = Order::with('customer')
                ->whereBetween('order_date', [$start, $end])
                ->latest()
                ->get();
        } elseif ($type === 'purchases') {
            $data = Purchase::with('supplier')
                ->whereBetween('purchase_date', [$start, $end])
                ->latest()
                ->get();
        } elseif ($type === 'adjustments') {
            $data = StockAdjustment::with(['product', 'user'])
                ->whereBetween('created_at', [$start, $end])
                ->latest()
                ->get();
        } else {
            $data = ProductReturn::with('product')
                ->whereBetween('created_at', [$start, $end])
                ->latest()
                ->get();
        }

        if ($request->format === 'excel') {
            return $this->exportToExcel($data, $type, $start, $end);
        }

        return view('reports.print', [
            'data' => $data,
            'type' => $type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
    }

    private function exportToExcel($data, $type, $start, $end)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title Block
        $sheet->setCellValue('A1', 'BONDOO RETAIL REPORT: ' . strtoupper($type));
        $sheet->setCellValue('A2', 'Period: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'));
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        if ($type === 'sales') {
            $headers = ['No.', 'Invoice No', 'Date', 'Customer', 'Payment Type', 'Sub Total', 'VAT', 'Total', 'Pay Amount', 'Due Amount'];
            $sheet->fromArray($headers, NULL, 'A4');
            $rowNum = 5;
            foreach ($data as $idx => $row) {
                $sheet->setCellValue('A' . $rowNum, $idx + 1);
                $sheet->setCellValue('B' . $rowNum, $row->invoice_no);
                $sheet->setCellValue('C' . $rowNum, $row->order_date->format('d-m-Y H:i'));
                $sheet->setCellValue('D' . $rowNum, $row->customer->name ?? 'Walk-in');
                $sheet->setCellValue('E' . $rowNum, $row->payment_type);
                $sheet->setCellValue('F' . $rowNum, $row->sub_total);
                $sheet->setCellValue('G' . $rowNum, $row->vat);
                $sheet->setCellValue('H' . $rowNum, $row->total);
                $sheet->setCellValue('I' . $rowNum, $row->pay_amount);
                $sheet->setCellValue('J' . $rowNum, $row->due_amount);
                $rowNum++;
            }
        } elseif ($type === 'purchases') {
            $headers = ['No.', 'PO Number', 'Date', 'Supplier', 'Status', 'Sub Total', 'Total', 'Pay Amount', 'Due Amount (Debt)'];
            $sheet->fromArray($headers, NULL, 'A4');
            $rowNum = 5;
            foreach ($data as $idx => $row) {
                $sheet->setCellValue('A' . $rowNum, $idx + 1);
                $sheet->setCellValue('B' . $rowNum, $row->purchase_no);
                $sheet->setCellValue('C' . $rowNum, $row->purchase_date->format('d-m-Y'));
                $sheet->setCellValue('D' . $rowNum, $row->supplier->name);
                $sheet->setCellValue('E' . $rowNum, $row->purchase_status);
                $sheet->setCellValue('F' . $rowNum, $row->sub_total);
                $sheet->setCellValue('G' . $rowNum, $row->total);
                $sheet->setCellValue('H' . $rowNum, $row->pay_amount);
                $sheet->setCellValue('I' . $rowNum, $row->due_amount);
                $rowNum++;
            }
        } elseif ($type === 'adjustments') {
            $headers = ['No.', 'Product Code', 'Product Name', 'Adjustment Type', 'Quantity', 'Reason', 'Adjusted By', 'Date'];
            $sheet->fromArray($headers, NULL, 'A4');
            $rowNum = 5;
            foreach ($data as $idx => $row) {
                $sheet->setCellValue('A' . $rowNum, $idx + 1);
                $sheet->setCellValue('B' . $rowNum, $row->product->code);
                $sheet->setCellValue('C' . $rowNum, $row->product->name);
                $sheet->setCellValue('D' . $rowNum, $row->type);
                $sheet->setCellValue('E' . $rowNum, $row->quantity);
                $sheet->setCellValue('F' . $rowNum, $row->reason);
                $sheet->setCellValue('G' . $rowNum, $row->user->name ?? 'System');
                $sheet->setCellValue('H' . $rowNum, $row->created_at->format('d-m-Y H:i'));
                $rowNum++;
            }
        } else {
            $headers = ['No.', 'Return Type', 'Reference No', 'Product Code', 'Product Name', 'Quantity', 'Reason', 'Refund Amount', 'Date'];
            $sheet->fromArray($headers, NULL, 'A4');
            $rowNum = 5;
            foreach ($data as $idx => $row) {
                $sheet->setCellValue('A' . $rowNum, $idx + 1);
                $sheet->setCellValue('B' . $rowNum, $row->type);
                $sheet->setCellValue('C' . $rowNum, $row->reference_no);
                $sheet->setCellValue('D' . $rowNum, $row->product->code);
                $sheet->setCellValue('E' . $rowNum, $row->product->name);
                $sheet->setCellValue('F' . $rowNum, $row->quantity);
                $sheet->setCellValue('G' . $rowNum, $row->reason);
                $sheet->setCellValue('H' . $rowNum, $row->refund_amount);
                $sheet->setCellValue('I' . $rowNum, $row->created_at->format('d-m-Y H:i'));
                $rowNum++;
            }
        }

        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Report_' . $type . '_' . $start->format('Ymd') . '_to_' . $end->format('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
