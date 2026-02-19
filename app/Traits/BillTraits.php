<?php
namespace App\Traits;

use App\Models\ProductSale;
use App\Models\DailyRoomRecord;
use App\Models\ExtraServiceSale;

trait BillTraits
{
    public function getBillRooms($ids, int $buy = 1, int $free = 1)
    {
        $dailyRooms = DailyRoomRecord::whereIn('id', $ids)
            ->with(['room', 'therapistType'])
            ->get();


            $grouped = $dailyRooms->groupBy(fn ($item) =>
                $item->room_id . '-' . $item->service_type
            );
// 180 = 3
        $items = [];

        foreach ($grouped as $group) {

            // dump($group->first()->toArray());

            $first = $group->first();

            $totalSlots = $first->total_time;


            $setSize   = $buy + $free; //1 + 1 = 2
            $fullSets  = intdiv($totalSlots, $setSize); // 1
            $remainder = $totalSlots % $setSize; // 3 % 2 = 1

            $paidSlots = ($fullSets * $buy) + min($remainder, $buy); //  (1 * 1) + 1 = 2

            $freeSlots = $totalSlots - $paidSlots;  // 3 - 2 = 1

            $price = $first->room_price + $first->service_type_price;  // 10000 + 5000 = 15000
            $therapistTypeTitle = optional($first->therapistType)->title;

            $therapistPrice = $first->therapist->price;

            // Paid item
            $items[] = [
                'branch_id' => 1,
                'room_id'   => $first->room_id,
                'room_name' => "{$first->room->name} – {$therapistTypeTitle}",
                'service_type' => $first->therapistType,
                'service_type_price' => $first->service_type_price,
                'quantity'  => $paidSlots,
                'start_time' => $first->start_time,
                'end_time' => $first->end_time,
                'total_time' => $first->total_time - $freeSlots,
                'unit_price'=> $price,
                'therapist_price' => $therapistPrice,
                'total_price' => ($paidSlots * $price ) + ($paidSlots * $therapistPrice) ,  // 2 * 15000 = 30000
            ];

            // Free item
            if ($freeSlots > 0) {
                $items[] = [
                    'branch_id' => 1,
                    'room_id'   => $first->room_id,
                    'room_name' => "{$first->room->name} – {$therapistTypeTitle} (Free)",
                    'service_type' => $first->therapistType,
                    'service_type_price' => 0,
                    'quantity'  => $freeSlots, // 1
                    'start_time' => $first->start_time,
                    'end_time' => $first->end_time,
                    'total_time' => $freeSlots, // 1
                    'unit_price'=> 0,
                    'therapist_price' => $therapistPrice,
                    'total_price' => $freeSlots * $first->therapist->price,
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

    public function getBillExtraServices($roomIds)
    {
        return ExtraServiceSale::whereIn('daily_room_record_id', $roomIds)->with('extraService')->get();
    }


    public function totalBill($getBillItems, $getBillRooms, $getBillExtraServices) {
        $total = 0;
        foreach ($getBillItems as $item) {
            $total += $item->quantity * $item->unit_price;
        }

        foreach ($getBillRooms as $billRoom) {
            $total += $billRoom['total_price'];
        }

        foreach ($getBillExtraServices as $extraService) {
            $total +=  $extraService->total_price;
        }

        return $total;
    }

}
