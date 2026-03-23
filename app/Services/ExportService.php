<?php

namespace App\Services;

use App\Exports\Invoices\InvoiceExport;
use App\Models\ExcelExportList;
use App\Models\Invoice;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{

    private $branchId;

    public function exportInvoice($startDate, $endDate){

            $invoices = Invoice::whereBetween('invoice_datetime', [$startDate, $endDate. ' 23:59:59'])
                        ->with('invoiceRooms', 'invoiceProducts', 'invoiceExtraServices', 'user', 'dailyRoomRecord')
                        ->get();

            if($invoices->count() !== 0) {
                $this->branchId = $invoices[0]->branch_id;
            }

            $details = [];

          foreach($invoices as $invoice) {

          // =========== Rooms
            if($invoice->invoiceRooms->count() != 0) {
                foreach ($invoice->invoiceRooms as $room) {
                    $details[] = [
                        'invoice_no'      => $invoice->invoice_no,
                        'title'           => $room->room_name,
                        'unit_price'      => $room->unit_price,
                        'unit'            => $room->quantity,
                        'discount_per_unit' => null,
                        'therapist_price' => $invoice->dailyRoomRecord->therapist->price ?? 0,
                        'total'           => ($room->unit_price * $room->quantity) + ($invoice->dailyRoomRecord->therapist->price ?? 0),
                    ];
                }
            }

            // =========== Invoce Product

            if($invoice->invoiceProducts->count() != 0) {
                foreach ($invoice->invoiceProducts as $product) {
                    $details[] = [
                        'invoice_no' => $invoice->invoice_no,
                        'title' => $product->product_name,
                        'unit_price' => $product->unit_price,
                        'unit' => $product->quantity,
                        'discount_per_unit' => $product->unit_discount,
                        'therapist_price' => null,
                        'total' => $product->total_price,
                    ];
                }
            }

            // ========== Extra Service
            if($invoice->invoiceExtraServices->count() != 0) {
                foreach ($invoice->invoiceExtraServices as $service) {
                    $details[] = [
                        'invoice_no' => $invoice->invoice_no,
                        'title' => $service->extra_service_title,
                        'unit_price' => $service->unit_price,
                        'unit' => $service->quantity,
                        'discount_per_unit' => $service->unit_discount,
                        'therapist_price' => null,
                        'total' => $service->total_price,
                    ];
                }
            }


        }

            $fileName = 'billion-spa-invoice-export' . time() . '.xlsx';

            Excel::store(
                new InvoiceExport($invoices, collect($details)),
                'excel-export/' . $fileName,
                // 'obs'
                'public'
            );

            $this->addExcelExportList('Invoice', $fileName, $startDate, $endDate);
    }


    private function addExcelExportList($title, $fileName, $startDate, $endDate)
    {
        $excelExportList = new ExcelExportList();
        $excelExportList->title = $title;
        $excelExportList->file_name = $fileName;
        $excelExportList->start_date = $startDate;
        $excelExportList->end_date = $endDate;
        $excelExportList->user_id = auth()->user()->id;
        $excelExportList->branch_id = $this->branchId;
        $excelExportList->save();

    }


}