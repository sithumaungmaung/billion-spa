<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use PDF;
use App\Traits\BillTraits;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use App\Models\DailyRoomRecord;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{

    use BillTraits;

    protected $roomIds;

    public function previewPDF($ids, $onePlusOne) {

        $roomIds = array_map(
            'intval',
            explode(',', $ids)
        );

        $billRooms = $this->getBillRooms($roomIds, $onePlusOne);
        $billItems = $this->getBillItems($roomIds);
        $billExtraServices = $this->getBillExtraServices($roomIds);

        $invoice = [
            'items' => $billItems->toArray(),
            'rooms' => $billRooms,
            'extraServices' => $billExtraServices->toArray(),
            'total' => $this->totalBill($billItems, $billRooms, $billExtraServices),
        ];

        $pdf = PDF::loadView('pdf.bill', compact('invoice'));
        return $pdf->stream('invoice.pdf');
    }





}