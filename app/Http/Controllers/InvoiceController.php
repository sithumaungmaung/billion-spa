<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\PDF;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use App\Models\DailyRoomRecord;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    protected $roomIds;

    public function previewPDF($ids, $onePlusOne) {

        $roomIds = array_map(
            'intval',
            explode(',', $ids)
        );

        dd([
            'roomIds' => $roomIds,
            'onePlusOne' => $onePlusOne
        ]);

        $rooms = DailyRoomRecord::whereIn('id', $roomIds)->with('room')->get();

        $invoice = [
            'rooms' => $rooms,
            'items' => $this->getBillItems($roomIds)->toArray(),
            'total' => $this->totalBill()
        ];


        $pdf = PDF::loadView('invoice', compact('invoice'));
        return $pdf->stream('invoice.pdf');
    }


      public function getBillItems()
    {
        $billItems = ProductSale::whereIn('daily_room_record_id', $roomIds)->with('product')->get();
        return $billItems;
    }

    public function totalBill() {
        $total = 0;
        foreach ($this->billItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }

        foreach ($this->billRooms as $billRoom) {
            $total += $billRoom['total_price'];
        }

        return $total;
    }


    public function getBillRooms($roomIds)
    {
        $dailyRooms = DailyRoomRecord::whereIn('id', $roomIds)->with('room')->get();

        $grouped = $dailyRooms->groupBy(fn ($item) =>
            $item->room_id . '-' . $item->service_type
        );

        $items = [];

        foreach ($grouped as $group) {
            $first = $group->first();

            $totalSlots = $group->count();
            $paidSlots  = (int) ceil($totalSlots / $this->onePlusone);
            $freeSlots  = $totalSlots - $paidSlots;

            $price = $first->price + $first->service_type_price;

            $serviceType = $first->service_type > 0 ? 'By Name' : 'Normal';

            $items[] = [
                'branch_id' => 1,
                'room_id'     => $first->room_id,
                'service_type' =>  $first->service_type,
                'service_type_price' =>  $first->service_type_price,
                'room_name'   => "{$first->room->name} – {$serviceType}",
                'quantity'    => $paidSlots,
                'unit_price'  => $price,
                'total_price' => $paidSlots * $price,
            ];

            if($freeSlots > 0) {
                $items[] = [
                    'branch_id' => 1,
                    'room_id'     => $first->room_id,
                    'room_name'   => "{$first->room->name} – {$serviceType}",
                    'service_type' =>  $first->service_type,
                    'service_type_price' =>  0,
                    'quantity'    => $freeSlots,
                    'unit_price'  => 0,
                    'total_price' => 0,
                ];
            }
        }

        return $items;
    }



}
