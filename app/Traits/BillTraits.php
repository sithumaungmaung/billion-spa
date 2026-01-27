<?php
namespace App\Traits;

use App\Models\DailyRoomRecord;

trait BillTraits
{
    public function getBillRooms($roomIds, $onePlusone)
    {
        $dailyRooms = DailyRoomRecord::whereIn('id', $roomIds)->with('room')->get();
        
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
}