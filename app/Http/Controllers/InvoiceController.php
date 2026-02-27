<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Invoice;
use App\Traits\BillTraits;
use App\Models\InvoiceRoom;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use App\Models\DailyRoomRecord;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{

    use BillTraits;

    protected $roomIds;
    // protected $total;

    public function previewInvoice($ids, $buy, $free) {

        $req = request();

        $disPercentage = $req->discountPercentage;
        $serviceChargePercentage = $req->serviceChargePercentage;
        $disAmount = $req->discountAmount;
        $serviceChargeAmount = $req->serviceChargeAmount;

        $roomIds = array_map(
            'intval',
            explode(',', $ids)
        );

        $billRooms = $this->getBillRooms($roomIds, $buy, $free);
        $billItems = $this->getBillItems($roomIds);
        $billExtraServices = $this->getBillExtraServices($roomIds);

        $total = $this->totalBill($billItems, $billRooms, $billExtraServices);
        $grandTotal = $total + $serviceChargeAmount - $disAmount;

        $invoice = [
            'items' => $billItems->toArray(),
            'rooms' => $billRooms,
            'extraServices' => $billExtraServices->toArray(),
            'total' => $total,
            'grandTotal' => $grandTotal,
            'disPercentage' => $disPercentage,
            'disAmount' => $disAmount,
            'serviceChargePercentage' => $serviceChargePercentage,
            'serviceChargeAmount' => $serviceChargeAmount
        ];

        $pdf = PDF::loadView('pdf.bill', compact('invoice'))
                ->setPaper([0, 0, 300, 1000], 'portrait');
        return $pdf->stream('invoice.pdf');
    }


    public function detailPDF($invoice_id) {

        $invoiceDetail = Invoice::find($invoice_id);
        $billRooms = InvoiceRoom::where('invoice_id', $invoiceDetail->id)->get();


        $roomIds = DailyRoomRecord::where('invoice_id', $invoiceDetail->id)->pluck('id')->toArray();

        $billItems = $this->getBillItems($roomIds);
        $billExtraServices = $this->getBillExtraServices($roomIds);

        $invoice = [
            'invoiceDetail' => $invoiceDetail,
            'items' => $billItems->toArray(),
            'rooms' => $billRooms->toArray(),
            'extraServices' => $billExtraServices->toArray(),
            'total' => $this->totalBill($billItems, $billRooms, $billExtraServices),
        ];

        // $pdf = PDF::loadView('pdf.preview_invoice', compact('invoice'));
        $pdf = PDF::loadView('pdf.preview_invoice', compact('invoice'))
          ->setPaper([0, 0, 300, 1000], 'portrait');

        return $pdf->stream('invoice.pdf');

    }



}