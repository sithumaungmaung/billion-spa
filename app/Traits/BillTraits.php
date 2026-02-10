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

            // dump($dailyRooms->toArray());

            $grouped = $dailyRooms->groupBy(fn ($item) =>
            $item->room_id . '-' . $item->service_type
        );
// 180 = 3
        $items = [];

        foreach ($grouped as $group) {


            $first = $group->first();
            // dump($group->toArray());
            $totalSlots = $first->total_time;
            // dump($first->toArray());

            $setSize   = $buy + $free;
            $fullSets  = intdiv($totalSlots, $setSize);
            $remainder = $totalSlots % $setSize;

            $paidSlots = ($fullSets * $buy) + min($remainder, $buy);
            $freeSlots = $totalSlots - $paidSlots;

            $price = $first->room_price + $first->service_type_price;
            $therapistTypeTitle = optional($first->therapistType)->title;

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
                'total_time' => $first->total_time,
                'unit_price'=> $price,
                'total_price' => $paidSlots * $price,
            ];

            // Free item
            if ($freeSlots > 0) {
                $items[] = [
                    'branch_id' => 1,
                    'room_id'   => $first->room_id,
                    'room_name' => "{$first->room->name} – {$therapistTypeTitle} (Free)",
                    'service_type' => $first->therapistType,
                    'service_type_price' => 0,
                    'quantity'  => $freeSlots,
                    'start_time' => $first->start_time,
                    'end_time' => $first->end_time,
                    'total_time' => $freeSlots,
                    'unit_price'=> 0,
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