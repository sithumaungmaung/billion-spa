<?php
namespace App\Traits;

use App\Models\ProductSale;
use App\Models\DailyRoomRecord;

trait BillTraits
{
    public function getBillRooms($ids, $onePlusone)
    {

        $dailyRooms = DailyRoomRecord::whereIn('id', $ids)->with('room')->get();

        $grouped = $dailyRooms->groupBy(fn ($item) =>
            $item->room_id . '-' . $item->service_type
        );

        $items = [];

        foreach ($grouped as $group) {
            $first = $group->first();

            $totalSlots = $group->count();
            $paidSlots  = (int) ceil($totalSlots / $onePlusone);
            $freeSlots  = $totalSlots - $paidSlots;

            $price = $first->price + $first->service_type_price;

            $serviceType =  $first->serviceType ?  $first->serviceType->title : "";

            $items[] = [
                'branch_id' => 1,
                'room_id'     => $first->room_id,
                'service_type' =>  $first->serviceType,
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

    public function getBillItems($roomIds)
    {
        $billItems = ProductSale::whereIn('daily_room_record_id', $roomIds)->with('product')->get();
        return $billItems;
    }


    public function totalBill($getBillItems, $getBillRooms) {
        $total = 0;
        foreach ($getBillItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }

        foreach ($getBillRooms as $billRoom) {
            $total += $billRoom['total_price'];
        }

        return $total;
    }

}