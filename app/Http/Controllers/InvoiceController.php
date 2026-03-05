<?php

namespace App\Http\Controllers;

use App\Models\DailyRoomRecord;
use App\Models\Invoice;
use App\Models\InvoiceRoom;
use App\Services\MpdfService;
use App\Services\RabbitService;
use App\Traits\BillTraits;

class InvoiceController extends Controller
{
    use BillTraits;

    protected $roomIds;

    public function __construct(private readonly MpdfService $pdfService)
    {
    }

    public function previewInvoice($ids, $buy, $free)
    {
        $req = request();

        $disPercentage = $req->discountPercentage;
        $serviceChargePercentage = $req->serviceChargePercentage;
        $disAmount = $req->discountAmount;
        $serviceChargeAmount = $req->serviceChargeAmount;

        $roomIds = array_map('intval', explode(',', $ids));

        $billRooms = $this->getBillRooms($roomIds, $buy, $free);
        $billItems = $this->getBillItems($roomIds);
        $billExtraServices = $this->getBillExtraServices($roomIds);

        $total = $this->totalBill($billItems, $billRooms, $billExtraServices);
        $grandTotal = $total + $serviceChargeAmount - $disAmount;

        $invoice = [
            // 'invoiceDetail' => '',
            'rooms' => $billRooms,

            'items' => $billItems->map(function ($item) {
                $name = RabbitService::uni2zg($item['product']['name']);
                $item['product']['name'] = RabbitService::zg2uni($name);
                return $item;
            }),
            'extraServices' => $billExtraServices->map(function ($service) {
                $name = RabbitService::uni2zg($service->extraService->title);
                $name = RabbitService::zg2uni($name);
                $service['title'] = $name;
                return $service;
                }),
                // 'extraServices' => $billExtraServices->toArray(),
            'total' => $total,
            'grandTotal' => $grandTotal,
            'disPercentage' => $disPercentage,
            'disAmount' => $disAmount,
            'serviceChargePercentage' => $serviceChargePercentage,
            'serviceCharge' => $serviceChargeAmount,
        ];

        $invoice = $this->normalizeMyanmarText($invoice);

        return $this->pdfService->streamView('pdf.bill', compact('invoice'), 'invoice.pdf');
    }

    public function detailPDF($invoice_id)
    {
        $invoiceDetail = Invoice::find($invoice_id);
        $billRooms = InvoiceRoom::where('invoice_id', $invoiceDetail->id)->get();

        $roomIds = DailyRoomRecord::where('invoice_id', $invoiceDetail->id)
            ->pluck('id')
            ->toArray();

        $billItems = $this->getBillItems($roomIds);
        $billExtraServices = $this->getBillExtraServices($roomIds);

        $disPercentage = $invoiceDetail->discount_percent;
        $disAmount = $invoiceDetail->discount_amount;

        $serviceChargePercentage = $invoiceDetail->service_charge_percent;
        $serviceCharge = $invoiceDetail->service_charge;

        $invoice = [
            'invoiceDetail' => $invoiceDetail->toArray(),
            'rooms' => $billRooms->toArray(),
            'items' => $billItems->map(function ($item) {
                $name = RabbitService::uni2zg($item['product']['name']);
                $item['product']['name'] = RabbitService::zg2uni($name);
                return $item;
            }),
            'extraServices' => $billExtraServices->map(function ($service) {
                $name = RabbitService::uni2zg($service->extraService->title);
                $name = RabbitService::zg2uni($name);
                $service['title'] = $name;
                return $service;
            }),
            'total' => $invoiceDetail->sub_total,
            'grandTotal' => $invoiceDetail->grand_total,
            'disPercentage' => $disPercentage,
            'disAmount' => $disAmount,
            'serviceChargePercentage' => $serviceChargePercentage,
            'serviceCharge' => $serviceCharge,
        ];

        $invoice = $this->normalizeMyanmarText($invoice);

        return $this->pdfService->streamView('pdf.preview_invoice', compact('invoice'), 'invoice.pdf');
    }

    private function normalizeMyanmarText(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->normalizeMyanmarText($item);
            }

            return $value;
        }

        if (!is_string($value) || $value === '') {
            return $value;
        }

        $value = RabbitService::zg2uni($value);

        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($value, \Normalizer::FORM_C);
            if ($normalized !== false) {
                $value = $normalized;
            }
        }

        return $value;
    }


    // public static function zawGyi($value)
    // {
    //     return RabbitService::zg2uni($value);
    // }

}
