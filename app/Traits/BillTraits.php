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
            ->with(['room', 'serviceType'])
            ->get();

        $grouped = $dailyRooms->groupBy(fn ($item) =>
            $item->room_id . '-' . $item->service_type
        );

        $items = [];

        foreach ($grouped as $group) {

            $first = $group->first();
            $totalSlots = $group->count();

            $setSize   = $buy + $free;
            $fullSets  = intdiv($totalSlots, $setSize);
            $remainder = $totalSlots % $setSize;

            $paidSlots = ($fullSets * $buy) + min($remainder, $buy);
            $freeSlots = $totalSlots - $paidSlots;

            $price = $first->price + $first->service_type_price;
            $serviceTypeTitle = optional($first->serviceType)->title;

            // Paid item
            $items[] = [
                'branch_id' => 1,
                'room_id'   => $first->room_id,
                'room_name' => "{$first->room->name} – {$serviceTypeTitle}",
                'service_type' => $first->serviceType,
                'service_type_price' => $first->service_type_price,
                'quantity'  => $paidSlots,
                'unit_price'=> $price,
                'total_price' => $paidSlots * $price,
            ];

            // Free item
            if ($freeSlots > 0) {
                $items[] = [
                    'branch_id' => 1,
                    'room_id'   => $first->room_id,
                    'room_name' => "{$first->room->name} – {$serviceTypeTitle} (Free)",
                    'service_type' => $first->serviceType,
                    'service_type_price' => 0,
                    'quantity'  => $freeSlots,
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
            $total +=  $extraService->unit_price;
        }

        return $total;
    }

}